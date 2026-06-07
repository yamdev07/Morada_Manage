
<?php $__env->startSection('title', 'Statistiques des Activités'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-chart-bar me-2 text-info"></i>
                                Statistiques des Activités
                            </h4>
                            <p class="text-muted mb-0">Analyse des logs d'activité</p>
                        </div>
                        <a href="<?php echo e(route('activity.index')); ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Retour au journal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques globales -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted mb-0">Total</h5>
                            <h2 class="mt-2"><?php echo e(number_format($stats['total'])); ?></h2>
                        </div>
                        <div class="avatar bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-history text-primary fa-2x"></i>
                        </div>
                    </div>
                    <p class="text-muted mb-0">Activités enregistrées</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted mb-0">Aujourd'hui</h5>
                            <h2 class="mt-2"><?php echo e(number_format($stats['today'])); ?></h2>
                        </div>
                        <div class="avatar bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-calendar-day text-success fa-2x"></i>
                        </div>
                    </div>
                    <p class="text-muted mb-0">Activités aujourd'hui</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted mb-0">Cette semaine</h5>
                            <h2 class="mt-2"><?php echo e(number_format($stats['this_week'])); ?></h2>
                        </div>
                        <div class="avatar bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-calendar-week text-warning fa-2x"></i>
                        </div>
                    </div>
                    <p class="text-muted mb-0">Activités cette semaine</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card border-start border-info border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted mb-0">Ce mois</h5>
                            <h2 class="mt-2"><?php echo e(number_format($stats['this_month'])); ?></h2>
                        </div>
                        <div class="avatar bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-calendar-alt text-info fa-2x"></i>
                        </div>
                    </div>
                    <p class="text-muted mb-0">Activités ce mois</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row">
        <!-- Par événement -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Répartition par événement</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Événement</th>
                                    <th>Nombre</th>
                                    <th>Pourcentage</th>
                                    <th>Barre</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $total = $stats['total'];
                                ?>
                                <?php $__currentLoopData = $stats['by_event']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                                        $color = match($event) {
                                            'created' => 'success',
                                            'updated' => 'warning',
                                            'deleted' => 'danger',
                                            'restored' => 'info',
                                            default => 'secondary'
                                        };
                                        $label = match($event) {
                                            'created' => 'Créations',
                                            'updated' => 'Modifications',
                                            'deleted' => 'Suppressions',
                                            'restored' => 'Restaurations',
                                            default => ucfirst($event)
                                        };
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-<?php echo e($color); ?>"><?php echo e($label); ?></span>
                                        </td>
                                        <td><?php echo e(number_format($count)); ?></td>
                                        <td><?php echo e($percentage); ?>%</td>
                                        <td>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-<?php echo e($color); ?>" role="progressbar" 
                                                     style="width: <?php echo e($percentage); ?>%;"></div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 10 des utilisateurs -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Top 10 des utilisateurs actifs</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Utilisateur</th>
                                    <th>Actions</th>
                                    <th>Pourcentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $stats['by_user']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userStat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $percentage = $total > 0 ? round(($userStat->count / $total) * 100, 1) : 0;
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <?php echo e(substr($userStat->causer?->name ?? 'Système', 0, 1)); ?>

                                                </div>
                                                <span><?php echo e($userStat->causer?->name ?? 'Système'); ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo e(number_format($userStat->count)); ?></td>
                                        <td>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-primary" role="progressbar" 
                                                     style="width: <?php echo e($percentage); ?>%;"></div>
                                            </div>
                                            <small class="text-muted"><?php echo e($percentage); ?>%</small>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activités par modèle -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Activités par type de modèle</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Modèle</th>
                                    <th>Actions</th>
                                    <th>Pourcentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $stats['by_model']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modelStat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $percentage = $total > 0 ? round(($modelStat->count / $total) * 100, 1) : 0;
                                        $modelName = class_basename($modelStat->subject_type);
                                    ?>
                                    <tr>
                                        <td>
                                            <code><?php echo e($modelName); ?></code>
                                        </td>
                                        <td><?php echo e(number_format($modelStat->count)); ?></td>
                                        <td>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-info" role="progressbar" 
                                                     style="width: <?php echo e($percentage); ?>%;"></div>
                                            </div>
                                            <small class="text-muted"><?php echo e($percentage); ?>%</small>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques journalières -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Activités récentes</h5>
                        <span class="text-muted small">Derniers 7 jours</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Jour</th>
                                    <th>Activités</th>
                                    <th>Tendance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $days = [];
                                    for ($i = 6; $i >= 0; $i--) {
                                        $date = now()->subDays($i);
                                        $days[$date->format('Y-m-d')] = 0;
                                    }
                                    
                                    // Comptez les activités pour chaque jour
                                    $recentActivities = \Spatie\Activitylog\Models\Activity::whereDate('created_at', '>=', now()->subDays(6))
                                        ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                                        ->groupBy('date')
                                        ->pluck('count', 'date');
                                    
                                    // Fusionnez avec les jours
                                    foreach ($recentActivities as $date => $count) {
                                        $days[$date] = $count;
                                    }
                                ?>
                                
                                <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $dayName = \Carbon\Carbon::parse($date)->translatedFormat('l');
                                        $formattedDate = \Carbon\Carbon::parse($date)->format('d/m');
                                        $isToday = $date == now()->format('Y-m-d');
                                    ?>
                                    <tr class="<?php echo e($isToday ? 'table-active' : ''); ?>">
                                        <td>
                                            <?php echo e($dayName); ?>

                                            <small class="text-muted d-block"><?php echo e($formattedDate); ?></small>
                                        </td>
                                        <td>
                                            <span class="fw-semibold"><?php echo e($count); ?></span>
                                        </td>
                                        <td>
                                            <?php if($count > 0): ?>
                                                <div class="progress" style="height: 6px;">
                                                    <?php
                                                        $maxCount = max($days);
                                                        $width = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                                                    ?>
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                         style="width: <?php echo e($width); ?>%;"></div>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted small">Aucune</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Résumé -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Résumé des statistiques</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Moyenne quotidienne</span>
                                    <span class="badge bg-primary rounded-pill">
                                        <?php echo e($total > 0 ? number_format($total / 30, 1) : 0); ?>/jour
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Hier</span>
                                    <span class="badge bg-secondary rounded-pill"><?php echo e($stats['yesterday']); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Utilisateurs actifs (30j)</span>
                                    <span class="badge bg-success rounded-pill"><?php echo e($stats['by_user']->count()); ?></span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Modèles suivis</span>
                                    <span class="badge bg-info rounded-pill"><?php echo e($stats['by_model']->count()); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Taux de création</span>
                                    <span class="badge bg-warning rounded-pill">
                                        <?php echo e($total > 0 ? number_format(($stats['by_event']['created'] ?? 0) / $total * 100, 1) : 0); ?>%
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Taux de modification</span>
                                    <span class="badge bg-warning rounded-pill">
                                        <?php echo e($total > 0 ? number_format(($stats['by_event']['updated'] ?? 0) / $total * 100, 1) : 0); ?>%
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="<?php echo e(route('activity.export', 'csv')); ?>" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-download me-1"></i> Exporter les statistiques
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.avatar {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
    font-weight: 600;
}
.card {
    border-radius: 10px;
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}
.border-start {
    border-left-width: 4px !important;
}
.progress {
    border-radius: 3px;
}
.list-group-item {
    border: none;
    padding: 0.75rem 0;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Simple animation for counters
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('h2');
    counters.forEach(counter => {
        const target = parseInt(counter.textContent.replace(/,/g, ''));
        if (!isNaN(target)) {
            animateCounter(counter, target);
        }
    });
});

function animateCounter(element, target) {
    let current = 0;
    const increment = target / 100;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target.toLocaleString();
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current).toLocaleString();
        }
    }, 20);
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP ELITEBOOK\Desktop\dev\HotelManagement\resources\views/activity/statistics.blade.php ENDPATH**/ ?>