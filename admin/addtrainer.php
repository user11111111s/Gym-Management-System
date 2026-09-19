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
    <title>Add Trainer - GymShark</title>
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

        /* Camera styles */
        .camera-container {
            width: 100%;
            position: relative;
            overflow: hidden;
            margin-bottom: 1rem;
            border: 1px solid var(--clr-info-light);
            border-radius: var(--border-radius-1);
        }

        #cameraFeed {
            width: 100%;
            display: block;
        }

        #capturedImage {
            width: 100%;
            display: none;
            border-radius: var(--border-radius-1);
        }

        .camera-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .camera-btn {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius-1);
            cursor: pointer;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .camera-btn.capture {
            background: var(--clr-primary);
            color: var(--clr-white);
        }

        .camera-btn.retake {
            background: var(--clr-danger);
            color: var(--clr-white);
        }

        .camera-instructions {
            font-size: 0.85rem;
            color: var(--clr-dark-variant);
            margin-bottom: 0.5rem;
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
             <a href="add_member.php">
                <span class="material-symbols-sharp">person_add</span>
                <h3>Add Member</h3>
             </a>
             <a href="#" class="active">
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
            <h1>Add New Trainer</h1>
            <div class="form-container">
                <form action="process_trainer.php" method="POST" enctype="multipart/form-data" id="trainerForm">
                    <input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"
>
                    <div class="form-grid">
                        <!-- Personal Information -->

                        <div class="form-group">
                            <label for="FirstName">First Name *</label>
                            <input type="text" id="FirstName" name="FirstName" required>
                        </div>

                        <div class="form-group">
                            <label for="LastName">Last Name *</label>
                            <input type="text" id="LastName" name="LastName" required>
                        </div>

                        <div class="form-group">
                            <label for="trainer_username">Username *</label>
                            <input type="text" id="trainer_username" name="trainer_username" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="number">Phone Number *</label>
                            <input type="tel" id="number" name="number" required>
                        </div>

                        <div class="form-group">
                            <label for="dob">Date of Birth *</label>
                            <input type="date" id="dob" name="dob" required>
                        </div>


                        <div class="form-group">
                            <label for="specialization">Specialization *</label>
                            <select id="specialization" name="specialization" required>
                                <option value="">Select Specialization</option>
                                <option value="strength">Strength Training</option>
                                <option value="cardio">Cardio</option>
                                <option value="yoga">Yoga</option>
                                <option value="pilates">Pilates</option>
                                <option value="crossfit">CrossFit</option>
                                <option value="nutrition">Nutrition</option>
                                <option value="weight_loss">Weight Loss</option>
                                <option value="bodybuilding">Bodybuilding</option>
                                <option value="rehabilitation">Rehabilitation</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="experience">Experience (Years) *</label>
                            <input type="number" id="experience" name="experience" min="0" max="50" required>
                        </div>

                        <div class="form-group">
                            <label for="hourly_rate">Hourly Rate (₹) *</label>
                            <input type="number" id="hourly_rate" name="hourly_rate" min="100" required>
                        </div>      
<!-- Replace the current availability dropdown with this detailed scheduler -->
<div class="form-group" style="grid-column: 1 / -1;">
    <label for="availability_type">Availability Type *</label>
    <select id="availability_type" name="availability_type" required onchange="toggleAvailabilityFields()">
        <option value="">Select Availability Type</option>
        <option value="custom">Custom Schedule</option>
    </select>
</div>

<div id="custom_availability" style="grid-column: 1 / -1; display: none;">
    <div class="form-group">
        <label>Select Days Available *</label>
        <div class="day-checkboxes" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 0.5rem; margin-top: 0.5rem;">
            <div>
                <input type="checkbox" id="monday" name="days[]" value="monday" onchange="toggleDayTimeFields('monday')">
                <label for="monday">Monday</label>
            </div>
            <div>
                <input type="checkbox" id="tuesday" name="days[]" value="tuesday" onchange="toggleDayTimeFields('tuesday')">
                <label for="tuesday">Tuesday</label>
            </div>
            <div>
                <input type="checkbox" id="wednesday" name="days[]" value="wednesday" onchange="toggleDayTimeFields('wednesday')">
                <label for="wednesday">Wednesday</label>
            </div>
            <div>
                <input type="checkbox" id="thursday" name="days[]" value="thursday" onchange="toggleDayTimeFields('thursday')">
                <label for="thursday">Thursday</label>
            </div>
            <div>
                <input type="checkbox" id="friday" name="days[]" value="friday" onchange="toggleDayTimeFields('friday')">
                <label for="friday">Friday</label>
            </div>
            <div>
                <input type="checkbox" id="saturday" name="days[]" value="saturday" onchange="toggleDayTimeFields('saturday')">
                <label for="saturday">Saturday</label>
            </div>
            <div>
                <input type="checkbox" id="sunday" name="days[]" value="sunday" onchange="toggleDayTimeFields('sunday')">
                <label for="sunday">Sunday</label>
            </div>
        </div>
    </div>

    <!-- Time slots for each day -->
    <div id="time_slots_container">
        <!-- Monday time slots -->
        <div id="monday_times" class="day-time-slots" style="display: none; margin-top: 1rem; padding: 1rem; border: 1px solid var(--clr-info-light); border-radius: var(--border-radius-1);">
            <h4 style="margin-top: 0; margin-bottom: 0.5rem;">Monday Hours</h4>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="monday_start">Start Time</label>
                    <input type="time" id="monday_start" name="monday_start" class="time-input">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="monday_end">End Time</label>
                    <input type="time" id="monday_end" name="monday_end" class="time-input">
                </div>
            </div>
        </div>

        <!-- Tuesday time slots -->
        <div id="tuesday_times" class="day-time-slots" style="display: none; margin-top: 1rem; padding: 1rem; border: 1px solid var(--clr-info-light); border-radius: var(--border-radius-1);">
            <h4 style="margin-top: 0; margin-bottom: 0.5rem;">Tuesday Hours</h4>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="tuesday_start">Start Time</label>
                    <input type="time" id="tuesday_start" name="tuesday_start" class="time-input">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="tuesday_end">End Time</label>
                    <input type="time" id="tuesday_end" name="tuesday_end" class="time-input">
                </div>
            </div>
        </div>

        <!-- Wednesday time slots -->
        <div id="wednesday_times" class="day-time-slots" style="display: none; margin-top: 1rem; padding: 1rem; border: 1px solid var(--clr-info-light); border-radius: var(--border-radius-1);">
            <h4 style="margin-top: 0; margin-bottom: 0.5rem;">Wednesday Hours</h4>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="wednesday_start">Start Time</label>
                    <input type="time" id="wednesday_start" name="wednesday_start" class="time-input">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="wednesday_end">End Time</label>
                    <input type="time" id="wednesday_end" name="wednesday_end" class="time-input">
                </div>
            </div>
        </div>

        <!-- Thursday time slots -->
        <div id="thursday_times" class="day-time-slots" style="display: none; margin-top: 1rem; padding: 1rem; border: 1px solid var(--clr-info-light); border-radius: var(--border-radius-1);">
            <h4 style="margin-top: 0; margin-bottom: 0.5rem;">Thursday Hours</h4>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="thursday_start">Start Time</label>
                    <input type="time" id="thursday_start" name="thursday_start" class="time-input">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="thursday_end">End Time</label>
                    <input type="time" id="thursday_end" name="thursday_end" class="time-input">
                </div>
            </div>
        </div>

        <!-- Friday time slots -->
        <div id="friday_times" class="day-time-slots" style="display: none; margin-top: 1rem; padding: 1rem; border: 1px solid var(--clr-info-light); border-radius: var(--border-radius-1);">
            <h4 style="margin-top: 0; margin-bottom: 0.5rem;">Friday Hours</h4>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="friday_start">Start Time</label>
                    <input type="time" id="friday_start" name="friday_start" class="time-input">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="friday_end">End Time</label>
                    <input type="time" id="friday_end" name="friday_end" class="time-input">
                </div>
            </div>
        </div>

        <!-- Saturday time slots -->
        <div id="saturday_times" class="day-time-slots" style="display: none; margin-top: 1rem; padding: 1rem; border: 1px solid var(--clr-info-light); border-radius: var(--border-radius-1);">
            <h4 style="margin-top: 0; margin-bottom: 0.5rem;">Saturday Hours</h4>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="saturday_start">Start Time</label>
                    <input type="time" id="saturday_start" name="saturday_start" class="time-input">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="saturday_end">End Time</label>
                    <input type="time" id="saturday_end" name="saturday_end" class="time-input">
                </div>
            </div>
        </div>

        <!-- Sunday time slots -->
        <div id="sunday_times" class="day-time-slots" style="display: none; margin-top: 1rem; padding: 1rem; border: 1px solid var(--clr-info-light); border-radius: var(--border-radius-1);">
            <h4 style="margin-top: 0; margin-bottom: 0.5rem;">Sunday Hours</h4>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="sunday_start">Start Time</label>
                    <input type="time" id="sunday_start" name="sunday_start" class="time-input">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="sunday_end">End Time</label>
                    <input type="time" id="sunday_end" name="sunday_end" class="time-input">
                </div>
            </div>
        </div>
    </div>
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

                        <!-- Camera capture section -->
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="profile_pic">Profile Picture *</label>
                            <p class="camera-instructions">Take a photo or upload an image file</p>
                            
                            <div class="camera-container">
                                <video id="cameraFeed" autoplay playsinline></video>
                                <canvas id="canvas" style="display:none;"></canvas>
                                <img id="capturedImage" alt="Captured profile picture">
                            </div>
                            
                            <div class="camera-buttons">
                                <button type="button" id="captureBtn" class="camera-btn capture">Take Photo</button>
                                <button type="button" id="retakeBtn" class="camera-btn retake" style="display:none;">Retake Photo</button>
                            </div>
                            
                            <input type="file" id="profile_pic" name="profile_pic" accept="image/*" style="margin-top: 0.5rem;">
                            <input type="hidden" id="camera_capture" name="camera_capture" required>
                        </div>

                        <!-- Full Width Fields -->
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="address">Address *</label>
                            <textarea id="address" name="address" rows="2" required></textarea>
                        </div>

                        
                    </div>

                    <div class="btn-container">
                        <button type="submit" name="submit" class="btn btn-primary">Register Trainer</button>
                        <button type="button" onclick="printContract()" class="btn btn-secondary">Print Contract</button>
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
   // Enhanced validation function
function validateForm() {
    let dobInput = document.getElementById("dob").value;
    let experienceInput = parseInt(document.getElementById("experience").value, 10);
    
    // Check if DOB is provided
    if (!dobInput) {
        alert("Please enter your date of birth.");
        return false;
    }
    
    let today = new Date();
    let dob = new Date(dobInput);
    
    // Calculate age
    let age = today.getFullYear() - dob.getFullYear();
    let monthDiff = today.getMonth() - dob.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
        age--;
    }
    
    // Validate age range (18-65)
    if (age < 18) {
        alert("You must be at least 18 years old to register as a trainer.");
        return false;
    }
    if (age > 65) {
        alert("Trainer age must not exceed 65 years.");
        return false;
    }
    
    // Calculate maximum logical experience
    // Assuming most people start professional training after at least some education/certification
    const minStartingAge = 18; // Minimum age to start as a trainer
    const maxPossibleExperience = age - minStartingAge;
    
    // Validate experience
    if (experienceInput < 0) {
        alert("Experience cannot be negative.");
        return false;
    }
    if (experienceInput > maxPossibleExperience) {
        alert(`Experience years cannot exceed ${maxPossibleExperience} years based on your age.`);
        return false;
    }
    
    return true;
}

// Add event listener to the form for submission
document.addEventListener('DOMContentLoaded', function() {
    const trainerForm = document.getElementById('trainerForm');
    if (trainerForm) {
        trainerForm.addEventListener('submit', function(event) {
            // Run validations
            if (!validateForm()) {
                event.preventDefault();
                return false;
            }
            
            // Also run availability validation if it exists
            if (typeof validateAvailability === 'function' && !validateAvailability()) {
                event.preventDefault();
                return false;
            }
            
            return true;
        });
    }
    
    // Real-time DOB validation
    const dobInput = document.getElementById('dob');
    const experienceInput = document.getElementById('experience');
    
    if (dobInput) {
        dobInput.addEventListener('change', function() {
            validateDOB();
        });
        
        // Also validate if user clicks out of the field
        dobInput.addEventListener('blur', function() {
            validateDOB();
        });
    }
    
    function validateDOB() {
        if (dobInput.value) {
            const dob = new Date(dobInput.value);
            const today = new Date();
            
            // Calculate age
            let age = today.getFullYear() - dob.getFullYear();
            let monthDiff = today.getMonth() - dob.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                age--;
            }
            
            // Display validation message
            const validationMsg = document.getElementById('dob-validation-msg');
            if (!validationMsg) {
                // Create validation message element if it doesn't exist
                const msgElement = document.createElement('div');
                msgElement.id = 'dob-validation-msg';
                msgElement.style.marginTop = '5px';
                msgElement.style.fontSize = '0.85rem';
                dobInput.parentNode.appendChild(msgElement);
            }
            
            const msgElement = document.getElementById('dob-validation-msg');
            
            // Validate age range (18-65)
            if (age < 18) {
                msgElement.textContent = "You must be at least 18 years old to register as a trainer.";
                msgElement.style.color = "#e63946"; // Red color for error
                dobInput.style.borderColor = "#e63946";
            } else if (age > 65) {
                msgElement.textContent = "Trainer age must not exceed 65 years.";
                msgElement.style.color = "#e63946"; // Red color for error
                dobInput.style.borderColor = "#e63946";
            } else {
                msgElement.textContent = "Age verification successful.";
                msgElement.style.color = "#2ecc71"; // Green color for success
                dobInput.style.borderColor = "#2ecc71";
                
                // Set max experience based on age
                if (experienceInput) {
                    const maxExperience = age - 18;
                    experienceInput.setAttribute('max', maxExperience);
                    // Update experience input if current value exceeds max
                    if (parseInt(experienceInput.value) > maxExperience) {
                        experienceInput.value = maxExperience;
                    }
                }
            }
        }
    }
});

// Camera functionality
const cameraFeed = document.getElementById('cameraFeed');
const canvas = document.getElementById('canvas');
const capturedImage = document.getElementById('capturedImage');
const captureBtn = document.getElementById('captureBtn');
const retakeBtn = document.getElementById('retakeBtn');
const cameraCapture = document.getElementById('camera_capture');
const fileInput = document.getElementById('profile_pic');
let stream;

// Start camera when page loads
document.addEventListener('DOMContentLoaded', startCamera);

function startCamera() {
    navigator.mediaDevices.getUserMedia({ video: true, audio: false })
        .then(function(videoStream) {
            stream = videoStream;
            cameraFeed.srcObject = stream;
            cameraFeed.style.display = 'block';
            capturedImage.style.display = 'none';
            captureBtn.style.display = 'inline-block';
            retakeBtn.style.display = 'none';
        })
        .catch(function(error) {
            console.error("Camera error: ", error);
            alert("Unable to access camera. Please make sure your camera is connected and you have granted permission.");
        });
}

// Capture image from camera
captureBtn.addEventListener('click', function() {
    // Set canvas dimensions to match video
    canvas.width = cameraFeed.videoWidth;
    canvas.height = cameraFeed.videoHeight;
    
    // Draw current video frame to canvas
    canvas.getContext('2d').drawImage(cameraFeed, 0, 0, canvas.width, canvas.height);
    
    // Convert canvas to data URL
    const imageDataURL = canvas.toDataURL('image/png');
    
    // Display captured image
    capturedImage.src = imageDataURL;
    capturedImage.style.display = 'block';
    cameraFeed.style.display = 'none';
    
    // Store image data in hidden input
    cameraCapture.value = imageDataURL;
    
    // Update buttons
    captureBtn.style.display = 'none';
    retakeBtn.style.display = 'inline-block';
    
    // Stop camera stream
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
});

// Retake photo
retakeBtn.addEventListener('click', function() {
    cameraCapture.value = '';
    startCamera();
});

// Handle form submission validation
document.getElementById('trainerForm').addEventListener('submit', function(event) {
    if (!cameraCapture.value && !fileInput.files.length) {
        event.preventDefault();
        alert('Profile picture is required. Please take a photo with the camera or upload an image file.');
    }
});

// Handle file upload as an alternative to camera
fileInput.addEventListener('change', function() {
    if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            capturedImage.src = e.target.result;
            capturedImage.style.display = 'block';
            cameraFeed.style.display = 'none';
            
            // Stop camera stream if running
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            
            // Update buttons
            captureBtn.style.display = 'none';
            retakeBtn.style.display = 'inline-block';
            
            // Clear camera capture value since we're using file upload
            cameraCapture.value = '';
        }
        
        reader.readAsDataURL(fileInput.files[0]);
    }
});

// Function to print contract
function printContract() {
    const formData = new FormData(document.getElementById('trainerForm'));
    const fullName = formData.get('FirstName') + ' ' + formData.get('LastName');
    
    // Get availability text from the selected option
    const availabilityType = document.getElementById('availability_type');
    const availabilityText = availabilityType.options[availabilityType.selectedIndex].text;
    
    // Check if custom availability is selected and generate a custom availability string
    let availabilityDetails = availabilityText;
    if (formData.get('availability_type') === 'custom') {
        // Get selected days
        const selectedDays = [];
        document.querySelectorAll('input[name="days[]"]:checked').forEach(checkbox => {
            const day = checkbox.value;
            const startTime = document.getElementById(`${day}_start`).value;
            const endTime = document.getElementById(`${day}_end`).value;
            if (startTime && endTime) {
                selectedDays.push(`${day.charAt(0).toUpperCase() + day.slice(1)}: ${startTime} to ${endTime}`);
            }
        });
        
        if (selectedDays.length > 0) {
            availabilityDetails += ` (${selectedDays.join(', ')})`;
        }
    }
    
    let contractContent = `
        <div style="padding: 20px; max-width: 800px; margin: 0 auto; font-family: Arial, sans-serif;">
            <h2 style="text-align: center;">GymShark Trainer Contract</h2>
            <hr>
            <p><strong>Date:</strong> ${new Date().toLocaleDateString()}</p>
            <p><strong>Trainer Name:</strong> ${fullName}</p>
            <p><strong>Trainer ID:</strong> ${formData.get('trainer_id')}</p>
            <p><strong>Specialization:</strong> ${document.getElementById('specialization').options[document.getElementById('specialization').selectedIndex].text}</p>
            <p><strong>Experience:</strong> ${formData.get('experience')} years</p>
            <p><strong>Hourly Rate:</strong> ₹${formData.get('hourly_rate')}</p>
            <p><strong>Availability:</strong> ${availabilityDetails}</p>
            
            <h3 style="margin-top: 20px;">Terms and Conditions</h3>
            <ol>
                <li>The trainer agrees to provide professional fitness instruction to GymShark members.</li>
                <li>The trainer shall maintain all certifications and qualifications required for their role.</li>
                <li>The trainer will adhere to all GymShark policies and procedures.</li>
                <li>Payment will be processed biweekly based on documented client sessions.</li>
                <li>Either party may terminate this agreement with two weeks' written notice.</li>
            </ol>
            
            <div style="margin-top: 40px; display: flex; justify-content: space-between;">
                <div style="width: 45%;">
                    <p>____________________________</p>
                    <p>Trainer Signature</p>
                </div>
                <div style="width: 45%;">
                    <p>____________________________</p>
                    <p>GymShark Representative</p>
                </div>
            </div>
            
            <hr style="margin-top: 30px;">
            <p style="text-align: center; font-size: 12px;">This is a binding agreement between the trainer and GymShark Fitness.</p>
        </div>
    `;

    const printWindow = window.open('', '', 'width=800,height=600');
    printWindow.document.write('<html><head><title>Trainer Contract</title></head><body>');
    printWindow.document.write(contractContent);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

// Function to toggle availability fields based on selection
function toggleAvailabilityFields() {
    const availabilityType = document.getElementById('availability_type').value;
    const customAvailability = document.getElementById('custom_availability');
    
    if (availabilityType === 'custom') {
        customAvailability.style.display = 'block';
    } else {
        customAvailability.style.display = 'none';
        
        // Reset all checkboxes and hide time fields
        const dayCheckboxes = document.querySelectorAll('input[name="days[]"]');
        dayCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
            toggleDayTimeFields(checkbox.value);
        });
    }
}

// Function to toggle time fields for each day
function toggleDayTimeFields(day) {
    const checkbox = document.getElementById(day);
    const timeFields = document.getElementById(`${day}_times`);
    
    if (checkbox.checked) {
        timeFields.style.display = 'block';
    } else {
        timeFields.style.display = 'none';
        // Reset time inputs
        document.getElementById(`${day}_start`).value = '';
        document.getElementById(`${day}_end`).value = '';
    }
}

// Function to validate custom availability before form submission
function validateAvailability() {
    const availabilityType = document.getElementById('availability_type').value;
    
    if (availabilityType === 'custom') {
        const dayCheckboxes = document.querySelectorAll('input[name="days[]"]:checked');
        
        // Check if at least one day is selected
        if (dayCheckboxes.length === 0) {
            alert('Please select at least one day of availability.');
            return false;
        }
        
        // Check if time slots are filled for selected days
        let timeValid = true;
        dayCheckboxes.forEach(checkbox => {
            const day = checkbox.value;
            const startTime = document.getElementById(`${day}_start`).value;
            const endTime = document.getElementById(`${day}_end`).value;
            
            if (!startTime || !endTime) {
                timeValid = false;
            }
        });
        
        if (!timeValid) {
            alert('Please fill in both start and end times for all selected days.');
            return false;
        }
    }
    
    return true;
}

// Add form validation before submission
document.addEventListener('DOMContentLoaded', function() {
    const trainerForm = document.getElementById('trainerForm');
    if (trainerForm) {
        const originalSubmitHandler = trainerForm.onsubmit;
        
        trainerForm.onsubmit = function(event) {
            if (!validateAvailability()) {
                event.preventDefault();
                return false;
            }
            
            // Call original submit handler if it exists
            if (typeof originalSubmitHandler === 'function') {
                return originalSubmitHandler.call(this, event);
            }
            
            return true;
        };
    }
});
    </script>
</body>
</html>