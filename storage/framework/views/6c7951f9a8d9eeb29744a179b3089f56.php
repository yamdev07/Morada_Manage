


<?php $__env->startSection('title', 'Conflits de réservation'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 fw-bold text-dark">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Conflits de réservation
                    </h1>
                    <p class="text-muted mb-0">
                        Chambre <?php echo e($room->number); ?> - <?php echo e($room->type->name ?? 'Standard'); ?>

                    </p>
                </div>
                <a href="<?php echo e(route('availability.search')); ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Retour à la recherche
                </a>
            </div>
        </div>
    </div>

    <!-- Informations de la recherche -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Votre recherche</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">Arrivée</small>
                            <div class="fw-bold"><?php echo e(\Carbon\Carbon::parse($checkIn)->format('d/m/Y')); ?></div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Départ</small>
                            <div class="fw-bold"><?php echo e(\Carbon\Carbon::parse($checkOut)->format('d/m/Y')); ?></div>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted">Durée</small>
                            <div class="fw-bold"><?php echo e($nights); ?> nuit(s)</div>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted">Adultes</small>
                            <div class="fw-bold"><?php echo e($adults); ?></div>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted">Enfants</small>
                            <div class="fw-bold"><?php echo e($children); ?></div>
                        </div>
                    </div>
                    
                    <!-- Prix et informations chambre -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <small class="text-muted">Prix total pour cette période</small>
                            <div class="fw-bold text-success fs-4"><?php echo e($formattedSearchPrice); ?></div>
                            <small class="text-muted">(<?php echo e($formattedRoomPrice); ?>)</small>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted">Capacité</small>
                                    <div class="fw-bold"><?php echo e($roomCapacity); ?> personnes</div>
                                </div>
                                <div>
                                    <small class="text-muted">Statut chambre</small>
                                    <div class="fw-bold"><?php echo e($roomStatus); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm <?php echo e($conflicts->count() > 0 ? 'border-warning' : 'border-success'); ?>">
                <div class="card-body text-center">
                    <?php if($conflicts->count() > 0): ?>
                        <div class="text-warning mb-2">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                        <h5 class="fw-bold text-warning">Conflits détectés</h5>
                        <div class="display-4 fw-bold text-warning"><?php echo e($conflicts->count()); ?></div>
                        <p class="text-muted mb-0">réservation(s) en conflit</p>
                    <?php else: ?>
                        <div class="text-success mb-2">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                        <h5 class="fw-bold text-success">Disponible</h5>
                        <div class="display-4 fw-bold text-success">✓</div>
                        <p class="text-muted mb-0">Aucun conflit détecté</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Conflits de réservation -->
    <?php if($conflicts->count() > 0): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm border-warning">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-calendar-times me-2"></i>
                            <strong>Réservations en conflit (<?php echo e($conflicts->count()); ?>)</strong>
                        </div>
                        <span class="badge bg-danger">
                            <i class="fas fa-ban me-1"></i> Indisponible
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-info-circle fa-2x me-3 mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-2">Cette chambre n'est pas disponible pour votre période</h6>
                                <p class="mb-2">
                                    La chambre <?php echo e($room->number); ?> est déjà réservée pendant votre séjour.
                                    Voici les réservations existantes qui créent un conflit.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Client</th>
                                    <th>Arrivée</th>
                                    <th>Départ</th>
                                    <th>Durée</th>
                                    <th>Statut</th>
                                    <th>Prix</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $conflicts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo e($reservation->customer->name ?? 'Client inconnu'); ?></div>
                                        <small class="text-muted">
                                            <?php echo e($reservation->customer->phone ?? 'N/A'); ?>

                                        </small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">
                                            <?php echo e($reservation->check_in->format('d/m/Y')); ?>

                                        </div>
                                        <small class="text-muted"><?php echo e($reservation->check_in->format('H:i')); ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">
                                            <?php echo e($reservation->check_out->format('d/m/Y')); ?>

                                        </div>
                                        <small class="text-muted"><?php echo e($reservation->check_out->format('H:i')); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo e($reservation->check_in->diffInDays($reservation->check_out)); ?> nuit(s)
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($reservation->status == 'active'): ?>
                                            <span class="badge bg-success">En séjour</span>
                                        <?php elseif($reservation->status == 'reservation'): ?>
                                            <span class="badge bg-warning">Réservée</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?php echo e($reservation->status); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold">
                                            <?php echo e(number_format($reservation->total_price, 0, ',', ' ')); ?> FCFA
                                        </div>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('transaction.show', $reservation->id)); ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>
                                            Voir
                                        </a>
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
    <?php endif; ?>

    <!-- Visualisation calendrier simplifiée -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Visualisation de la période
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline-simple">
                        <?php
                            $startDate = \Carbon\Carbon::parse($checkIn);
                            $endDate = \Carbon\Carbon::parse($checkOut);
                            $searchDays = $startDate->diffInDays($endDate);
                        ?>
                        
                        <div class="d-flex align-items-center mb-3">
                            <div class="timeline-legend me-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="legend-color" style="background-color: #007bff; width: 20px; height: 10px; margin-right: 8px;"></div>
                                    <small class="text-muted">Votre recherche</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="legend-color" style="background-color: #dc3545; width: 20px; height: 10px; margin-right: 8px;"></div>
                                    <small class="text-muted">Réservation existante</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="timeline-visual">
                            <?php for($i = 0; $i < $searchDays; $i++): ?>
                                <?php
                                    $currentDate = $startDate->copy()->addDays($i);
                                    $isConflict = false;
                                    
                                    // Vérifier si cette date est en conflit
                                    foreach($conflicts as $reservation) {
                                        if ($currentDate->between(
                                            $reservation->check_in->copy()->startOfDay(),
                                            $reservation->check_out->copy()->subDay()->endOfDay()
                                        )) {
                                            $isConflict = true;
                                            break;
                                        }
                                    }
                                ?>
                                <div class="timeline-day <?php echo e($isConflict ? 'conflict' : 'available'); ?>"
                                     title="<?php echo e($currentDate->format('d/m/Y')); ?>">
                                    <?php echo e($currentDate->format('d')); ?>

                                </div>
                            <?php endfor; ?>
                        </div>
                        
                        <div class="mt-3 d-flex justify-content-between">
                            <small class="text-muted"><?php echo e($startDate->format('d/m/Y')); ?></small>
                            <small class="text-muted"><?php echo e($endDate->format('d/m/Y')); ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Solutions alternatives -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-lightbulb me-2 text-primary"></i>
                        Que faire maintenant ?
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body text-center">
                                    <div class="text-primary mb-3">
                                        <i class="fas fa-search fa-2x"></i>
                                    </div>
                                    <h6 class="fw-bold">Option 1 : Rechercher d'autres chambres</h6>
                                    <p class="text-muted small mb-3">
                                        Trouvez d'autres chambres disponibles pour vos dates
                                    </p>
                                    <a href="<?php echo e(route('availability.search')); ?>?check_in=<?php echo e($checkIn); ?>&check_out=<?php echo e($checkOut); ?>&adults=<?php echo e($adults); ?>&children=<?php echo e($children); ?>" 
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-bed me-2"></i>
                                        Voir les disponibilités
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card border-warning h-100">
                                <div class="card-body text-center">
                                    <div class="text-warning mb-3">
                                        <i class="fas fa-calendar-edit fa-2x"></i>
                                    </div>
                                    <h6 class="fw-bold">Option 2 : Modifier vos dates</h6>
                                    <p class="text-muted small mb-3">
                                        Changez les dates de votre séjour pour cette chambre
                                    </p>
                                    <a href="<?php echo e(route('availability.search')); ?>" 
                                       class="btn btn-outline-warning">
                                        <i class="fas fa-calendar me-2"></i>
                                        Modifier la recherche
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card border-info h-100">
                                <div class="card-body text-center">
                                    <div class="text-info mb-3">
                                        <i class="fas fa-clock fa-2x"></i>
                                    </div>
                                    <h6 class="fw-bold">Option 3 : Attendre la disponibilité</h6>
                                    <p class="text-muted small mb-3">
                                        Cette chambre sera disponible à partir de
                                        <?php
                                            $nextAvailable = $conflicts->sortBy('check_out')->first();
                                        ?>
                                        <?php if($nextAvailable): ?>
                                            <strong><?php echo e($nextAvailable->check_out->format('d/m/Y')); ?></strong>
                                        <?php else: ?>
                                            <strong>Prochainement</strong>
                                        <?php endif; ?>
                                    </p>
                                    <a href="<?php echo e(route('availability.calendar')); ?>?room_type=<?php echo e($room->type_id); ?>" 
                                       class="btn btn-outline-info">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        Voir le calendrier
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .timeline-simple {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
    }
    
    .timeline-visual {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        justify-content: center;
        margin: 20px 0;
    }
    
    .timeline-day {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        cursor: help;
    }
    
    .timeline-day.available {
        background-color: #007bff;
        color: white;
    }
    
    .timeline-day.conflict {
        background-color: #dc3545;
        color: white;
    }
    
    .timeline-legend {
        display: flex;
        gap: 20px;
    }
    
    .legend-color {
        border-radius: 2px;
    }
    
    .card.border-warning {
        border-width: 2px !important;
    }
    
    .card.border-success {
        border-width: 2px !important;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confirmation avant de quitter
        window.addEventListener('beforeunload', function(e) {
            if (window.location.pathname.includes('conflicts')) {
                // Don't prevent default - just a precaution
            }
        });
        
        // Animation des lignes du tableau
        const tableRows = document.querySelectorAll('table tbody tr');
        tableRows.forEach((row, index) => {
            row.style.opacity = '0';
            row.style.transform = 'translateX(-20px)';
            
            setTimeout(() => {
                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '1';
                row.style.transform = 'translateX(0)';
            }, index * 50);
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('template.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP ELITEBOOK\Desktop\dev\HotelManagement\resources\views\availability\voir-conflits.blade.php ENDPATH**/ ?>