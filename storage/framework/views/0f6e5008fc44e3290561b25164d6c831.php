
<?php $__env->startSection('title', 'Mes Réservations'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>
                <i class="fas fa-calendar-alt me-2"></i>
                <?php if($isCustomer): ?>
                    Mes Réservations
                <?php else: ?>
                    Toutes les Réservations
                <?php endif; ?>
            </h2>
        </div>
    </div>

    <!-- Si client, montrer seulement ses réservations -->
    <?php if($isCustomer): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Vous voyez ici uniquement vos propres réservations.
    </div>
    <?php endif; ?>

    <!-- Liste des réservations actives -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="fas fa-clock me-2"></i>Réservations Actives
                <span class="badge bg-primary"><?php echo e($transactions->count()); ?></span>
            </h5>
            
            <?php if($transactions->count() > 0): ?>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Chambre</th>
                                    <th>Arrivée</th>
                                    <th>Départ</th>
                                    <th>Nuits</th>
                                    <th>Total</th>
                                    <th>Payé</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <td><?php echo e($transaction->room->number); ?></td>
                                    <td><?php echo e(Helper::dateFormat($transaction->check_in)); ?></td>
                                    <td><?php echo e(Helper::dateFormat($transaction->check_out)); ?></td>
                                    <td><?php echo e($transaction->getDateDifferenceWithPlural()); ?></td>
                                    <td><?php echo e(Helper::formatCFA($transaction->getTotalPrice())); ?></td>
                                    <td><?php echo e(Helper::formatCFA($transaction->getTotalPayment())); ?></td>
                                    <td>
                                        <?php if($transaction->getTotalPrice() - $transaction->getTotalPayment() <= 0): ?>
                                            <span class="badge bg-success">Payé</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">En attente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('transaction.show.public', $transaction->id)); ?>" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Aucune réservation active.
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Historique des réservations -->
    <div class="row">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="fas fa-history me-2"></i>Anciennes Réservations
                <span class="badge bg-secondary"><?php echo e($transactionsExpired->count()); ?></span>
            </h5>
            
            <?php if($transactionsExpired->count() > 0): ?>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Chambre</th>
                                    <th>Arrivée</th>
                                    <th>Départ</th>
                                    <th>Nuits</th>
                                    <th>Total</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $transactionsExpired; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <td><?php echo e($transaction->room->number); ?></td>
                                    <td><?php echo e(Helper::dateFormat($transaction->check_in)); ?></td>
                                    <td><?php echo e(Helper::dateFormat($transaction->check_out)); ?></td>
                                    <td><?php echo e($transaction->getDateDifferenceWithPlural()); ?></td>
                                    <td><?php echo e(Helper::formatCFA($transaction->getTotalPrice())); ?></td>
                                    <td>
                                        <span class="badge bg-secondary">Terminée</span>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Aucune ancienne réservation.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP ELITEBOOK\Desktop\dev\HotelManagement\resources\views\transaction\my-reservations.blade.php ENDPATH**/ ?>