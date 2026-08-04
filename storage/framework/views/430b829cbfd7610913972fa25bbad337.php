<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Account | College Music Distribution</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo"><i class="fa-solid fa-shield-halved"></i> Security Verification</div>
                <p class="auth-subtitle">Verify your email/phone address</p>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span><?php echo e(session('success')); ?></span>
                </div>
            <?php endif; ?>

            <?php if(session('warning')): ?>
                <div class="alert alert-warning">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span><?php echo e(session('warning')); ?></span>
                </div>
            <?php endif; ?>

            <?php if(session('debug_verification_code')): ?>
                <div style="background-color: rgba(99, 102, 241, 0.1); border: 1px dashed var(--accent); padding: 0.75rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; text-align: center; font-size: 0.9rem;">
                    <i class="fa-solid fa-bug" style="color: var(--accent); margin-right: 0.5rem;"></i>
                    <strong style="color: var(--text-primary);">Demo Code Helper:</strong> 
                    <span style="color: var(--accent); font-family: monospace; font-size: 1.1rem; font-weight: bold; margin-left: 0.5rem;"><?php echo e(session('debug_verification_code')); ?></span>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('verify')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                
                <div class="form-group">
                    <label class="form-label" for="email">Verify Email</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="e.g. john@example.com" value="<?php echo e(old('email', session('verify_email'))); ?>" required>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="code">Verification Code</label>
                    <input type="text" id="code" name="code" class="form-input" placeholder="6-digit code" style="text-align: center; letter-spacing: 0.5em; font-size: 1.25rem; font-weight: bold;" required>
                    <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fa-solid fa-circle-check"></i> Verify & Log In
                </button>
            </form>

            <div style="margin-top: 1.5rem; text-align: center; font-size: 0.9rem;">
                <span style="color: var(--text-secondary);">Incorrect registration details?</span>
                <a href="<?php echo e(route('register')); ?>" style="font-weight: 500; margin-left: 0.25rem;">Start Over</a>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\wamp64\www\College-Music\resources\views/auth/verify.blade.php ENDPATH**/ ?>