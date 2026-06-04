<div class="order-details">
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-user me-2"></i> Informations client</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Nom:</th>
                            <td><?php echo e($order->customer_name ?? 'Non spécifié'); ?></td>
                        </tr>
                        <?php if($order->customer_phone): ?>
                        <tr>
                            <th>Téléphone:</th>
                            <td><?php echo e($order->customer_phone); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if($order->room_number): ?>
                        <tr>
                            <th>Chambre:</th>
                            <td>
                                <span class="badge bg-info">Chambre <?php echo e($order->room_number); ?></span>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th>Date commande:</th>
                            <td><?php echo e($order->created_at->format('d/m/Y H:i')); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i> Résumé de la commande</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Numéro:</th>
                            <td><strong>#<?php echo e(str_pad($order->id, 6, '0', STR_PAD_LEFT)); ?></strong></td>
                        </tr>
                        <tr>
                            <th>Statut:</th>
                            <td>
                                <?php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'preparing' => 'info',
                                        'delivered' => 'success',
                                        'paid' => 'primary',
                                        'cancelled' => 'danger'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'En attente',
                                        'preparing' => 'En préparation',
                                        'delivered' => 'Livré',
                                        'paid' => 'Payé',
                                        'cancelled' => 'Annulé'
                                    ];
                                ?>
                                <span class="badge bg-<?php echo e($statusColors[$order->status] ?? 'secondary'); ?>">
                                    <?php echo e($statusLabels[$order->status] ?? $order->status); ?>

                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Total:</th>
                            <td class="h5 text-primary"><?php echo e(number_format($order->total, 2)); ?> €</td>
                        </tr>
                        <?php if($order->payment_method): ?>
                        <tr>
                            <th>Paiement:</th>
                            <td>
                                <?php
                                    $paymentMethods = [
                                        'cash' => 'Espèces',
                                        'card' => 'Carte bancaire',
                                        'room_charge' => 'Frais de chambre',
                                        'online' => 'En ligne'
                                    ];
                                ?>
                                <?php echo e($paymentMethods[$order->payment_method] ?? $order->payment_method); ?>

                            </td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-list-alt me-2"></i> Détails des articles</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Menu</th>
                            <th class="text-center">Prix unitaire</th>
                            <th class="text-center">Quantité</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if($item->menu->image): ?>
                                    <img src="<?php echo e(asset('storage/' . $item->menu->image)); ?>" 
                                         alt="<?php echo e($item->menu->name); ?>" 
                                         class="rounded me-3" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div>
                                        <strong><?php echo e($item->menu->name); ?></strong>
                                        <?php if($item->menu->description): ?>
                                        <br><small class="text-muted"><?php echo e(Str::limit($item->menu->description, 50)); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center"><?php echo e(number_format($item->price, 2)); ?> €</td>
                            <td class="text-center"><?php echo e($item->quantity); ?></td>
                            <td class="text-center"><?php echo e(number_format($item->price * $item->quantity, 2)); ?> €</td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Sous-total:</th>
                            <th class="text-center"><?php echo e(number_format($order->items->sum(function($item) {
                                return $item->price * $item->quantity;
                            }), 2)); ?> €</th>
                        </tr>
                        <?php if($order->tax_rate > 0): ?>
                        <tr>
                            <th colspan="3" class="text-end">Taxes (<?php echo e($order->tax_rate); ?>%):</th>
                            <th class="text-center"><?php echo e(number_format($order->total * $order->tax_rate / 100, 2)); ?> €</th>
                        </tr>
                        <?php endif; ?>
                        <?php if($order->discount > 0): ?>
                        <tr>
                            <th colspan="3" class="text-end">Réduction:</th>
                            <th class="text-center text-danger">-<?php echo e(number_format($order->discount, 2)); ?> €</th>
                        </tr>
                        <?php endif; ?>
                        <tr class="table-active">
                            <th colspan="3" class="text-end">Total:</th>
                            <th class="text-center h5"><?php echo e(number_format($order->total, 2)); ?> €</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <?php if($order->notes): ?>
    <div class="card mt-3">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i> Notes</h6>
        </div>
        <div class="card-body">
            <p class="mb-0"><?php echo e($order->notes); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <div class="card mt-3">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-history me-2"></i> Historique</h6>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Commande créée</span>
                    <span class="text-muted"><?php echo e($order->created_at->format('d/m/Y H:i')); ?></span>
                </li>
                <?php if($order->updated_at != $order->created_at): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Dernière modification</span>
                    <span class="text-muted"><?php echo e($order->updated_at->format('d/m/Y H:i')); ?></span>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<style>
.order-details .card {
    border: 1px solid #e9ecef;
    margin-bottom: 1rem;
}

.order-details .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 0.75rem 1.25rem;
}

.order-details .table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.order-details img.rounded {
    border: 1px solid #dee2e6;
}
</style><?php /**PATH C:\Users\HP ELITEBOOK\Desktop\dev\HotelManagement\resources\views\restaurant\partials\order-details.blade.php ENDPATH**/ ?>