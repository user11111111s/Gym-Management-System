<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

require_once dirname(__DIR__) . '/config/security.php';

start_secure_session();
require_admin();
require_post_request();
require_csrf_token();

require_once __DIR__ . '/db_connect.php';

// Import necessary libraries
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Mpdf\Mpdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Logger function for debugging and tracking
function logMessage($message, $type = 'info', $file = 'app_log.txt') {
    $timestamp = date('Y-m-d H:i:s');
    $formattedMessage = "[$timestamp][$type] $message\n";
    error_log($formattedMessage, 3, $file);
}

// Save profile picture from file upload
function saveProfilePicture($fileData) {
    try {
        $target_dir = "uploads/trainers/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir) && !mkdir($target_dir, 0777, true)) {
            throw new Exception("Failed to create upload directory");
        }
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($fileData['type'], $allowedTypes)) {
            throw new Exception("Invalid file format. Only JPG and PNG allowed.");
        }
        
        // Generate unique filename
        $file_name = time() . "_" . basename($fileData["name"]);
        $target_file = $target_dir . $file_name;
        
        // Move uploaded file
        if (!move_uploaded_file($fileData["tmp_name"], $target_file)) {
            throw new Exception("Failed to save uploaded image");
        }
        
        return $target_file;
    } catch (Exception $e) {
        logMessage("Profile picture save error: " . $e->getMessage(), 'error');
        return null;
    }
}

// Generate QR code for trainer ID
function generateQRCode($trainerId) {
    try {
        $qrDir = 'trainer_cards/';
        
        // Create directory if it doesn't exist
        if (!file_exists($qrDir) && !mkdir($qrDir, 0777, true)) {
            throw new Exception("Failed to create QR code directory");
        }
        
        // Generate QR code data
        $qrCode = new QrCode('GYMSHARK-TRAINER-' . $trainerId);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        
        // Save QR code
        $qrPath = $qrDir . 'qr_' . $trainerId . '.png';
        if (file_put_contents($qrPath, $result->getString()) === false) {
            throw new Exception("Failed to save QR code");
        }
        
        return $qrPath;
    } catch (Exception $e) {
        logMessage("QR code generation error: " . $e->getMessage(), 'error');
        throw $e;
    }
}

// Generate trainer ID card as PDF
function generateTrainerCard($trainerData) {
    try {
        // Generate QR code
        $qrPath = generateQRCode($trainerData['trainer_username']);
        
        // Calculate age from DOB
        $dob = new DateTime($trainerData['dob']);
        $today = new DateTime();
        $age = $today->diff($dob)->y;
        
        // Set profile picture path (use default if none exists)
        $profilePicPath = !empty($trainerData['profile_pic']) && file_exists($trainerData['profile_pic']) 
            ? $trainerData['profile_pic'] 
            : 'assets/default_profile.png';
        
        // Generate HTML for ID card
        $cardHtml = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                .id-card {
                    width: 350px;
                    height: 500px;
                    background: linear-gradient(145deg, #ff5722, #ff9800);
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
                
                .ribbon {
                    position: absolute;
                    top: 20px;
                    right: -15px;
                    padding: 8px 20px;
                    background: #ffffff;
                    color: #ff5722;
                    font-weight: bold;
                    font-size: 14px;
                    border-radius: 3px 0 0 3px;
                    transform: rotate(45deg) translateX(15px);
                    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
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
                
                .trainer-info {
                    margin-top: 20px;
                    text-align: center;
                }
                
                .trainer-name {
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
                
                .specialization {
                    margin: 10px 0;
                    font-weight: bold;
                    font-size: 16px;
                    background: rgba(255,255,255,0.2);
                    padding: 5px 15px;
                    border-radius: 20px;
                    display: inline-block;
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
                <div class="ribbon">TRAINER</div>
                
                <div class="logo">
                    GYMSHARK FITNESS
                </div>
                
                <div class="photo-container">
                    <img src="' . $profilePicPath . '" alt="Trainer Photo">
                </div>
                
                <div class="trainer-info">
                    <div class="trainer-name">' . htmlspecialchars($trainerData['FirstName'] . ' ' . $trainerData['LastName']) . '</div>
                    
                    <div class="specialization">' . ucfirst(htmlspecialchars($trainerData['specialization'])) . ' Specialist</div>
                    
                    <div class="info-grid">
                        <div class="info-label">ID:</div>
                        <div>' . htmlspecialchars($trainerData['trainer_id']) . '</div>
                        
                        <div class="info-label">Blood Type:</div>
                        <div>' . htmlspecialchars($trainerData['blood_type']) . '</div>
                        
                        <div class="info-label">Experience:</div>
                        <div>' . htmlspecialchars($trainerData['experience']) . ' years</div>
                    </div>
                    
                    <div class="qr-code">
                        <img src="' . $qrPath . '" alt="QR Code">
                    </div>
                    
                    <div class="valid-until">
                        Staff ID - GymShark Fitness
                    </div>
                </div>
            </div>
        </body>
        </html>';
        
        // Generate PDF
        $mpdf = new Mpdf(['format' => [350, 500]]);
        $mpdf->WriteHTML($cardHtml);
        
        // Save PDF
        $pdfPath = 'trainer_cards/' . $trainerData['trainer_id'] . '_id_card.pdf';
        $mpdf->Output($pdfPath, 'F');
        
        return $pdfPath;
    } catch (Exception $e) {
        logMessage("ID card generation error: " . $e->getMessage(), 'error');
        throw $e;
    }
}

// Send welcome email with ID card attachment
function sendWelcomeEmail($trainerData) {
    try {
        // Generate ID card
        $idCardPath = generateTrainerCard($trainerData);
        
        // Initialize PHPMailer
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'gymsharkuser@gmail.com';
        $mail->Password = 'lixc dcwh gfur ehya'; // Consider using environment variables
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('gymsharkuser@gmail.com', 'GymShark Fitness');
        $mail->addAddress($trainerData['email'], $trainerData['FirstName'] . ' ' . $trainerData['LastName']);
        
        // Attach ID card
        $mail->addAttachment($idCardPath, 'GymShark_Trainer_ID_Card.pdf');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to the GymShark Fitness Training Team!';
        
        // Email body HTML
        $body = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #ff5722; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
                .button {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #ff5722;
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
                    <h1>Welcome to the Training Team!</h1>
                </div>
                <div class="content">
                    <h2>Hello ' . htmlspecialchars($trainerData['FirstName']) . ',</h2>
                    <p>Welcome to the GymShark Fitness Training Team! We\'re thrilled to have someone with your expertise join our fitness family.</p>
                    
                    <h3>Your Trainer Details:</h3>
                    <ul>
                        <li>Trainer ID: ' . htmlspecialchars($trainerData['trainer_id']) . '</li>
                        <li>Specialization: ' . ucfirst(htmlspecialchars($trainerData['specialization'])) . '</li>
                        <li>Start Date: ' . date('d-m-Y') . '</li>
                    </ul>

                    <div class="section">
                        <h3 style="color: #ff5722;">Your Trainer ID Card</h3>
                        <p>We\'ve attached your digital trainer ID card to this email. Please:</p>
                        <ul>
                            <li>Download and save it on your phone</li>
                            <li>Print a copy to carry with you</li>
                            <li>Wear it visibly when conducting training sessions</li>
                        </ul>
                    </div>

                    <h3>Important Information:</h3>
                    <ul>
                        <li>Trainer Hours: 7:00 AM - 8:00 PM</li>
                        <li>Staff Meeting: Every Monday at 9:00 AM</li>
                        <li>First Aid Kit Locations: Near reception, inside each training room</li>
                        <li>Emergency Procedures: Posted in staff room</li>
                    </ul>

                    <div class="section">
                        <h3 style="color: #ff5722;">Next Steps:</h3>
                        <ol>
                            <li>Complete your onboarding paperwork at HR</li>
                            <li>Pick up your uniform from the admin office</li>
                            <li>Schedule your orientation with the head trainer</li>
                            <li>Set up your trainer profile in our scheduling system</li>
                        </ol>
                    </div>

                    <p style="margin-top: 20px;">
                        <a href="https://gymshark.com/trainer-portal" class="button">Access Trainer Portal</a>
                    </p>
                    
                    <p>We look forward to seeing your expertise in action and the positive impact you\'ll make on our members\' fitness journeys!</p>
                    
                    <p>Best regards,<br>
                    The GymShark Management Team</p>
                </div>
                <div class="footer">
                    <p>GymShark Fitness<br>
                    Contact: +91 XXXXXXXXXX<br>
                    Email: staff@gymshark.com</p>
                </div>
            </div>
        </body>
        </html>';

        $mail->Body = $body;
        $mail->AltBody = strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        logMessage("Email sending failed: " . $e->getMessage(), 'error');
        return false;
    }
}

// Main processing code
if (isset($_POST['submit'])) {
    try {
        logMessage("Form submission received");
        
        // Validate username uniqueness
        $trainer_username = $_POST['trainer_username'];
        $checkQuery = "SELECT trainer_id FROM trainers WHERE trainer_username = ?";
        $stmt = $conn->prepare($checkQuery);
        if (!$stmt) {
            throw new Exception("Failed to prepare username check query: " . $conn->error);
        }

        $stmt->bind_param("s", $trainer_username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $_SESSION['error_msg'] = "Trainer username already exists! Please choose another.";
            header("Location: addtrainer.php");
            exit();
        }
        $stmt->close();

        // Default password (hashed)
        $default_password = password_hash("12345678", PASSWORD_BCRYPT);
        
        // Handle profile picture upload
        $profile_pic = null;
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            $profile_pic = saveProfilePicture($_FILES['profile_pic']);
        }
        
        // Collect trainer data
        $trainerData = [
            'trainer_username' => trim($_POST['trainer_username']),
            'FirstName' => trim($_POST['FirstName']),
            'LastName' => trim($_POST['LastName']),
            'specialization' => $_POST['specialization'],
            'experience' => (int)($_POST['experience'] ?? 0),
            'hourly_rate' => (float)($_POST['hourly_rate'] ?? 0),
            'number' => trim($_POST['number']),
            'email' => trim($_POST['email']),
            'address' => trim($_POST['address']),
            'dob' => $_POST['dob'],
            'blood_type' => $_POST['blood_type'] ?? 'Unknown',
            'profile_pic' => $profile_pic,
            'password_hash' => $default_password
        ];
        
        // Validate required fields
        $requiredFields = ['trainer_username','FirstName', 'LastName', 'email', 'number', 'dob', 'specialization', 'address'];
        foreach ($requiredFields as $field) {
            if (empty($trainerData[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }
        
        // Basic email validation
        if (!filter_var($trainerData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        
        // Insert into trainers table
        $sql = "INSERT INTO trainers (trainer_username,FirstName, LastName, email, number, dob, blood_type, 
                specialization, experience, hourly_rate, address, join_date, profile_pic, password_hash) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare trainer insert query: " . $conn->error);
        }

        $stmt->bind_param(
            "ssssssssidsss",
            $trainerData['trainer_username'],
            $trainerData['FirstName'],
            $trainerData['LastName'],
            $trainerData['email'],
            $trainerData['number'],
            $trainerData['dob'],
            $trainerData['blood_type'],
            $trainerData['specialization'],
            $trainerData['experience'],
            $trainerData['hourly_rate'],
            $trainerData['address'],
            $trainerData['profile_pic'],
            $trainerData['password_hash']
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to insert trainer data: " . $stmt->error);
        }
        
        // Get the newly created trainer ID
        $trainer_id = $stmt->insert_id;
        $trainerData['trainer_id'] = $trainer_id;
        logMessage("Trainer inserted with ID: $trainer_id");
        
        // Handle availability schedule
        if (isset($_POST['availability_type']) && $_POST['availability_type'] === 'custom' && isset($_POST['days'])) {
            foreach ($_POST['days'] as $day) {
                // Sanitize inputs
                $time_from = filter_var($_POST[$day . '_start'] ?? '', FILTER_SANITIZE_STRING);
                $time_to = filter_var($_POST[$day . '_end'] ?? '', FILTER_SANITIZE_STRING);
                
                // Validate times
                if (!empty($time_from) && !empty($time_to) && $time_from < $time_to) {
                    $sql_availability = "INSERT INTO trainer_availability (trainer_id, day_of_week, time_from, time_to) 
                                         VALUES (?, ?, ?, ?)";
                    $stmt_avail = $conn->prepare($sql_availability);
                    
                    if (!$stmt_avail) {
                        logMessage("Failed to prepare availability query: " . $conn->error, 'warning');
                        continue;
                    }
                    
                    $stmt_avail->bind_param("isss", $trainer_id, $day, $time_from, $time_to);
                    
                    if (!$stmt_avail->execute()) {
                        logMessage("Failed to insert availability for $day: " . $stmt_avail->error, 'warning');
                    }
                    
                    $stmt_avail->close();
                } else {
                    logMessage("Skipping invalid time range for $day: $time_from - $time_to", 'warning');
                }
            }
        }
        
        // Send welcome email with ID card
        if (sendWelcomeEmail($trainerData)) {
            $_SESSION['success_msg'] = "Trainer registered successfully. Welcome email sent with ID card!";
        } else {
            $_SESSION['warning_msg'] = "Trainer registered successfully, but email sending failed.";
        }
        
        // Redirect to trainer list
        header("Location: trainer.php");
        exit();
        
    } catch (Exception $e) {
        logMessage("Registration error: " . $e->getMessage(), 'error');
        $_SESSION['error_msg'] = "Error: " . $e->getMessage();
        header("Location: addtrainer.php");
        exit();
    }
}
?>