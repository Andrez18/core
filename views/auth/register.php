<?php
$title = 'Crear Cuenta - Melon Mind';
include 'views/layouts/auth_header.php';
?>

<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px 0;">
    <div class="register-container" style="background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); width: 100%; max-width: 500px;">
        <div class="register-header" style="text-align: center; margin-bottom: 30px;">
            <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                <i class="fas fa-brain" style="font-size: 32px; color: #667eea; margin-right: 12px;"></i>
                <h1 style="margin: 0; color: #212529; font-size: 28px;">Melon Mind</h1>
            </div>
            <p style="color: #6c757d; margin: 0;">Crea tu cuenta profesional</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-error" style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/register" id="registerForm">
            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="full_name" style="display: block; margin-bottom: 6px; font-weight: 500; color: #495057;">Nombre Completo *</label>
                    <input type="text" id="full_name" name="full_name" required 
                           style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;"
                           value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="username" style="display: block; margin-bottom: 6px; font-weight: 500; color: #495057;">Usuario *</label>
                    <input type="text" id="username" name="username" required 
                           style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;"
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="email" style="display: block; margin-bottom: 6px; font-weight: 500; color: #495057;">Email *</label>
                <input type="email" id="email" name="email" required 
                       style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="password" style="display: block; margin-bottom: 6px; font-weight: 500; color: #495057;">Contraseña *</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" required minlength="6"
                               style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px; padding-right: 45px;">
                        <button type="button" onclick="togglePassword('password', 'passwordToggle1')" 
                                style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #6c757d; cursor: pointer;">
                            <i class="fas fa-eye" id="passwordToggle1"></i>
                        </button>
                    </div>
                    <small style="color: #6c757d; font-size: 12px;">Mínimo 6 caracteres</small>
                </div>
                <div class="form-group">
                    <label for="confirm_password" style="display: block; margin-bottom: 6px; font-weight: 500; color: #495057;">Confirmar Contraseña *</label>
                    <div style="position: relative;">
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6"
                               style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px; padding-right: 45px;">
                        <button type="button" onclick="togglePassword('confirm_password', 'passwordToggle2')" 
                                style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #6c757d; cursor: pointer;">
                            <i class="fas fa-eye" id="passwordToggle2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="password-strength" id="passwordStrength" style="margin-bottom: 20px; display: none;">
                <div style="display: flex; gap: 4px; margin-bottom: 8px;">
                    <div class="strength-bar" style="height: 4px; flex: 1; background: #e9ecef; border-radius: 2px;"></div>
                    <div class="strength-bar" style="height: 4px; flex: 1; background: #e9ecef; border-radius: 2px;"></div>
                    <div class="strength-bar" style="height: 4px; flex: 1; background: #e9ecef; border-radius: 2px;"></div>
                    <div class="strength-bar" style="height: 4px; flex: 1; background: #e9ecef; border-radius: 2px;"></div>
                </div>
                <small id="strengthText" style="color: #6c757d; font-size: 12px;">Ingresa una contraseña</small>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: flex; align-items: flex-start; cursor: pointer;">
                    <input type="checkbox" required style="margin-right: 8px; margin-top: 2px;">
                    <span style="font-size: 14px; color: #495057;">
                        Acepto los <a href="/terms" style="color: #667eea; text-decoration: none;">términos y condiciones</a> 
                        y la <a href="/privacy" style="color: #667eea; text-decoration: none;">política de privacidad</a>
                    </span>
                </label>
            </div>

            <button type="submit" class="btn-register" 
                    style="width: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 12px; border-radius: 6px; font-size: 16px; font-weight: 500; cursor: pointer; transition: transform 0.2s ease;">
                Crear Cuenta
            </button>
        </form>

        <div class="register-footer" style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e9ecef;">
            <p style="color: #6c757d; margin-bottom: 10px;">¿Ya tienes una cuenta?</p>
            <a href="/login" style="color: #667eea; text-decoration: none; font-weight: 500;">Iniciar sesión</a>
        </div>
    </div>

    <script>
        function togglePassword(inputId, toggleId) {
            const passwordInput = document.getElementById(inputId);
            const passwordToggle = document.getElementById(toggleId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordToggle.className = 'fas fa-eye';
            }
        }

        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('passwordStrength');
            const strengthBars = strengthDiv.querySelectorAll('.strength-bar');
            const strengthText = document.getElementById('strengthText');
            
            if (password.length === 0) {
                strengthDiv.style.display = 'none';
                return;
            }
            
            strengthDiv.style.display = 'block';
            
            let strength = 0;
            let feedback = [];
            
            // Length check
            if (password.length >= 8) strength++;
            else feedback.push('al menos 8 caracteres');
            
            // Uppercase check
            if (/[A-Z]/.test(password)) strength++;
            else feedback.push('una mayúscula');
            
            // Lowercase check
            if (/[a-z]/.test(password)) strength++;
            else feedback.push('una minúscula');
            
            // Number check
            if (/\d/.test(password)) strength++;
            else feedback.push('un número');
            
            // Reset bars
            strengthBars.forEach(bar => {
                bar.style.background = '#e9ecef';
            });
            
            // Color bars based on strength
            const colors = ['#dc3545', '#fd7e14', '#ffc107', '#28a745'];
            const labels = ['Muy débil', 'Débil', 'Buena', 'Fuerte'];
            
            for (let i = 0; i < strength; i++) {
                strengthBars[i].style.background = colors[strength - 1];
            }
            
            if (strength === 4) {
                strengthText.textContent = labels[3];
                strengthText.style.color = '#28a745';
            } else {
                strengthText.textContent = `${labels[strength]} - Necesita: ${feedback.join(', ')}`;
                strengthText.style.color = colors[strength];
            }
        });

        // Password match validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.style.borderColor = '#dc3545';
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.style.borderColor = '#dee2e6';
                this.setCustomValidity('');
            }
        });

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                showNotification('Las contraseñas no coinciden', 'error');
                return;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                showNotification('La contraseña debe tener al menos 6 caracteres', 'error');
                return;
            }
        });

        // Add hover effect to register button
        document.querySelector('.btn-register').addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-1px)';
        });

        document.querySelector('.btn-register').addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    </script>
</body>

<?php include 'views/layouts/footer.php'; ?>