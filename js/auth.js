/**
 * Authentication Manager
 * Handles user login, registration, and session management
 */

class AuthManager {
    constructor() {
        this.initializeEventListeners();
        this.initAuth();
    }

    async initAuth() {
        // Check if user is already logged in
        try {
            const response = await fetch('/php/auth.php?action=verify');
            const data = await response.json();
            
            if (data.authenticated) {
                this.handleLoginSuccess(data.user);
            }
        } catch (error) {
            console.log('No active session');
        }
    }

    initializeEventListeners() {
        // Handle login form submission
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleLogin();
            });
        }

        // Handle registration form submission
        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleRegister();
            });
        }

        // Handle social login buttons
        const socialLoginButtons = document.querySelectorAll('.social-login-btn');
        socialLoginButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleSocialLogin(button.dataset.provider);
            });
        });

        // Handle forgot password
        const forgotPasswordLink = document.getElementById('forgotPasswordLink');
        if (forgotPasswordLink) {
            forgotPasswordLink.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleForgotPassword();
            });
        }
    }

    async handleLogin() {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        if (!email || !password) {
            this.showError('Please fill in all fields.');
            return;
        }

        if (!this.validateEmail(email)) {
            this.showError('Please enter a valid email address.');
            return;
        }

        this.showLoading(true);

        try {
            const response = await fetch('/php/auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'login',
                    email: email,
                    password: password
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                this.handleLoginSuccess(data.user);
            } else {
                this.showError(data.error || 'Login failed');
            }
        } catch (error) {
            console.error('Login error:', error);
            this.showError('Login failed. Please try again.');
        } finally {
            this.showLoading(false);
        }
    }

    handleLoginSuccess(user) {
        // Store user data in localStorage
        localStorage.setItem('currentUser', JSON.stringify(user));

        this.showSuccess('Login successful! Redirecting...');
        
        // Redirect based on role
        setTimeout(() => {
            switch(user.role) {
                case 'patient':
                    window.location.href = 'patient-dashboard.html';
                    break;
                case 'doctor':
                    window.location.href = 'doctor-dashboard.html';
                    break;
                case 'admin':
                    window.location.href = 'admin-dashboard.html';
                    break;
                default:
                    window.location.href = 'dashboard.html';
            }
        }, 2000);
    }

    async handleRegister() {
        const firstName = document.getElementById('firstName').value;
        const lastName = document.getElementById('lastName').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        const phone = document.getElementById('phone').value;

        if (!firstName || !lastName || !email || !password || !confirmPassword) {
            this.showError('Please fill in all required fields.');
            return;
        }

        if (!this.validateEmail(email)) {
            this.showError('Please enter a valid email address.');
            return;
        }

        if (password !== confirmPassword) {
            this.showError('Passwords do not match.');
            return;
        }

        if (password.length < 6) {
            this.showError('Password must be at least 6 characters long.');
            return;
        }

        this.showLoading(true);

        try {
            const response = await fetch('/php/auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'register',
                    first_name: firstName,
                    last_name: lastName,
                    email: email,
                    password: password,
                    phone: phone,
                    role: 'patient'
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                this.showSuccess('Registration successful! Redirecting to login...');
                
                setTimeout(() => {
                    window.location.href = 'login.html';
                }, 2000);
            } else {
                this.showError(data.error || 'Registration failed');
            }
        } catch (error) {
            console.error('Registration error:', error);
            this.showError('Registration failed. Please try again.');
        } finally {
            this.showLoading(false);
        }
    }

    async handleLogout() {
        try {
            await fetch('/php/auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'logout'
                })
            });

            localStorage.removeItem('currentUser');
            window.location.href = 'login.html';
        } catch (error) {
            console.error('Logout error:', error);
            localStorage.removeItem('currentUser');
            window.location.href = 'login.html';
        }
    }

    handleSocialLogin(provider) {
        this.showError(`${provider} login will be available soon.`);
    }

    handleForgotPassword() {
        const email = prompt('Please enter your email address:');
        if (email && this.validateEmail(email)) {
            this.showSuccess('Password reset instructions sent to your email.');
        } else if (email) {
            this.showError('Please enter a valid email address.');
        }
    }

    validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    showError(message) {
        this.showMessage(message, 'error');
    }

    showSuccess(message) {
        this.showMessage(message, 'success');
    }

    showMessage(message, type) {
        // Remove existing messages
        const existingMessages = document.querySelectorAll('.auth-message');
        existingMessages.forEach(msg => msg.remove());

        // Create new message
        const messageDiv = document.createElement('div');
        messageDiv.className = `auth-message ${type}`;
        messageDiv.textContent = message;
        messageDiv.style.cssText = `
            padding: 12px 16px;
            margin: 16px 0;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
            ${type === 'error' ? 
                'background-color: #fee; color: #c53030; border: 1px solid #fed7d7;' : 
                'background-color: #f0fff4; color: #2f855a; border: 1px solid #9ae6b4;'}
        `;

        // Insert message at the top of the form
        const form = document.querySelector('form');
        if (form) {
            form.insertBefore(messageDiv, form.firstChild);
        }

        // Auto-remove message after 5 seconds
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 5000);
    }

    showLoading(show) {
        const submitButtons = document.querySelectorAll('button[type="submit"]');
        submitButtons.forEach(button => {
            if (show) {
                button.disabled = true;
                button.style.opacity = '0.6';
                button.textContent = 'Processing...';
            } else {
                button.disabled = false;
                button.style.opacity = '1';
                button.textContent = button.getAttribute('data-original-text') || 'Submit';
            }
        });
    }
}

// Initialize authentication manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.authManager = new AuthManager();
});

// Setup social login complete callback
window.socialLoginSetupComplete = () => {
    console.log('Social login setup complete');
};