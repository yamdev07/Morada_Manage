

<?php $__env->startSection('title', 'Modifier le Profil'); ?>

<?php $__env->startSection('content'); ?>

<div class="container-fluid py-3">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">
            <i class="fas fa-user-edit text-primary me-2"></i>Modifier mon profil
        </h4>
        <a href="<?php echo e(route('profile.index')); ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Retour
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 pt-3">
                    <h6 class="fw-bold mb-0">
                        <i class="fas fa-user-cog text-primary me-2"></i>Informations du compte
                    </h6>
                </div>

                <div class="card-body">

                    <!-- Message succès -->
                    <?php if(session('success')): ?>
                        <div class="alert alert-success">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <!-- Formulaire -->
                    <form action="<?php echo e(route('profile.update')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <!-- Nom -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nom complet</label>
                            <input type="text" name="name" class="form-control" 
                                   value="<?php echo e(old('name', auth()->user()->name)); ?>" required>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <small class="text-danger"><?php echo e($message); ?></small>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Adresse Email</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?php echo e(old('email', auth()->user()->email)); ?>" required>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <small class="text-danger"><?php echo e($message); ?></small>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Mot de passe -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nouveau mot de passe 
                                <small class="text-muted">(optionnel)</small>
                            </label>
                            <input type="password" name="password" class="form-control" placeholder="Laisser vide pour ne pas changer">
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <small class="text-danger"><?php echo e($message); ?></small>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Confirmation -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Confirmer le mot de passe</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirmer le mot de passe">
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-hotel-primary">
                                <i class="fas fa-save me-1"></i>Enregistrer les modifications
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

</div>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('footer'); ?>
<!-- ========== FOOTER ========== -->
<div class="container-fluid mt-5">
    <div class="row align-items-center h-100 py-2">

        <div class="col-md-6">
            <div class="d-flex align-items-center">
                <i class="fas fa-hotel text-primary me-2"></i>
                <small class="text-muted">
                    © <?php echo e(date('Y')); ?> Laravel Hotel. All rights reserved.
                </small>
            </div>
        </div>

        <div class="col-md-6">
            <div class="d-flex justify-content-end align-items-center">
                <small class="text-muted me-3">
                    Version 2.0 - Laravel <?php echo e(app()->version()); ?>

                </small>
                <div class="d-flex gap-2">
                    <a href="https://www.facebook.com/tirajohw/" class="text-muted" data-bs-toggle="tooltip" title="Facebook">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="https://www.instagram.com/tirajoh/" class="text-muted" data-bs-toggle="tooltip" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://github.com/WailanTirajoh/" class="text-muted" data-bs-toggle="tooltip" title="GitHub">
                        <i class="fab fa-github"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('template.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP ELITEBOOK\Desktop\dev\HotelManagement\resources\views\profile\edit.blade.php ENDPATH**/ ?>