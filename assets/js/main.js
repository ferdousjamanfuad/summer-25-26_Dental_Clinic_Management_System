// Tab switching functionality
function switchTab(tabName) {
    // Remove active class from all tab buttons and content
    var tabBtns = document.querySelectorAll('.tab-btn');
    var tabContents = document.querySelectorAll('.tab-content');
    
    for (var i = 0; i < tabBtns.length; i++) {
        tabBtns[i].classList.remove('active');
    }
    for (var i = 0; i < tabContents.length; i++) {
        tabContents[i].classList.remove('active');
    }
    
    // Add active class to clicked tab and its content
    document.querySelector('[data-tab="' + tabName + '"]').classList.add('active');
    document.getElementById('tab-' + tabName).classList.add('active');
}

// Form validation
function validateLoginForm() {
    var email = document.getElementById('email').value.trim();
    var password = document.getElementById('password').value.trim();
    var errorDiv = document.getElementById('form-errors');
    var errors = [];
    
    if (email === '') {
        errors.push('Email is required');
    } else {
        // Simple email validation
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            errors.push('Please enter a valid email');
        }
    }
    
    if (password === '') {
        errors.push('Password is required');
    }
    
    if (errors.length > 0) {
        if (errorDiv) {
            errorDiv.innerHTML = '<div class="alert alert-error">' + errors.join('<br>') + '</div>';
        }
        return false;
    }
    
    return true;
}

function validateRegisterForm() {
    var fullName = document.getElementById('full_name').value.trim();
    var email = document.getElementById('email').value.trim();
    var phone = document.getElementById('phone').value.trim();
    var password = document.getElementById('password').value.trim();
    var confirmPassword = document.getElementById('confirm_password').value.trim();
    var errorDiv = document.getElementById('form-errors');
    var errors = [];
    
    if (fullName === '') {
        errors.push('Full name is required');
    } else {
        var namePattern = /^[a-zA-Z .'-]+$/;
        if (!namePattern.test(fullName)) {
            errors.push('Name can only contain letters and spaces');
        }
    }
    
    if (email === '') {
        errors.push('Email is required');
    } else {
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            errors.push('Please enter a valid email');
        }
    }
    
    if (phone === '') {
        errors.push('Phone number is required');
    }
    
    if (password === '') {
        errors.push('Password is required');
    } else if (password.length < 6) {
        errors.push('Password must be at least 6 characters');
    }
    
    if (password !== confirmPassword) {
        errors.push('Passwords do not match');
    }
    
    if (errors.length > 0) {
        if (errorDiv) {
            errorDiv.innerHTML = '<div class="alert alert-error">' + errors.join('<br>') + '</div>';
        }
        return false;
    }
    
    return true;
}

// Confirm delete actions
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this?');
}

// Auto-hide flash messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    var alerts = document.querySelectorAll('.alert');
    for (var i = 0; i < alerts.length; i++) {
        (function(alert) {
            setTimeout(function() {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500);
            }, 5000);
        })(alerts[i]);
    }
    
    // Set active sidebar link
    var currentPath = window.location.pathname;
    var sidebarLinks = document.querySelectorAll('.sidebar-menu a');
    for (var i = 0; i < sidebarLinks.length; i++) {
        if (sidebarLinks[i].href.indexOf(currentPath) !== -1) {
            sidebarLinks[i].classList.add('active');
        }
    }
});

// Toggle password visibility
function togglePassword(inputId) {
    var input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
}
