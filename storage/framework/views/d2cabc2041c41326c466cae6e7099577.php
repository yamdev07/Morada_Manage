<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Laravel')); ?> - Administration</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">

    <!-- Bootstrap CSS (CDN) - Rapide et fiable -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons (si utilisé) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Chart.js (pour les graphiques) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Vite - Assets compilés (si disponible) -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/sass/app.scss', 'resources/js/app.js']); ?>

    <!-- Fallback CSS personnalisé (si Vite ne charge pas) -->
    <link href="<?php echo e(asset('css/custom.css')); ?>" rel="stylesheet">

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <div id="app">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="<?php echo e(url('/')); ?>">
                    <?php echo e(config('app.name', 'Laravel')); ?>

                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="<?php echo e(__('Toggle navigation')); ?>">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <?php if(auth()->guard()->check()): ?>
                            <?php if(auth()->user()->role !== 'Customer'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('dashboard.index')); ?>">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-bed"></i> Hébergement
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?php echo e(route('room.index')); ?>">Chambres</a></li>
                                    <li><a class="dropdown-item" href="<?php echo e(route('type.index')); ?>">Types</a></li>
                                    <li><a class="dropdown-item" href="<?php echo e(route('roomstatus.index')); ?>">Statuts</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('transaction.index')); ?>">
                                    <i class="fas fa-exchange-alt"></i> Transactions
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('customer.index')); ?>">
                                    <i class="fas fa-users"></i> Clients
                                </a>
                            </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <?php if(auth()->guard()->guest()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('login.index')); ?>"><?php echo e(__('Login')); ?></a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="fas fa-user-circle"></i> <?php echo e(Auth::user()->name); ?>

                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="<?php echo e(route('profile.index')); ?>">
                                        <i class="fas fa-id-card"></i> Mon Profil
                                    </a>
                                    
                                    <?php if(auth()->user()->role === 'Super'): ?>
                                    <a class="dropdown-item" href="<?php echo e(route('user.index')); ?>">
                                        <i class="fas fa-user-cog"></i> Utilisateurs
                                    </a>
                                    <?php endif; ?>
                                    
                                    <div class="dropdown-divider"></div>
                                    
                                    <a class="dropdown-item" href="<?php echo e(route('logout')); ?>"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt"></i> <?php echo e(__('Logout')); ?>

                                    </a>

                                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                                        <?php echo csrf_field(); ?>
                                    </form>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="py-4">
            <div class="container">
                <?php echo $__env->make('partials.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </main>
    </div>

    <!-- jQuery (nécessaire pour certains plugins) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS Bundle (Popper included) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts spécifiques à la page -->
    <?php echo $__env->yieldPushContent('scripts'); ?>

    <!-- Initialisation Bootstrap -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Vérifier Bootstrap
            if (typeof bootstrap === 'undefined') {
                console.error('⚠️ Bootstrap non chargé, tentative de reload...');
                var script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js';
                script.onload = function() {
                    console.log('✅ Bootstrap rechargé');
                    initBootstrap();
                };
                document.head.appendChild(script);
            } else {
                console.log('✅ Bootstrap chargé');
                initBootstrap();
            }
        });

        function initBootstrap() {
            // Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function(popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });

            // Toasts
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            toastElList.map(function(toastEl) {
                return new bootstrap.Toast(toastEl);
            });

            // Dropdowns (auto-initialisés, mais on force)
            var dropdowns = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            dropdowns.map(function(dropdown) {
                return new bootstrap.Dropdown(dropdown);
            });

            console.log('🚀 Bootstrap initialisé');
        }
    </script>
</body>
</html><?php /**PATH C:\Users\HP ELITEBOOK\Desktop\dev\HotelManagement\resources\views\layouts\app.blade.php ENDPATH**/ ?>