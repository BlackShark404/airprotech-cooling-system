<?php use Core\Session;?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Air Conditioning Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/home.css">
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar py-2">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="contact-info">
                <a href="tel:+1234567890" class="me-3 text-white text-decoration-none">
                    <i class="fas fa-phone me-2"></i>+1 234 567 890
                </a>
                <a href="mailto:contact@apcs.com" class="text-white text-decoration-none">
                    <i class="fas fa-envelope me-2"></i>contact@apcs.com
                </a>
            </div>
            <div class="social-links">
                <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="/assets/images/Air-TechLogo.jpg" alt="Logo" class="rounded-circle me-2" width="40" height="40">
                <span class="brand-text">AIR<span class="text-danger">PROTECH</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="/user/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/user/services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="/user/products">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="/user/bookings">My Bookings & Service Requests</a></li>

                    <!-- User Profile -->
                    <li class="nav-item dropdown ms-3">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src=<?=Session::get('profile_url') ? Session::get('profile_url') : '/assets/images/default-profile.jpg'?> alt="Profile" class="rounded-circle me-2" width="36" height="36">
                            <div class="d-flex flex-column lh-sm">
                                <span class="fw-semibold small text-dark"><?=$_SESSION['full_name'] ?? 'User'?></span>
                                <small class="text-success">● Online</small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="/user/profile">Profile</a></li>
                            <li><a class="dropdown-item" href="/user/settings">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/logout">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- User Profile Area -->
    <div class="profile-area py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <!-- Profile Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4 text-center">
                            <div class="position-relative mb-4 mx-auto" style="width: 150px; height: 150px;">
                                <img src="<?=Session::get('profile_url') ? Session::get('profile_url') : '/assets/images/default-profile.jpg'?>" 
                                     alt="Profile Picture" 
                                     class="rounded-circle border shadow-sm" 
                                     style="width: 150px; height: 150px; object-fit: cover;">
                                
                                <button type="button" 
                                        class="btn btn-primary btn-sm rounded-circle position-absolute end-0 bottom-0"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#profileImageModal">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                            
                            <h5 class="fw-bold mb-1"><?=$_SESSION['full_name'] ?? 'User'?></h5>
                            <p class="text-muted mb-3"><?=$_SESSION['email'] ?? ''?></p>
                            
                            <div class="d-flex justify-content-center gap-2">
                                <span class="badge bg-light-blue text-blue px-3 py-2">Customer</span>
                                <span class="badge bg-light-green text-green px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i> Verified
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Account Stats -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">Account Statistics</h5>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Active Bookings</span>
                                <span class="fw-semibold">3</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Pending Services</span>
                                <span class="fw-semibold">2</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Completed Services</span>
                                <span class="fw-semibold">12</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Product Orders</span>
                                <span class="fw-semibold">5</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-8">
                    <!-- Profile Information Form -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Personal Information</h5>
                            
                            <form id="profileUpdateForm">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="firstName" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="firstName" name="first_name" value="<?=$_SESSION['first_name'] ?? ''?>">
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="lastName" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lastName" name="last_name" value="<?=$_SESSION['last_name'] ?? ''?>">
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?=$_SESSION['email'] ?? ''?>" readonly>
                                        <small class="text-muted">Email cannot be changed</small>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="phoneNumber" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phoneNumber" name="phone_number" value="<?=$_SESSION['phone_number'] ?? ''?>">
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="3"><?=$_SESSION['address'] ?? ''?></textarea>
                                    </div>
                                    
                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="fas fa-save me-2"></i> Save Changes
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Password Update Section -->
                    <div class="card border-0 shadow-sm rounded-4 mt-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Change Password</h5>
                            
                            <form id="passwordUpdateForm">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="currentPassword" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="currentPassword" name="current_password">
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="newPassword" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="newPassword" name="new_password">
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password">
                                    </div>
                                    
                                    <div class="col-12 mt-3">
                                        <button type="submit" class="btn btn-outline-primary px-4">
                                            <i class="fas fa-lock me-2"></i> Update Password
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Profile Image Upload Modal -->
    <div class="modal fade" id="profileImageModal" tabindex="-1" aria-labelledby="profileImageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileImageModalLabel">Update Profile Picture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="profileImageForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="profileImage" class="form-label">Choose an image</label>
                            <input class="form-control" type="file" id="profileImage" name="profile_image" accept="image/*">
                            <div class="form-text">Maximum file size: 2MB. Supported formats: JPG, PNG, WEBP</div>
                        </div>
                        
                        <div class="text-center mt-4 mb-3">
                            <div id="imagePreview" class="d-none mb-3">
                                <img src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i> Upload Image
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container for Notifications -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <!-- Toasts will be inserted here dynamically -->
    </div>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    
    <script src="/assets/js/utility/form-handler.js"></script>
    <script src="/assets/js/utility/toast-notifications.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init();
        
        // Handle form submissions
        document.addEventListener('DOMContentLoaded', function() {
            // Profile update form
            handleFormSubmission('profileUpdateForm', '/api/users/profile/update');
            
            // Password update form
            handleFormSubmission('passwordUpdateForm', '/api/users/password/update');
            
            // Profile image preview
            const profileImageInput = document.getElementById('profileImage');
            const imagePreview = document.getElementById('imagePreview');
            const previewImage = imagePreview.querySelector('img');
            
            profileImageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        imagePreview.classList.remove('d-none');
                    }
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
            
            // Handle profile image upload
            const profileImageForm = document.getElementById('profileImageForm');
            profileImageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                axios.post('/api/users/profile/image', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(response => {
                    if (response.data.success) {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('profileImageModal'));
                        modal.hide();
                        
                        // Show success message
                        showToast('Success', response.data.message, 'success');
                        
                        // Update profile image on page
                        const profileImages = document.querySelectorAll('img[alt="Profile Picture"], img[alt="Profile"]');
                        profileImages.forEach(img => {
                            img.src = response.data.data.profile_url + '?t=' + new Date().getTime();
                        });
                        
                        // Reset form
                        profileImageForm.reset();
                        imagePreview.classList.add('d-none');
                    } else {
                        showToast('Error', response.data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Error', 'Failed to upload profile image. Please try again.', 'danger');
                });
            });
        });
    </script>
</body>
</html> 