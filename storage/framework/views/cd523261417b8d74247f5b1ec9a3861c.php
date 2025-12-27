<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/notifications.css')); ?>">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="icon" href="<?php echo e(asset('img/logo.png')); ?>">   
    <title><?php echo $__env->yieldContent('title'); ?></title>
    <style>
      #app-loader {
        position: fixed;
        inset: 0;
        display: grid;
        place-items: center;
        background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
        z-index: 9999;
        transition: opacity .35s ease, visibility .35s ease;
        overflow: hidden;
      }
      #app-loader::before {
        content: '';
        position: absolute;
        inset: 0;
        background: 
          radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
          radial-gradient(circle at 80% 70%, rgba(102, 126, 234, 0.15) 0%, transparent 50%);
        pointer-events: none;
      }
      #app-loader.hidden { opacity: 0; visibility: hidden; }
      .loader-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 24px;
        position: relative;
        z-index: 1;
      }
      .loader-spinner {
        width: 64px;
        height: 64px;
        position: relative;
        border-radius: 50%;
        background: conic-gradient(from 0deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        animation: spin 1.2s linear infinite;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .loader-spinner::before {
        content: '';
        position: absolute;
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2c3e50, #3498db);
      }
      .loader-spinner::after {
        content: '';
        position: absolute;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2c3e50, #3498db);
        box-shadow: inset 0 0 20px rgba(255, 255, 255, 0.1);
      }
      @keyframes spin { 
        to { transform: rotate(360deg); } 
      }
      .loader-title {
        color: #fff;
        font: 700 28px/1.2 'Segoe UI', system-ui, -apple-system, Arial, sans-serif;
        letter-spacing: 1px;
        text-align: center;
        text-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
        margin-bottom: 8px;
        animation: fadeInDown 0.6s ease-out;
      }
      .loader-text {
        color: rgba(255, 255, 255, 0.9);
        font: 600 16px/1.5 'Segoe UI', system-ui, -apple-system, Arial, sans-serif;
        letter-spacing: 0.5px;
        text-align: center;
        opacity: 0.95;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        animation: pulse 2s ease-in-out infinite;
      }
      @keyframes pulse {
        0%, 100% { opacity: 0.95; }
        50% { opacity: 0.7; }
      }
      @keyframes fadeInDown {
        from {
          opacity: 0;
          transform: translateY(-10px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }
    </style>
</head>
<body>
    <div id="app-loader" role="status" aria-live="polite" aria-busy="true">
      <div class="loader-content">
        <div class="loader-spinner"></div>
        <div class="loader-title">Ghorfa</div>
        <div class="loader-text">Loading…</div>
      </div>
    </div>
    <?php echo $__env->make('partials.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <?php echo $__env->yieldContent('content'); ?>
  
    <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.mobile-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php if(auth()->guard()->check()): ?>
    <script src="<?php echo e(asset('js/notifications.js')); ?>"></script>
    <?php endif; ?>
    <script>
      window.addEventListener('load', function () {
        const el = document.getElementById('app-loader');
        if (!el) return;
        setTimeout(() => el.classList.add('hidden'), 120);
        el.addEventListener('transitionend', () => el.remove());
      });

      window.addEventListener('beforeunload', function () {
        const el = document.getElementById('app-loader');
        if (!el) return;
        el.classList.remove('hidden');
      });
    </script>
</body>
</html><?php /**PATH C:\Ghorfa-Project\resources\views/layouts/app.blade.php ENDPATH**/ ?>