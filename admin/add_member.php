<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/security.php';

require_admin();

$csrfToken = csrf_token();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Member - GymShark</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@48,400,0,0" />
    <link rel="stylesheet" href="styles.css">
    <style>
        .form-container {
            background: var(--clr-white);
            padding: var(--card-padding);
            border-radius: var(--card-border-radius);
            margin-top: 2rem;
            box-shadow: var(--box-shadow);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--clr-dark);
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--clr-info-light);
            border-radius: var(--border-radius-1);
            background: transparent;
            color: var(--clr-dark);
        }

        .form-group input[type="file"] {
            border: none;
            padding: 0;
        }

        .btn-container {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius-1);
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--clr-primary);
            color: var(--clr-white);
        }

        .btn-secondary {
            background: var(--clr-success);
            color: var(--clr-white);
        }

        .btn:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }

        @media screen and (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside>
           <div class="top">
             <div class="logo">
               <h2>GYM</h2><h2><span class="danger">SHARK</span> </h2>
             </div>
             <div class="close" id="close_btn">
              <span class="material-symbols-sharp">
                close
                </span>
             </div>
           </div>
           <!-- end top -->
           <div class="sidebar">
             <a href="index.php">  
                <span class="material-symbols-sharp">grid_view </span>
                <h3>Dashboard</h3>
             </a>
             <a href="member.php">
                <span class="material-symbols-sharp">person_outline </span>
               <h3>Members</h3>
                <span class="msg_count">69</span>
             </a>
             <a href="add_member.php" class="active"> >
                      <span class="material-symbols-sharp">person_add</span>
                      <h3>Add Member</h3>
                  </a>
                  <a href="addtrainer.php" >
                      <span class="material-symbols-sharp">person_add</span>
                      <h3>Add Trainer</h3>
                  </a>
             
             <a href="scanner.php" >
                <span class="material-symbols-sharp">qr_code_scanner</span>
                <h3>Attendance</h3>
             </a>
             <a href="growth.php">
                <span class="material-symbols-sharp">insights </span>
                <h3>Growth</h3>
             </a>
             <a href="Trainer.php">
                <span class="material-symbols-sharp">Person </span>
                <h3>Trainers</h3>
                <span class="msg_count">14</span>
             </a>
             <a href="equipmentpage.php">
                <span class="material-symbols-sharp">receipt_long </span>
                <h3>Equipments</h3>
             </a>
              
             <a href="settings.html">
                <span class="material-symbols-sharp">settings </span>
                <h3>Settings</h3>
             </a>
             <a href="addeq.php">
                <span class="material-symbols-sharp">add </span>
                <h3>Add Equipment</h3>
             </a>
             <a href="request/reschedule_requests.php">
              <span class="material-symbols-sharp">receipt_long </span>
              <h3>Request Approval</h3>
           </a>
           <a href="../Login/logout.php">
              <span class="material-symbols-sharp">logout </span>
              <h3>logout</h3>
           </a>
            </div>
        </aside>
        <!-- end aside -->
  
        <main>
            <h1>Add New Member</h1>

            <div class="form-container">
                <form action="process_member.php" method="POST" enctype="multipart/form-data" id="memberForm">
                    <input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"
>
                    <div class="form-grid">
                        <!-- Personal Information -->
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>

                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>
                        <div class="form-group">
                            <label for="first_name">Username *</label>
                            <input type="text" id="username" name="username" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>

                        <div class="form-group">
                            <label for="dob">Date of Birth *</label>
                            <input type="date" id="dob" name="dob" required>
                        </div>

                        <div class="form-group">
                            <label for="blood_type">Blood Type *</label>
                            <select id="blood_type" name="blood_type" required>
                                <option value="">Select Blood Type</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="sex">Gender *</label>
                            <select id="sex" name="sex" required>
                                <option value="">Select Gender</option>
                                <option value="m">Male</option>
                                <option value="f">Female</option>
                                <option value="o">Other</option>
                            </select>
                        </div>

                        <!-- Membership Details -->
                        <div class="form-group">
                            <label for="membership_type">Membership Type *</label>
                            <select id="membership_type" name="membership_type" required>
                                <option value="">Select Type</option>
                                <option value="basic">Basic (₹1000/month)</option>
                                <option value="standard">Standard (₹1500/month)</option>
                                <option value="premium">Premium (₹2000/month)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="duration">Duration (months) *</label>
                            <select id="duration" name="duration" required>
                                <option value="1M">1 Month</option>
                                <option value="3M">3 Months</option>
                                <option value="6M">6 Months</option>
                                <option value="1Y">12 Months</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="amount">Amount (₹) *</label>
                            <input type="number" id="amount" name="amount" readonly>
                        </div>

                       

                        <div class="form-group">
                            <label for="profile_pic">Profile Picture *</label>
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                <input type="file" id="profile_pic" name="profile_pic" accept="image/*" capture="user" required>
                                <button type="button" id="camera_button" class="btn" style="background: var(--clr-info); color: var(--clr-white);">
                                    <span class="material-symbols-sharp">photo_camera</span>
                                    Take Photo
                                </button>
                                <video id="camera_feed" style="display: none; width: 100%; max-width: 400px; margin-top: 10px;" autoplay></video>
                                <canvas id="canvas" style="display: none;"></canvas>
                                <div id="photo_preview" style="display: none; margin-top: 10px;">
                                    <img id="captured_image" style="max-width: 100%; max-height: 200px; border: 1px solid var(--clr-info-light);">
                                </div>
                            </div>
                        </div>

                        <!-- Full Width Fields -->
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="address">Address *</label>
                            <textarea id="address" name="address" rows="3" required></textarea>
                        </div>
                    </div>

                    <div class="btn-container">
                        <button type="submit" name="submit" class="btn btn-primary">Register Member</button>
                        <button type="button" onclick="printReceipt()" class="btn btn-secondary">Print Receipt</button>
                    </div>
                </form>
            </div>
        </main>

        <!-- Right Section -->
        <div class="right">
            <div class="top">
                <button id="menu_bar">
                    <span class="material-symbols-sharp">menu</span>
                </button>

                <div class="theme-toggler">
                    <span class="material-symbols-sharp active">light_mode</span>
                    <span class="material-symbols-sharp">dark_mode</span>
                </div>

                <div class="profile">
                    <div class="info">
                        <p><b>miguel</b></p>
                        <p>Admin</p>
                        <small class="text-muted"></small>
                    </div>
                    <div class="profile-photo">
                        <!-- Add profile photo here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        // Add these validation functions after your existing script

        // Function to validate Date of Birth
        function validateDOB() {
            const dobInput = document.getElementById('dob');
            const dob = new Date(dobInput.value);
            const today = new Date();
            
            // Calculate age
            let age = today.getFullYear() - dob.getFullYear();
            const monthDiff = today.getMonth() - dob.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                age--;
            }
            
            // Check if age is valid (between 14 and 100)
            if (age < 14) {
                alert("Member must be at least 14 years old to join.");
                dobInput.value = '';
                dobInput.focus();
                return false;
            } else if (age > 100) {
                alert("Please verify the date of birth. Age appears to be over 100 years.");
                dobInput.focus();
                return false;
            } else if (dob > today) {
                alert("Date of birth cannot be in the future.");
                dobInput.value = '';
                dobInput.focus();
                return false;
            }
            
            return true;
        }

        // Add event listeners for real-time validation
        document.getElementById('dob').addEventListener('change', validateDOB);

        // Update form submission to include validation
        document.getElementById('memberForm').addEventListener('submit', function(event) {
            // Validate all fields before submission
            if (!validateDOB()) {
                event.preventDefault(); // Prevent form submission if validation fails
            }
        });

        // Camera functionality
        const cameraButton = document.getElementById('camera_button');
        const cameraFeed = document.getElementById('camera_feed');
        const canvas = document.getElementById('canvas');
        const photoPreview = document.getElementById('photo_preview');
        const capturedImage = document.getElementById('captured_image');
        const profilePicInput = document.getElementById('profile_pic');
        let stream = null;

        cameraButton.addEventListener('click', async function() {
            if (cameraFeed.style.display === 'none') {
                // Start camera
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    cameraFeed.srcObject = stream;
                    cameraFeed.style.display = 'block';
                    cameraButton.innerHTML = '<span class="material-symbols-sharp">camera</span> Capture Photo';
                } catch (err) {
                    console.error('Error accessing camera:', err);
                    alert('Could not access camera. Please ensure you have given permission and your device has a camera.');
                }
            } else if (cameraButton.innerHTML.includes('Capture')) {
                // Take photo
                const ctx = canvas.getContext('2d');
                canvas.width = cameraFeed.videoWidth;
                canvas.height = cameraFeed.videoHeight;
                ctx.drawImage(cameraFeed, 0, 0, canvas.width, canvas.height);
                
                // Convert canvas to blob
                canvas.toBlob(function(blob) {
                    const file = new File([blob], "camera_capture.jpg", { type: "image/jpeg" });
                    
                    // Create a DataTransfer object and add the file
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    profilePicInput.files = dataTransfer.files;
                    
                    // Display preview
                    capturedImage.src = URL.createObjectURL(blob);
                    photoPreview.style.display = 'block';
                    
                    // Stop camera and update button
                    stopCamera();
                    cameraButton.innerHTML = '<span class="material-symbols-sharp">photo_camera</span> Retake Photo';
                }, 'image/jpeg');
            } else {
                // Retake photo
                photoPreview.style.display = 'none';
                cameraFeed.style.display = 'block';
                cameraButton.innerHTML = '<span class="material-symbols-sharp">camera</span> Capture Photo';
                stream = await navigator.mediaDevices.getUserMedia({ video: true });
                cameraFeed.srcObject = stream;
            }
        });

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                cameraFeed.style.display = 'none';
            }
        }

        // Stop camera when form is submitted
        document.getElementById('memberForm').addEventListener('submit', function() {
            stopCamera();
        });

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            stopCamera();
        });
        
        // Calculate amount based on membership type and duration
        function calculateAmount() {
            const membershipType = document.getElementById('membership_type').value;
            const duration = parseInt(document.getElementById('duration').value);
            let baseAmount = 0;

            switch(membershipType) {
                case 'basic':
                    baseAmount = 6000;
                    break;
                case 'standard':
                    baseAmount = 10000;
                    break;
                case 'premium':
                    baseAmount = 15000;
                    break;
            }

            const totalAmount = baseAmount * duration;
            document.getElementById('amount').value = totalAmount;
        }

        // Add event listeners
        document.getElementById('membership_type').addEventListener('change', calculateAmount);
        document.getElementById('duration').addEventListener('change', calculateAmount);

        // Function to print receipt
        function printReceipt() {
            const formData = new FormData(document.getElementById('memberForm'));
            let receiptContent = `
                <div style="padding: 20px; max-width: 800px; margin: 0 auto;">
                    <h2 style="text-align: center;">GymShark Membership Receipt</h2>
                    <hr>
                    <p><strong>Date:</strong> ${new Date().toLocaleDateString()}</p>
                    <p><strong>Member Name:</strong> ${formData.get('first_name')} ${formData.get('last_name')}</p>
                    <p><strong>Membership Type:</strong> ${formData.get('membership_type')}</p>
                    <p><strong>Duration:</strong> ${formData.get('duration')} months</p>
                    <p><strong>Amount Paid:</strong> ₹${formData.get('amount')}</p>
                    <p><strong>Balance Due:</strong> ₹${formData.get('balance')}</p>
                    <hr>
                    <p style="text-align: center;">Thank you for joining GymShark!</p>
                </div>
            `;

            const printWindow = window.open('', '', 'width=800,height=600');
            printWindow.document.write('<html><head><title>Membership Receipt</title></head><body>');
            printWindow.document.write(receiptContent);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }
    </script>
</body>
</html>