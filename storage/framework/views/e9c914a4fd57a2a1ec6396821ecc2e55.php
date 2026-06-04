

<?php $__env->startSection('title', $statusLabel . ' - Liste rapide'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 fw-bold text-dark">
                        <?php switch($status):
                            case ('dirty'): ?>
                                <i class="fas fa-broom text-danger me-2"></i>
                                <?php break; ?>
                            <?php case ('cleaning'): ?>
                                <i class="fas fa-spinner text-warning me-2"></i>
                                <?php break; ?>
                            <?php case ('clean'): ?>
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <?php break; ?>
                            <?php case ('occupied'): ?>
                                <i class="fas fa-users text-info me-2"></i>
                                <?php break; ?>
                            <?php case ('maintenance'): ?>
                                <i class="fas fa-tools text-purple me-2"></i>
                                <?php break; ?>
                        <?php endswitch; ?>
                        <?php echo e($statusLabel); ?> (<?php echo e($rooms->count()); ?>)
                    </h1>
                    <p class="text-muted mb-0">Liste rapide des chambres</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('housekeeping.index')); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Retour
                    </a>
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>
                        Imprimer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total chambres</h6>
                            <h3 class="fw-bold text-dark"><?php echo e($rooms->count()); ?></h3>
                        </div>
                        <div class="align-self-center">
                            <?php switch($status):
                                case ('dirty'): ?>
                                    <i class="fas fa-broom fa-2x text-danger opacity-50"></i>
                                    <?php break; ?>
                                <?php case ('cleaning'): ?>
                                    <i class="fas fa-spinner fa-2x text-warning opacity-50"></i>
                                    <?php break; ?>
                                <?php case ('clean'): ?>
                                    <i class="fas fa-check-circle fa-2x text-success opacity-50"></i>
                                    <?php break; ?>
                                <?php case ('occupied'): ?>
                                    <i class="fas fa-users fa-2x text-info opacity-50"></i>
                                    <?php break; ?>
                                <?php case ('maintenance'): ?>
                                    <i class="fas fa-tools fa-2x text-purple opacity-50"></i>
                                    <?php break; ?>
                            <?php endswitch; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Par type</h6>
                            <h5 class="fw-bold text-dark">
                                <?php echo e($rooms->groupBy('type.name')->count()); ?> types
                            </h5>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-layer-group fa-2x text-primary opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Dernière mise à jour</h6>
                            <h5 class="fw-bold text-dark"><?php echo e(now()->format('H:i:s')); ?></h5>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x text-secondary opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des chambres -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header 
                    <?php switch($status):
                        case ('dirty'): ?> bg-danger text-white <?php break; ?>
                        <?php case ('cleaning'): ?> bg-warning text-dark <?php break; ?>
                        <?php case ('clean'): ?> bg-success text-white <?php break; ?>
                        <?php case ('occupied'): ?> bg-info text-white <?php break; ?>
                        <?php case ('maintenance'): ?> bg-purple text-white <?php break; ?>
                    <?php endswitch; ?>">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-list me-2"></i>
                            <strong>Liste des chambres - <?php echo e($statusLabel); ?></strong>
                        </div>
                        <div class="text-light">
                            <small><?php echo e(now()->format('d/m/Y H:i')); ?></small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if($rooms->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3">Chambre</th>
                                        <th class="py-3">Type</th>
                                        <th class="py-3">Capacité</th>
                                        <th class="py-3">Prix/nuit</th>
                                        <th class="py-3">Dernière activité</th>
                                        <th class="py-3">Client actuel</th>
                                        <th class="py-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="room-badge 
                                                    <?php switch($status):
                                                        case ('dirty'): ?> bg-danger <?php break; ?>
                                                        <?php case ('cleaning'): ?> bg-warning <?php break; ?>
                                                        <?php case ('clean'): ?> bg-success <?php break; ?>
                                                        <?php case ('occupied'): ?> bg-info <?php break; ?>
                                                        <?php case ('maintenance'): ?> bg-purple <?php break; ?>
                                                    <?php endswitch; ?> me-3">
                                                    <?php echo e($room->number); ?>

                                                </div>
                                                <div>
                                                    <div class="fw-bold"><?php echo e($room->type->name ?? 'Standard'); ?></div>
                                                    <small class="text-muted">Étage: <?php echo e($room->floor ?? 'N/A'); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <?php echo e($room->type->name ?? 'Standard'); ?>

                                        </td>
                                        <td class="py-3">
                                            <i class="fas fa-users text-muted me-1"></i>
                                            <?php echo e($room->capacity); ?>

                                        </td>
                                        <td class="py-3">
                                            <span class="fw-bold text-success">
                                                <?php echo e(number_format($room->price, 0, ',', ' ')); ?> FCFA
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <?php if($room->updated_at): ?>
                                                <?php echo e($room->updated_at->diffForHumans()); ?>

                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3">
                                            <?php if($room->activeTransactions->count() > 0): ?>
                                                <?php
                                                    $currentTransaction = $room->activeTransactions->first();
                                                ?>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2">
                                                        <div class="avatar-title bg-light rounded-circle text-dark">
                                                            <?php echo e(substr($currentTransaction->customer->name, 0, 1)); ?>

                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold"><?php echo e($currentTransaction->customer->name); ?></div>
                                                        <small class="text-muted">
                                                            <?php echo e($currentTransaction->check_in->format('d/m')); ?> - <?php echo e($currentTransaction->check_out->format('d/m')); ?>

                                                        </small>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">Aucun</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3">
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('availability.room.detail', $room->id)); ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <?php switch($status):
                                                    case ('dirty'): ?>
                                                        <form action="<?php echo e(route('housekeeping.start-cleaning', $room->id)); ?>" 
                                                              method="POST" class="d-inline">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="btn btn-sm btn-warning">
                                                                <i class="fas fa-play"></i>
                                                            </button>
                                                        </form>
                                                        <?php break; ?>
                                                    
                                                    <?php case ('cleaning'): ?>
                                                        <form action="<?php echo e(route('housekeeping.mark-cleaned', $room->id)); ?>" 
                                                              method="POST" class="d-inline">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <?php break; ?>
                                                    
                                                    <?php case ('maintenance'): ?>
                                                        <form action="<?php echo e(route('housekeeping.end-maintenance', $room->id)); ?>" 
                                                              method="POST" class="d-inline">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <?php break; ?>
                                                <?php endswitch; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <?php switch($status):
                                case ('dirty'): ?>
                                    <i class="fas fa-check-circle fa-4x text-success mb-4"></i>
                                    <h4 class="text-dark mb-3">Aucune chambre à nettoyer</h4>
                                    <p class="text-muted mb-4">Toutes les chambres sont propres</p>
                                    <?php break; ?>
                                
                                <?php case ('cleaning'): ?>
                                    <i class="fas fa-clock fa-4x text-muted mb-4"></i>
                                    <h4 class="text-dark mb-3">Aucune chambre en nettoyage</h4>
                                    <p class="text-muted mb-4">Aucun nettoyage en cours</p>
                                    <?php break; ?>
                                
                                <?php case ('clean'): ?>
                                    <i class="fas fa-bed fa-4x text-muted mb-4"></i>
                                    <h4 class="text-dark mb-3">Aucune chambre nettoyée</h4>
                                    <p class="text-muted mb-4">Aucune chambre n'a été nettoyée récemment</p>
                                    <?php break; ?>
                                
                                <?php case ('occupied'): ?>
                                    <i class="fas fa-users fa-4x text-muted mb-4"></i>
                                    <h4 class="text-dark mb-3">Aucune chambre occupée</h4>
                                    <p class="text-muted mb-4">Toutes les chambres sont disponibles</p>
                                    <?php break; ?>
                                
                                <?php case ('maintenance'): ?>
                                    <i class="fas fa-check-circle fa-4x text-success mb-4"></i>
                                    <h4 class="text-dark mb-3">Aucune chambre en maintenance</h4>
                                    <p class="text-muted mb-4">Toutes les chambres sont opérationnelles</p>
                                    <?php break; ?>
                            <?php endswitch; ?>
                            
                            <a href="<?php echo e(route('housekeeping.index')); ?>" class="btn btn-primary">
                                <i class="fas fa-home me-2"></i>
                                Retour au dashboard
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Vue par tuiles -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <i class="fas fa-th me-2"></i>
                    <strong>Vue par tuiles (<?php echo e($rooms->count()); ?>)</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-3 mb-3">
                            <div class="card 
                                <?php switch($status):
                                    case ('dirty'): ?> border-danger <?php break; ?>
                                    <?php case ('cleaning'): ?> border-warning <?php break; ?>
                                    <?php case ('clean'): ?> border-success <?php break; ?>
                                    <?php case ('occupied'): ?> border-info <?php break; ?>
                                    <?php case ('maintenance'): ?> border-purple <?php break; ?>
                                <?php endswitch; ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="fw-bold mb-0">
                                            Chambre <?php echo e($room->number); ?>

                                        </h6>
                                        <span class="badge 
                                            <?php switch($status):
                                                case ('dirty'): ?> bg-danger <?php break; ?>
                                                <?php case ('cleaning'): ?> bg-warning <?php break; ?>
                                                <?php case ('clean'): ?> bg-success <?php break; ?>
                                                <?php case ('occupied'): ?> bg-info <?php break; ?>
                                                <?php case ('maintenance'): ?> bg-purple <?php break; ?>
                                            <?php endswitch; ?>">
                                            <?php echo e($room->type->name ?? 'Standard'); ?>

                                        </span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-users me-1"></i>
                                            Capacité: <?php echo e($room->capacity); ?>

                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="fas fa-money-bill me-1"></i>
                                            <?php echo e(number_format($room->price, 0, ',', ' ')); ?> FCFA
                                        </small>
                                        <?php if($room->last_cleaned_at): ?>
                                        <small class="text-muted d-block">
                                            <i class="fas fa-clock me-1"></i>
                                            <?php echo e($room->last_cleaned_at->diffForHumans()); ?>

                                        </small>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <a href="<?php echo e(route('availability.room.detail', $room->id)); ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>
                                            Détails
                                        </a>
                                        
                                        <?php switch($status):
                                            case ('dirty'): ?>
                                                <form action="<?php echo e(route('housekeeping.start-cleaning', $room->id)); ?>" 
                                                      method="POST">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-broom me-1"></i>
                                                        Démarrer
                                                    </button>
                                                </form>
                                                <?php break; ?>
                                            
                                            <?php case ('cleaning'): ?>
                                                <form action="<?php echo e(route('housekeeping.mark-cleaned', $room->id)); ?>" 
                                                      method="POST">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="fas fa-check me-1"></i>
                                                        Terminer
                                                    </button>
                                                </form>
                                                <?php break; ?>
                                            
                                            <?php case ('maintenance'): ?>
                                                <form action="<?php echo e(route('housekeeping.end-maintenance', $room->id)); ?>" 
                                                      method="POST">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="fas fa-check me-1"></i>
                                                        Terminer
                                                    </button>
                                                </form>
                                                <?php break; ?>
                                        <?php endswitch; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .text-purple {
        color: #6f42c1 !important;
    }
    
    .bg-purple {
        background-color: #6f42c1 !important;
    }
    
    .room-badge {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.1rem;
    }
    
    .avatar-sm {
        width: 32px;
        height: 32px;
    }
    
    .avatar-title {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.9rem;
    }
    
    .card-header.bg-danger,
    .card-header.bg-warning,
    .card-header.bg-success,
    .card-header.bg-info,
    .card-header.bg-purple,
    .card-header.bg-secondary {
        background: linear-gradient(135deg, var(--bs-danger), #dc2626) !important;
    }
    
    .card-header.bg-warning {
        background: linear-gradient(135deg, var(--bs-warning), #e0a800) !important;
    }
    
    .card-header.bg-success {
        background: linear-gradient(135deg, var(--bs-success), #198754) !important;
    }
    
    .card-header.bg-info {
        background: linear-gradient(135deg, var(--bs-info), #0aa2c0) !important;
    }
    
    .card-header.bg-purple {
        background: linear-gradient(135deg, var(--bs-purple), #6f42c1) !important;
    }
    
    .card-header.bg-secondary {
        background: linear-gradient(135deg, var(--bs-secondary), #64748b) !important;
    }
    
    .card.border-danger:hover {
        box-shadow: 0 0.5rem 1rem rgba(220, 53, 69, 0.15);
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
    
    .card.border-warning:hover {
        box-shadow: 0 0.5rem 1rem rgba(255, 193, 7, 0.15);
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
    
    .card.border-success:hover {
        box-shadow: 0 0.5rem 1rem rgba(25, 135, 84, 0.15);
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
    
    .card.border-info:hover {
        box-shadow: 0 0.5rem 1rem rgba(13, 202, 240, 0.15);
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
    
    .card.border-purple:hover {
        box-shadow: 0 0.5rem 1rem rgba(111, 66, 193, 0.15);
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Rafraîchissement automatique toutes les 60 secondes
    setTimeout(function() {
        window.location.reload();
    }, 60000);
    
    // Afficher le nombre de chambres
    console.log('<?php echo e($statusLabel); ?>: <?php echo e($rooms->count()); ?> chambres');
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('template.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP ELITEBOOK\Desktop\dev\HotelManagement\resources\views\housekeeping\quick-list.blade.php ENDPATH**/ ?>