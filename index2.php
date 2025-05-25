<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tindahan ni Aling Nena - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #f5576c 75%, #4facfe 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 1200px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 32px 64px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.2);
            overflow: hidden;
            min-height: 650px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .left-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .left-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 60"><circle cx="30" cy="30" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="15" cy="15" r="0.5" fill="rgba(255,255,255,0.08)"/><circle cx="45" cy="45" r="1.5" fill="rgba(255,255,255,0.06)"/></svg>') repeat;
            animation: float 25s linear infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            100% { transform: translateY(-60px) rotate(360deg); }
        }

        .logo {
            background: rgba(255, 255, 255, 0.15);
            padding: 32px;
            border-radius: 20px;
            margin-bottom: 40px;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            z-index: 1;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: white;
            font-weight: bold;
            box-shadow: 0 8px 24px rgba(79, 172, 254, 0.3);
            font-family: "Poppins", sans-serif;
        }

        .brand-text {
            color: rgba(255, 255, 255, 0.95);
            margin-top: 30px;
            margin-bottom: 20px;
            z-index: 1;
        }

        .brand-text h1 {
            font-size: 1.8rem;
            font-weight: 600;
            letter-spacing: 1px;
            font-family: "Poppins", sans-serif;
        }

        .recent-logins {
            width: 100%;
            max-width: 300px;
            z-index: 1;
        }

        .recent-logins h3 {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
            margin-bottom: 24px;
            text-align: center;
            font-weight: 500;
            font-family: "Poppins", sans-serif;
            letter-spacing: 0.5px;
        }

        .login-avatars {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            justify-content: center;
        }

        .avatar {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(15px);
            overflow: hidden;
            position: relative;
            font-family: "Poppins", sans-serif;
        }

        .avatar:hover {
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 12px 28px rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.25);
        }

        .right-section {
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
        }

        .login-header {
            margin-bottom: 40px;
        }

        .login-header h2 {
            font-size: 2.4rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            font-weight: 700;
            letter-spacing: -0.5px;
            font-family: "Poppins", sans-serif;
        }

        .login-header p {
            color: #6B7280;
            font-size: 1.1rem;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #374151;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 18px 24px;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.8);
            color: #374151;
            backdrop-filter: blur(10px);
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            background: white;
            transform: translateY(-1px);
        }

        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9CA3AF;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .password-toggle:hover {
            color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }

        .password-toggle svg {
            width: 20px;
            height: 20px;
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 32px;
        }

        .forgot-password a {
            color: #667eea;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
        }

        .forgot-password a:hover {
            color: #764ba2;
        }

        .forgot-password a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s ease;
        }

        .forgot-password a:hover::after {
            width: 100%;
        }

        .btn {
            width: 100%;
            padding: 18px;
            border: none;
            border-radius: 12px;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            letter-spacing: 0.5px;
            font-family: "Inter", sans-serif;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.25);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(102, 126, 234, 0.35);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background: rgba(102, 126, 234, 0.05);
            color: #667eea;
            border: 2px solid #667eea;
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.2);
        }

        .divider {
            text-align: center;
            margin: 32px 0;
            position: relative;
            color: #9CA3AF;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #E5E7EB, transparent);
        }

        .divider span {
            background: rgba(255, 255, 255, 0.9);
            padding: 0 24px;
            backdrop-filter: blur(10px);
        }

        .loading {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 8px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .remember-me {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin: 0;
            accent-color: #667eea;
            border-radius: 4px;
        }

        .checkbox-wrapper label {
            margin: 0;
            font-size: 0.95rem;
            cursor: pointer;
            color: #374151;
            font-weight: 400;
        }

        .error-message {
            color: #DC2626;
            font-size: 0.875rem;
            margin: 12px 0;
            padding: 12px 16px;
            background: linear-gradient(135deg, #FEF2F2 0%, #FDE8E8 100%);
            border: 1px solid #FECACA;
            border-radius: 10px;
            font-weight: 500;
            backdrop-filter: blur(10px);
        }

        .success-message {
            color: #059669;
            font-size: 0.875rem;
            margin-top: 8px;
            display: none;
        }

        .input-error {
            border-color: #DC2626 !important;
            background: linear-gradient(135deg, #FEF2F2 0%, #FDE8E8 100%) !important;
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
                margin: 10px;
                border-radius: 20px;
            }
            
            .left-section {
                padding: 40px 30px;
                min-height: 320px;
            }
            
            .right-section {
                padding: 40px 30px;
            }

            .brand-text h1 {
                font-size: 1.4rem;
            }
            
            .recent-logins h3 {
                font-size: 0.9rem;
            }

            .login-header h2 {
                font-size: 2rem;
            }

            .avatar {
                width: 60px;
                height: 60px;
                font-size: 0.8rem;
            }
        }

        /* Enhanced hover effects */
        .form-group input:hover {
            border-color: #9CA3AF;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* Smooth page load animation */
        .container {
            opacity: 0;
            transform: translateY(30px);
            animation: pageLoad 0.8s ease-out forwards;
        }

        @keyframes pageLoad {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Floating elements animation */
        .logo {
            animation: logoFloat 6s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <div class="logo">
                <div class="logo-icon">
                    <img src="logo.png" alt="Logo">
                </div>
            </div>
            <div class="brand-text">
                <h1>RECENT LOGINS</h1>
            </div>
            <div class="recent-logins">
                <div class="login-avatars">
                    <div class="avatar" title="Admin" onclick="fillUserEmail('admin@tindahan.com')">
                        AD
                    </div>
                    <div class="avatar" title="Manager" onclick="fillUserEmail('manager@tindahan.com')">
                        MG  
                    </div>
                    <div class="avatar" title="Staff" onclick="fillUserEmail('staff@tindahan.com')">
                        ST
                    </div>
                </div>
            </div>
        </div>

        <div class="right-section">
            <div class="login-header">
                <h2>Welcome Back!</h2>
                <p>Sign in to access your dashboard and manage your tindahan.</p>
            </div>

            <form id="loginForm">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <span class="password-toggle" onclick="togglePassword()">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="remember-me">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <div class="forgot-password">
                        <a href="#" onclick="showForgotPassword()">Forgot Password?</a>
                    </div>
                </div>

                <button type="submit" name="login" class="btn btn-primary" id="loginBtn">
                    <span id="loginText">Sign In</span>
                    <div class="loading" id="loginLoading"></div>
                </button>

                <div class="divider">
                    <span>or</span>
                </div>

                <button type="button" class="btn btn-secondary" onclick="window.location.href='register.php'">
                    Create New Account
                </button>
            </form>
        </div>
    </div>

    <script>
        let isPasswordVisible = false;

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (isPasswordVisible) {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            } else {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                `;
            }
            isPasswordVisible = !isPasswordVisible;
        }

        function fillUserEmail(email) {
            const emailInput = document.getElementById('email');
            emailInput.value = email;
            emailInput.focus();
            
            // Add a subtle animation
            emailInput.style.transform = 'scale(1.02)';
            setTimeout(() => {
                emailInput.style.transform = '';
                document.getElementById('password').focus();
            }, 200);
        }

        function showForgotPassword() {
            // Create a more elegant modal instead of alert
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 1000;
                backdrop-filter: blur(10px);
            `;
            
            const content = document.createElement('div');
            content.style.cssText = `
                background: white;
                padding: 30px;
                border-radius: 16px;
                box-shadow: 0 24px 48px rgba(0, 0, 0, 0.2);
                text-align: center;
                max-width: 400px;
                margin: 20px;
            `;
            
            content.innerHTML = `
                <h3 style="color: #667eea; margin-bottom: 16px; font-family: Poppins, sans-serif;">Forgot Password?</h3>
                <p style="color: #6B7280; margin-bottom: 24px;">This feature will redirect you to the password recovery page.</p>
                <button onclick="this.closest('.modal').remove()" style="
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    border: none;
                    padding: 12px 24px;
                    border-radius: 8px;
                    cursor: pointer;
                    font-weight: 600;
                ">Got it</button>
            `;
            
            modal.className = 'modal';
            modal.appendChild(content);
            document.body.appendChild(modal);
            
            // Add click outside to close
            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.remove();
            });
        }

        // Enhanced form submission with better UX
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const loginBtn = document.getElementById('loginBtn');
            const loginText = document.getElementById('loginText');
            const loginLoading = document.getElementById('loginLoading');
            
            // Add ripple effect
            const ripple = document.createElement('span');
            const rect = loginBtn.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s linear;
                background-color: rgba(255, 255, 255, 0.3);
                width: ${size}px;
                height: ${size}px;
                left: ${rect.width / 2 - size / 2}px;
                top: ${rect.height / 2 - size / 2}px;
            `;
            
            loginBtn.appendChild(ripple);
            
            loginBtn.disabled = true;
            loginBtn.style.transform = 'scale(0.98)';
            loginText.style.display = 'none';
            loginLoading.style.display = 'block';
            
            setTimeout(() => {
                loginBtn.disabled = false;
                loginBtn.style.transform = '';
                loginText.style.display = 'block';
                loginLoading.style.display = 'none';
                ripple.remove();
            }, 2000);
        });

        // Enhanced avatar interactions
        document.querySelectorAll('.avatar').forEach((avatar, index) => {
            avatar.addEventListener('click', function() {
                // Create a more sophisticated click effect
                this.style.transform = 'scale(0.9) rotate(5deg)';
                
                // Add a temporary glow effect
                this.style.boxShadow = '0 0 20px rgba(255, 255, 255, 0.5)';
                
                setTimeout(() => {
                    this.style.transform = '';
                    this.style.boxShadow = '';
                }, 200);
            });

            // Add staggered animation on load
            avatar.style.opacity = '0';
            avatar.style.transform = 'translateY(20px)';
            setTimeout(() => {
                avatar.style.transition = 'all 0.5s ease';
                avatar.style.opacity = '1';
                avatar.style.transform = 'translateY(0)';
            }, 300 + (index * 100));
        });

        // Add input focus animations
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = '';
            });
        });

        // Add a subtle cursor trail effect (optional)
        document.addEventListener('mousemove', function(e) {
            const trail = document.createElement('div');
            trail.style.cssText = `
                position: fixed;
                width: 4px;
                height: 4px;
                background: linear-gradient(135deg, #667eea, #764ba2);
                border-radius: 50%;
                pointer-events: none;
                z-index: 9999;
                left: ${e.clientX - 2}px;
                top: ${e.clientY - 2}px;
                animation: fadeOut 1s ease-out forwards;
            `;
            
            document.body.appendChild(trail);
            setTimeout(() => trail.remove(), 1000);
        });

        // Add fadeOut animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeOut {
                0% { opacity: 0.8; transform: scale(1); }
                100% { opacity: 0; transform: scale(0); }
            }
            @keyframes ripple {
                to { transform: scale(4); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>