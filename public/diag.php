<?php

/*
|--------------------------------------------------------------------------
| Morada Lodge — diagnostic serveur
|--------------------------------------------------------------------------
| Fichier volontairement minimal : aucune dépendance, aucun chargement de
| Laravel. S'il renvoie lui aussi une erreur 500, le problème vient du
| serveur (PHP, permissions, .htaccess) et non du code de l'application.
|
| À placer à côté de index.php, puis visiter :  https://votre-domaine/diag.php
| À SUPPRIMER une fois le diagnostic terminé.
|--------------------------------------------------------------------------
*/

header('Content-Type: text/plain; charset=utf-8');

echo "=== DIAGNOSTIC MORADA ===\n\n";

echo "PHP          : ".PHP_VERSION."\n";
echo "Dossier      : ".__DIR__."\n";
echo "Fichier      : ".__FILE__."\n";
echo "Utilisateur  : ".(function_exists('get_current_user') ? get_current_user() : '?')."\n";
echo "Memoire      : ".ini_get('memory_limit')."\n";
echo "display_err  : ".ini_get('display_errors')."\n\n";

echo "--- Laravel attendu dans le dossier parent ---\n";

$parent = dirname(__DIR__);
echo "Parent       : $parent\n";

foreach ([
    'vendor/autoload.php',
    'bootstrap/app.php',
    '.env',
    'artisan',
    'storage/framework/views',
] as $chemin) {
    $complet = $parent.'/'.$chemin;
    echo str_pad($chemin, 28).' : '.(file_exists($complet) ? 'TROUVE' : 'ABSENT')."\n";
}

echo "\n--- Au cas ou Laravel serait dans CE dossier ---\n";
foreach (['vendor/autoload.php', 'bootstrap/app.php', '.env', 'artisan'] as $chemin) {
    echo str_pad($chemin, 28).' : '.(file_exists(__DIR__.'/'.$chemin) ? 'TROUVE' : 'ABSENT')."\n";
}

echo "\n--- Fichiers du nouveau site ---\n";
foreach ([
    'index.php',
    'setup.php',
    'css/morada.css',
    'js/morada.js',
    'img/default/default-room.png',
    'img/morada',
    'storage',
] as $chemin) {
    $complet = __DIR__.'/'.$chemin;
    $etat = file_exists($complet)
        ? 'TROUVE'.(is_file($complet) ? ' ('.filesize($complet)." o)" : ' (dossier)')
        : 'ABSENT';
    echo str_pad($chemin, 28).' : '.$etat."\n";
}

echo "\n--- Extensions PHP requises ---\n";
foreach (['pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'fileinfo', 'gd'] as $ext) {
    echo str_pad($ext, 28).' : '.(extension_loaded($ext) ? 'ok' : 'MANQUANTE')."\n";
}

echo "\n--- Fonctions parfois desactivees ---\n";
$off = array_filter(array_map('trim', explode(',', (string) ini_get('disable_functions'))));
echo $off ? implode(', ', $off)."\n" : "aucune\n";

foreach (['symlink', 'proc_open', 'putenv', 'getenv'] as $f) {
    echo str_pad($f, 28).' : '.(function_exists($f) ? 'disponible' : 'DESACTIVEE')."\n";
}

echo "\n--- Taille de setup.php (doit faire ~9 Ko) ---\n";
$setup = __DIR__.'/setup.php';
if (is_file($setup)) {
    echo 'taille       : '.filesize($setup)." o\n";
    echo 'lisible      : '.(is_readable($setup) ? 'oui' : 'NON')."\n";
    echo 'permissions  : '.substr(sprintf('%o', fileperms($setup)), -4)."\n";
    $debut = (string) file_get_contents($setup, false, null, 0, 20);
    echo 'commence par : '.str_replace("\n", '\n', $debut)."\n";
} else {
    echo "setup.php absent de ce dossier\n";
}

echo "\n=== FIN ===\n";
echo "Supprimez diag.php une fois le probleme resolu.\n";
