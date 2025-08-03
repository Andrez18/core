<?php
$title = 'Página no encontrada - Melon Mind';
include 'views/layouts/header.php';
?>

<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div style="text-align: center; color: white; max-width: 500px; padding: 40px;">
        <div style="font-size: 120px; font-weight: bold; margin-bottom: 20px; opacity: 0.8;">
            404
        </div>
        <h1 style="font-size: 32px; margin-bottom: 16px;">Página no encontrada</h1>
        <p style="font-size: 18px; margin-bottom: 32px; opacity: 0.9;">
            Lo sentimos, la página que buscas no existe o ha sido movida.
        </p>
        <div style="display: flex; gap: 16px; justify-content: center;">
            <button onclick="window.history.back()" 
                    style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); padding: 12px 24px; border-radius: 6px; cursor: pointer; font-size: 16px; transition: all 0.2s ease;">
                <i class="fas fa-arrow-left"></i>
                Volver
            </button>
            <button onclick="window.location.href='/dashboard'" 
                    style="background: white; color: #667eea; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: 500; transition: all 0.2s ease;">
                <i class="fas fa-home"></i>
                Ir al Dashboard
            </button>
        </div>
    </div>
</body>

<?php include 'views/layouts/footer.php'; ?>