<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

require_once dirname(__DIR__) . '/config/security.php';

start_secure_session();
require_admin();
require_post_request();
require_csrf_token();

require_once __DIR__ . '/db_connect.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// PDF GENERATION
use Mpdf\Mpdf;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to generate member ID card
function generateMemberCard($memberData) {
    // Create member_cards directory if it doesn't exist
    if (!file_exists('member_cards')) {
        mkdir('member_cards', 0777, true);
    }

    // Generate QR Code
    $qrCode = new QrCode('GYMSHARK-MEMBER-' . $memberData['username']);       
    // Create writer
    $writer = new PngWriter();
    
    // Create the QR code result
    $result = $writer->write($qrCode);
    
    // Save QR code
    $qrPath = 'member_cards/qr_' . $memberData['id'] . '.png';
    file_put_contents($qrPath, $result->getString());
    
    // Calculate age from DOB
    $dob = new DateTime($memberData['dob']);
    $today = new DateTime();
    $age = $today->diff($dob)->y;
    
    // Generate HTML for ID card
    $cardHtml = '
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            .id-card {
                width: 350px;
                height: 500px;
                background: linear-gradient(145deg, #7380ec, #5a67d8);
                border-radius: 15px;
                padding: 20px;
                color: white;
                font-family: Arial, sans-serif;
                position: relative;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            }
            
            .logo {
                text-align: center;
                margin-bottom: 15px;
                font-size: 24px;
                font-weight: bold;
            }
            
            .photo-container {
                width: 120px;
                height: 120px;
                margin: 0 auto;
                border-radius: 60px;
                overflow: hidden;
                border: 3px solid white;
            }
            
            .photo-container img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            
            .member-info {
                margin-top: 20px;
                text-align: center;
            }
            
            .member-name {
                font-size: 20px;
                font-weight: bold;
                margin-bottom: 10px;
            }
            
            .info-grid {
                display: grid;
                grid-template-columns: auto auto;
                gap: 10px;
                margin: 15px 0;
                text-align: left;
                padding: 0 20px;
            }
            
            .info-label {
                font-weight: bold;
                color: rgba(255,255,255,0.9);
            }
            
            .qr-code {
                text-align: center;
                margin-top: 15px;
            }
            
            .qr-code img {
                width: 100px;
                height: 100px;
                background: white;
                padding: 5px;
                border-radius: 5px;
            }
            
            .valid-until {
                text-align: center;
                margin-top: 10px;
                font-size: 12px;
                color: rgba(255,255,255,0.9);
            }
        </style>
    </head>
    <body>
        <div class="id-card">
            <div class="logo">
                GYMSHARK FITNESS
            </div>
            
            <div class="photo-container">
                <img src="' . (file_exists($memberData['profile_pic']) ? $memberData['profile_pic'] : 'assets/default_profile.png') . '" alt="Member Photo">
            </div>
            
            <div class="member-info">
                <div class="member-name">' . htmlspecialchars($memberData['FirstName'] . ' ' . $memberData['LastName']) . '</div>
                
                <div class="info-grid">
                    <div class="info-label">Reg No:</div>
                    <div>' . htmlspecialchars($memberData['id']) . '</div>
                    
                    <div class="info-label">Blood Type:</div>
                    <div>' . htmlspecialchars($memberData['blood_type']) . '</div>
                    
                    <div class="info-label">Age:</div>
                    <div>' . $age . ' years</div>
                    
                    <div class="info-label">Membership:</div>
                    <div>' . ucfirst(htmlspecialchars($memberData['membership_type'])) . '</div>
                </div>
                
                <div class="qr-code">
                    <img src="' . $qrPath . '" alt="QR Code">
                </div>
                
                <div class="valid-until">
                    Valid until: ' . date('d-m-Y', strtotime('+' . $memberData['duration'] . ' months')) . '
                </div>
            </div>
        </div>
    </body>
    </html>';
    
    // Generate PDF
    $mpdf = new \Mpdf\Mpdf(['format' => [350, 500]]);
    $mpdf->WriteHTML($cardHtml);
    
    // Save PDF
    $pdfPath = 'member_cards/' . $memberData['id'] . '_id_card.pdf';
    $mpdf->Output($pdfPath, 'F');
    
    return $pdfPath;
}

// Function to send welcome email
function sendWelcomeEmail($memberData) {
    // Generate the member ID card
    $idCardPath = generateMemberCard($memberData);

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'gymsharkuser@gmail.com';
        $mail->Password = 'lixc dcwh gfur ehya'; // Consider using environment variables for security
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('gymsharkuser@gmail.com', 'GymShark Fitness');
        $mail->addAddress($memberData['email'], $memberData['FirstName'] . ' ' . $memberData['LastName']);

        // Attach the membership ID card
        $mail->addAttachment($idCardPath, 'GymShark_Member_ID_Card.pdf');

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to GymShark Fitness, ' . $memberData['FirstName'] . '!';

        // Email body
        $body = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #7380ec; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
                .button {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #7380ec;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                }
                .section {
                    margin-top: 20px;
                    padding: 15px;
                    background-color: #f3f4f6;
                    border-radius: 5px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Welcome to GymShark Fitness!</h1>
                </div>
                <div class="content">
                    <h2>Hello ' . htmlspecialchars($memberData['FirstName']) . ',</h2>
                    <p>Congratulations! You are now a valued member of GymShark Fitness.</p>
                    
                    <h3>Your Membership Details:</h3>
                    <ul>
                        <li><strong>Membership ID:</strong> ' . htmlspecialchars($memberData['id']) . '</li>
                        <li><strong>Plan:</strong> ' . ucfirst(htmlspecialchars($memberData['membership_type'])) . '</li>
                        <li><strong>Valid Until:</strong> ' . date('d-m-Y', strtotime('+' . $memberData['duration'] . ' months')) . '</li>
                    </ul>

                    <div class="section">
                        <h3 style="color: #7380ec;">Your Membership ID Card</h3>
                        <p>We\'ve attached your digital membership ID card to this email. Please:</p>
                        <ul>
                            <li>Download and save it on your phone.</li>
                            <li>Print a copy if needed.</li>
                            <li>Show it at the reception for entry.</li>
                        </ul>
                    </div>

                    <h3>Important Information:</h3>
                    <ul>
                        <li>🕒 <strong>Gym Timings:</strong> 5:00 AM - 11:00 PM</li>
                        <li>📍 <strong>Location:</strong> GymShark Fitness Center, Thane</li>
                        <li>📞 <strong>Contact:</strong> +91 XXXXXXXXXX</li>
                    </ul>

                    <div class="section">
                        <h3 style="color: #7380ec;">Next Steps:</h3>
                        <ol>
                            <li>Visit our gym for an orientation session.</li>
                            <li>Meet your trainer (if applicable).</li>
                            <li>Download our app to track your progress.</li>
                        </ol>
                    </div>

                    <p style="margin-top: 20px;">
                        <a href="https://gymshark.com/member-portal" class="button">Access Member Portal</a>
                    </p>
                    
                    <p>We can’t wait to see you in the gym! Stay fit, stay strong!</p>
                    
                    <p>Best regards,<br>
                    The GymShark Team</p>
                </div>
                <div class="footer">
                    <p>GymShark Fitness<br>
                    Contact: +91 XXXXXXXXXX<br>
                    Email: support@gymshark.com</p>
                </div>
            </div>
        </body>
        </html>';

        $mail->Body = $body;
        $mail->AltBody = strip_tags($body);


        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return false;
    }
}

// Function to save base64 image from camera capture
function saveBase64Image($base64Data, $targetDir) {
    // Check if the directory exists, create if not
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    // Remove header from base64 string if present
    if (strpos($base64Data, ',') !== false) {
        $base64Data = explode(',', $base64Data)[1];
    }
    
    // Decode base64 data
    $imageData = base64_decode($base64Data);
    
    // Generate a unique filename
    $filename = uniqid() . '.png';
    $targetFile = $targetDir . $filename;
    
    // Save the image
    if (file_put_contents($targetFile, $imageData)) {
        return $targetFile;
    }
    
    return false;
}

// Main process
if(isset($_POST['submit'])) {
    // Check for duplicate username **BEFORE INSERTING INTO users**
    $check_sql = "SELECT id FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $_POST['username']);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows > 0) {
        $_SESSION['error_msg'] = "Username already exists. Please choose a different username.";
        header("Location: add_member.php");
        exit();
    }
    
    $check_stmt->close();

    // Handle file upload
    $profile_pic = '';
    if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir) && !mkdir($target_dir, 0777, true)) {
            die("Error: Unable to create uploads directory!");
        }

        $file_extension = strtolower(pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if(move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            $profile_pic = $target_file;
        } else {
            die("Error: File upload failed.");
        }
    }

    // Insert into users table
    $sql = "INSERT INTO users (FirstName, LastName, username, email, number, dob, gender, 
            blood_type, duration, address, profile_pic) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("sssssssssss", 
        $_POST['first_name'], 
        $_POST['last_name'], 
        $_POST['username'], 
        $_POST['email'], 
        $_POST['phone'], 
        $_POST['dob'], 
        $_POST['sex'], 
        $_POST['blood_type'], 
        $_POST['duration'], 
        $_POST['address'], 
        $profile_pic
    );

    if($stmt->execute()) {
        // Get the inserted user ID
        $user_id = $conn->insert_id;

        // Determine plan_id based on membership type
        $plan_id = match ($_POST['membership_type']) {
            "basic" => 1,
            "standard" => 2,
            "premium" => 3,
            default => 0,
        };
// Calculate start_date and end_date
$start_date = date("Y-m-d");

// Extract and sanitize duration from form (default: '1M')
$raw_duration = $_POST['duration'] ?? '1M';

// Extract numeric value (e.g., '1M' -> 1, '3M' -> 3, '1Y' -> 12)
$duration = (int) filter_var($raw_duration, FILTER_SANITIZE_NUMBER_INT);
if ($raw_duration === '1Y') {
    $duration = 12;
}

// Set plan_duration (used for database storage)
$plan_duration = match ($duration) {
    1 => '1M',
    3 => '3M',
    6 => '6M',
    12 => '1Y',
    default => '1M',
};

// Calculate end_date
$end_date = date("Y-m-d", strtotime("+$duration months", strtotime($start_date)));

        
        // Convert "1Y" to 12 months
        if ($raw_duration === '1Y') {
            $duration = 12;
        }
        
        $start_date = date("Y-m-d");
        $end_date = date("Y-m-d", strtotime("+$duration months", strtotime($start_date)));

        $end_date = date("Y-m-d", strtotime("+$duration months", strtotime($start_date)));

        // Convert amount safely
        $amount = isset($_POST['amount']) ? number_format((float)$_POST['amount'], 2, '.', '') : '0.00';

        // Insert into plan_bookings table
        $sql2 = "INSERT INTO plan_bookings (plan_id, user_id, plan_duration, start_date, 
                 end_date, total_cost, status, razorpay_payment_id) 
                 VALUES (?, ?, ?, ?, ?, ?, 'completed', NULL)";

        $stmt2 = $conn->prepare($sql2);
        if (!$stmt2) {
            die("Plan Bookings SQL Error: " . $conn->error);
        }

        $stmt2->bind_param("iisssd", $plan_id, $user_id, $plan_duration, $start_date, $end_date, $amount);
        $sql3 = "SELECT id,FirstName, LastName, username, email,dob, blood_type, profile_pic FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql3);
        $stmt->bind_param("s", $_POST['username']);
        $stmt->execute();
        $result = $stmt->get_result();
        $memberData = $result->fetch_assoc(); // Ensure it's an associative array
        
        
        if (!$memberData) {
            die("Error: No user found with username " . $_POST['username']);
        }
        $memberData['membership_type'] = $_POST['membership_type'] ?? '';
        $memberData['duration'] = $_POST['duration'] ?? '';

        if (!$stmt2->execute()) {
            die("Plan Bookings Execution Error: " . $stmt2->error);
        } else {
            echo "Plan booking inserted successfully!<br>";
        }


        // Send welcome email with ID card
        $emailSent = sendWelcomeEmail($memberData);
        if (!$emailSent) {
            die("Error sending email. Check mail settings.");
        }

        $_SESSION['success_msg'] = "Member registered successfully and welcome email sent with ID card!";
        header("Location: member.php");
        exit();
        
    } else {
        $_SESSION['error_msg'] = "Error registering member: " . $conn->error;
        header("Location: add_member.php");
        exit();
    }
}
