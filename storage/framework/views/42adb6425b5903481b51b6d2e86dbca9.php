
<?php $__env->startSection('title', 'Add Facility'); ?>
<?php $__env->startSection('content'); ?>

<div class="container mt-4">
    <h2>Add New Facility</h2>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php
        // Liste des icônes FontAwesome disponibles
        $icons = [
            'fas fa-wifi' => 'WiFi',
            'fas fa-swimming-pool' => 'Pool',
            'fas fa-dumbbell' => 'Gym',
            'fas fa-concierge-bell' => 'Service Bell',
            'fas fa-parking' => 'Parking',
            'fas fa-utensils' => 'Restaurant',
            'fas fa-spa' => 'Spa',
            'fas fa-tv' => 'TV',
            'fas fa-shuttle-van' => 'Shuttle',
            'fas fa-cocktail' => 'Bar',
        ];
    ?>

    <form action="<?php echo e(route('facility.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <div class="mb-3">
            <label for="name" class="form-label">Facility Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo e(old('name')); ?>" required>
        </div>

        <div class="mb-3">
            <label for="detail" class="form-label">Detail</label>
            <textarea name="detail" class="form-control" required><?php echo e(old('detail')); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="icon" class="form-label">Icon (select from list)</label>
            <select name="icon" class="form-select">
                <option value="">-- No Icon --</option>
                <?php $__currentLoopData = $icons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($class); ?>" <?php echo e(old('icon') == $class ? 'selected' : ''); ?>>
                        <?php echo e($label); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Save Facility</button>
        <a href="<?php echo e(route('facility.index')); ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('template.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP ELITEBOOK\Desktop\dev\HotelManagement\resources\views\facility\create.blade.php ENDPATH**/ ?>