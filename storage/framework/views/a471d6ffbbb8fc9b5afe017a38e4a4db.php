

<?php $__env->startSection('content'); ?>
    <!-- Header Section -->
    <div class="auth-header">
        <h3>Mot de passe oublié</h3>
        <p>Entrez votre email pour recevoir le lien de réinitialisation</p>
    </div>

    <!-- Status Message -->
    <?php if(session('status')): ?>
        <div class="alert-success">
            <i class="fas fa-check-circle"></i>
            <span><?php echo e(session('status')); ?></span>
        </div>
    <?php endif; ?>

    <!-- Reset Form -->
    <form method="POST" action="<?php echo e(route('password.email')); ?>" id="resetForm">
        <?php echo csrf_field(); ?>

        <!-- Email Field -->
        <div class="form-group">
            <label for="email" class="form-label">Adresse email</label>
            <input type="email" id="email" name="email"
                   class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                   value="<?php echo e(old('email')); ?>"
                   required autocomplete="email" autofocus
                   placeholder="Entrez votre adresse email">
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo e($message); ?>

                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-primary" id="submitBtn">
            <span class="btn-text">
                <i class="fas fa-paper-plane"></i>
                Envoyer le lien de réinitialisation
            </span>
            <span class="btn-loading d-none">
                <i class="fas fa-spinner fa-spin"></i>
                Envoi en cours...
            </span>
        </button>

        <!-- Back to Login -->
        <div class="auth-links">
            <a href="<?php echo e(route('login')); ?>" class="auth-link">
                <i class="fas fa-arrow-left"></i>
                Retour à la connexion
            </a>
        </div>
    </form>

    <!-- Help Section -->
    <div class="auth-help">
        <div class="help-item">
            <i class="fas fa-info-circle"></i>
            <span>Entrez l'adresse email associée à votre compte</span>
        </div>
        <div class="help-item">
            <i class="fas fa-clock"></i>
            <span>Le lien de réinitialisation expire dans 60 minutes</span>
        </div>
        <div class="help-item">
            <i class="fas fa-envelope"></i>
            <span>Vérifiez vos spams si vous ne recevez pas l'email</span>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('resetForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');
    
    // Animation des champs
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.borderColor = 'var(--primary)';
            this.style.boxShadow = '0 0 0 3px var(--primary-soft)';
        });
        
        input.addEventListener('blur', function() {
            this.style.borderColor = 'var(--gray-200)';
            this.style.boxShadow = 'none';
        });
    });
    
    // Soumission du formulaire
    form.addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        
        // Validation basique
        if (!email) {
            e.preventDefault();
            alert('Veuillez entrer votre adresse email');
            return;
        }
        
        // Validation du format email
        const emailRegex = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Veuillez entrer une adresse email valide');
            return;
        }
        
        // Afficher l'état de chargement
        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');
        submitBtn.disabled = true;
        
        // Réactiver après 10 secondes au cas où
        setTimeout(() => {
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
            submitBtn.disabled = false;
        }, 10000);
    });
    
    // Raccourci clavier : Entrée pour soumettre
    const emailInput = document.getElementById('email');
    emailInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            form.requestSubmit();
        }
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP ELITEBOOK\Desktop\dev\HotelManagement\resources\views/auth/passwords/email.blade.php ENDPATH**/ ?>