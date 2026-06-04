
<?php $__env->startSection('title', 'Tous les logs d\'activité'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-list me-2 text-primary"></i>
                                Tous les logs d'activité
                            </h4>
                            <p class="text-muted mb-0"><?php echo e($activities->count()); ?> activités enregistrées</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="<?php echo e(route('activity-log.index')); ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Vue paginée
                            </a>
                            <div class="dropdown">
                                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-download me-1"></i> Exporter
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?php echo e(route('activity-log.export', 'csv')); ?>">CSV</a></li>
                                    <li><a class="dropdown-item" href="<?php echo e(route('activity-log.export', 'json')); ?>">JSON</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th width="150">Date</th>
                                    <th width="200">Description</th>
                                    <th width="150">Utilisateur</th>
                                    <th width="100">Événement</th>
                                    <th width="100">Objet</th>
                                    <th width="80" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td>
                                            <small class="text-muted"><?php echo e($activity->created_at->format('d/m/Y H:i')); ?></small>
                                        </td>
                                        <td>
                                            <div class="fw-semibold"><?php echo e(Str::limit($activity->description, 50)); ?></div>
                                            <?php if($activity->properties->count() > 0): ?>
                                                <small class="text-muted"><?php echo e($activity->properties->count()); ?> propriétés</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($activity->causer): ?>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <?php echo e(substr($activity->causer->name, 0, 1)); ?>

                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold"><?php echo e($activity->causer->name); ?></div>
                                                        <small class="text-muted"><?php echo e($activity->causer->email); ?></small>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted fst-italic">Système</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                                $badgeClass = match($activity->event) {
                                                    'created' => 'badge-success',
                                                    'updated' => 'badge-warning',
                                                    'deleted' => 'badge-danger',
                                                    'restored' => 'badge-info',
                                                    default => 'badge-secondary'
                                                };
                                                $eventLabel = match($activity->event) {
                                                    'created' => 'Créé',
                                                    'updated' => 'Modifié',
                                                    'deleted' => 'Supprimé',
                                                    'restored' => 'Restauré',
                                                    default => ucfirst($activity->event)
                                                };
                                            ?>
                                            <span class="badge <?php echo e($badgeClass); ?>"><?php echo e($eventLabel); ?></span>
                                        </td>
                                        <td>
                                            <?php if($activity->subject): ?>
                                                <code><?php echo e(class_basename($activity->subject_type)); ?></code>
                                            <?php else: ?>
                                                <span class="text-muted fst-italic">Supprimé</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo e(route('activity-log.show', $activity->id)); ?>" 
                                                   class="btn btn-outline-primary"
                                                   data-bs-toggle="tooltip" title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                <h4>Aucune activité trouvée</h4>
                                                <p>Aucun log d'activité n'a été enregistré pour le moment.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Total : <strong><?php echo e($activities->count()); ?></strong> activités
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#cleanupModal">
                                <i class="fas fa-broom me-1"></i> Nettoyer les anciens logs
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de nettoyage -->
<div class="modal fade" id="cleanupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nettoyer les logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('activity-log.cleanup')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <p>Supprimer les logs plus anciens que :</p>
                    <div class="mb-3">
                        <label class="form-label">Nombre de jours</label>
                        <input type="number" name="days" class="form-control" min="1" max="365" value="30" required>
                        <small class="text-muted">Les logs plus anciens seront supprimés</small>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Attention : Cette action est irréversible.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Nettoyer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
    font-weight: 600;
}
.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
}
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Initialiser les tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltips.map(function(el) {
        return new bootstrap.Tooltip(el);
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP ELITEBOOK\Desktop\dev\HotelManagement\resources\views\activity\all.blade.php ENDPATH**/ ?>