<?php
$title = 'Iniciar Sesión - Melon Mind';
include 'views/layouts/auth_header.php';
?>

<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="login-container" style="background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); width: 100%; max-width: 400px;">
        <div class="login-header" style="text-align: center; margin-bottom: 30px;">
            <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                <i class="fas fa-brain" style="font-size: 32px; color: #667eea; margin-right: 12px;"></i>
                <h1 style="margin: 0; color: #212529; font-size: 28px;">Melon Mind</h1>
            </div>
            <p style="color: #6c757d; margin: 0;">Inicia sesión en tu cuenta</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-error" style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="/login">
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="username" style="display: block; margin-bottom: 6px; font-weight: 500; color: #495057;">Usuario o Email</label>
                <input type="text" id="username" name="username" required 
                       style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 16px;"
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="password" style="display: block; margin-bottom: 6px; font-weight: 500; color: #495057;">Contraseña</label>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" required 
                           style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 16px; padding-right: 45px;">
                    <button type="button" onclick="togglePassword()" 
                            style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #6c757d; cursor: pointer;">
                        <i class="fas fa-eye" id="passwordToggle"></i>
                    </button>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" name="remember" style="margin-right: 8px;">
                    <span style="font-size: 14px; color: #495057;">Recordarme</span>
                </label>
            </div>

            <button type="submit" class="btn-login" 
                    style="width: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 12px; border-radius: 6px; font-size: 16px; font-weight: 500; cursor: pointer; transition: transform 0.2s ease;">
                <span id="loginText">Iniciar Sesión</span>
                <div id="loginSpinner" class="spinner" style="display: none;"></div>
            </button>
        </form>

        <div class="login-footer" style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e9ecef;">
            <p style="color: #6c757d; margin-bottom: 10px;">¿No tienes una cuenta?</p>
            <a href="/register" style="color: #667eea; text-decoration: none; font-weight: 500;">Crear cuenta</a>
        </div>

        <div class="demo-credentials" style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 6px; border: 1px solid #e9ecef;">
            <p style="margin: 0 0 8px 0; font-size: 12px; color: #6c757d; font-weight: 500;">Credenciales de prueba:</p>
            <p style="margin: 0; font-size: 12px; color: #495057;">
                <strong>Usuario:</strong> admin<br>
                <strong>Contraseña:</strong> admin123
            </p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordToggle.className = 'fas fa-eye';
            }
        }

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const loginText = document.getElementById('loginText');
            const loginSpinner = document.getElementById('loginSpinner');
            const submitBtn = this.querySelector('button[type="submit"]');
            
            // Show loading state
            loginText.style.display = 'none';
            loginSpinner.style.display = 'inline-block';
            submitBtn.disabled = true;
            
            const formData = new FormData(this);
            formData.append('ajax', '1');
            
            fetch('/login', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                } else {
                    showNotification(data.message || 'Error al iniciar sesión', 'error');
                    // Reset button state
                    loginText.style.display = 'inline';
                    loginSpinner.style.display = 'none';
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error de conexión', 'error');
                // Reset button state
                loginText.style.display = 'inline';
                loginSpinner.style.display = 'none';
                submitBtn.disabled = false;
            });
        });

        // Add hover effect to login button
        document.querySelector('.btn-login').addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-1px)';
        });

        document.querySelector('.btn-login').addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    </script>
</body>

<?php include 'views/layouts/footer.php'; ?>