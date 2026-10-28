<?php

/*
|--------------------------------------------------------------------------
| Morada Lodge — installation / mise à jour via navigateur
|--------------------------------------------------------------------------
| Pour les hébergements sans accès SSH (cPanel, FTP).
|
| 1. Envoyez les fichiers du projet par FTP
| 2. Visitez :  https://votre-domaine/setup.php?key=morada-setup-2026
| 3. SUPPRIMEZ ce fichier (un lien de suppression est proposé à la fin)
|
| Le script est SANS DANGER à relancer : il ne réinitialise aucun mot de
| passe et ne remplit la base que si elle est vide.
|--------------------------------------------------------------------------
*/

// Clé d'accès : définissez SETUP_KEY dans le .env (recommandé).
$SECRET = getenv('SETUP_KEY') ?: 'morada-setup-2026';

if (! hash_equals($SECRET, (string) ($_GET['key'] ?? ''))) {
    http_response_code(403);
    exit('Forbidden');
}

// Suppression du script, demandée depuis le lien de fin
if (isset($_GET['supprimer'])) {
    @unlink(__FILE__);
    header('Content-Type: text/plain; charset=utf-8');
    exit(is_file(__FILE__)
        ? "Suppression impossible : retirez setup.php à la main par FTP.\n"
        : "setup.php supprimé. Votre site est en ligne.\n");
}

header('Content-Type: text/plain; charset=utf-8');
@set_time_limit(300);

// La cle est validee : on peut afficher les erreurs sans rien exposer au public.
ini_set('display_errors', '1');
error_reporting(E_ALL);

/*
   Localisation de Laravel.
   L'arborescence varie selon l'hebergeur (public/, web/, web/ + private/...).
   Plutot que de supposer, on lit le chemin que index.php utilise deja, puis on
   essaie les emplacements habituels.
*/
$candidats = [];

$index = __DIR__.'/index.php';
if (is_file($index)) {
    $contenu = (string) file_get_contents($index);
    if (preg_match_all("#__DIR__\s*\.\s*'([^']*vendor/autoload\.php)'#", $contenu, $m)) {
        foreach ($m[1] as $trouve) {
            $candidats[] = __DIR__.$trouve;
        }
    }
}

foreach (['/../vendor/autoload.php', '/../private/vendor/autoload.php',
    '/vendor/autoload.php', '/../../vendor/autoload.php'] as $relatif) {
    $candidats[] = __DIR__.$relatif;
}

$autoload = null;
foreach ($candidats as $chemin) {
    if (is_file($chemin)) {
        $autoload = $chemin;
        break;
    }
}

if (! $autoload) {
    echo "ERREUR : impossible de trouver Laravel (vendor/autoload.php).

";
    echo "Dossier de ce script : ".__DIR__."

";
    echo "Emplacements essayes :
";
    foreach (array_unique($candidats) as $chemin) {
        echo '  '.$chemin."
";
    }
    echo "
Indiquez le bon chemin et je corrige le script.
";
    exit(1);
}

$racine = dirname($autoload, 2);   // .../vendor/autoload.php -> racine du projet

require $autoload;
$app = require_once $racine.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo '(Laravel trouve dans : '.$racine.")

";

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$avertissements = [];

echo "=====================================\n";
echo "  MORADA LODGE — installation\n";
echo "=====================================\n\n";

/* ─────────── 1. Caches ─────────── */
echo "1) Vidage des caches\n";
foreach (['config:clear', 'route:clear', 'view:clear', 'cache:clear'] as $commande) {
    try {
        Artisan::call($commande);
        echo "   $commande\n";
    } catch (\Throwable $e) {
        echo "   $commande : ignoré (".$e->getMessage().")\n";
    }
}

// Sans ça, les fichiers PHP mis à jour (routes, contrôleurs) restent figés
// alors que les vues Blade, elles, sont recompilées : la cause classique du
// « j'ai déployé mais rien ne change ».
if (function_exists('opcache_reset')) {
    @opcache_reset();
    echo "   OPcache réinitialisé\n";
}
echo "\n";

/* ─────────── 2. Base de données ─────────── */
echo "2) Connexion à la base\n";
try {
    DB::connection()->getPdo();
    echo '   OK : '.DB::connection()->getDatabaseName()."\n\n";
} catch (\Throwable $e) {
    echo "   ÉCHEC : ".$e->getMessage()."\n";
    echo "   Vérifiez DB_DATABASE / DB_USERNAME / DB_PASSWORD dans le .env.\n";
    exit(1);
}

echo "3) Migrations\n";
try {
    Artisan::call('migrate', ['--force' => true]);
    $sortie = trim(Artisan::output());
    echo '   '.str_replace("\n", "\n   ", $sortie ?: 'Rien à migrer')."\n\n";
} catch (\Throwable $e) {
    echo '   ÉCHEC : '.$e->getMessage()."\n\n";
    $avertissements[] = 'Les migrations ont échoué : la base est peut-être incomplète.';
}

/* ─────────── 4. Données de départ (jamais écrasées) ─────────── */
echo "4) Données de départ\n";

$seeders = [
    'menus' => ['Database\\Seeders\\MenuSeeder', 'la carte du restaurant'],
    'types' => ['Database\\Seeders\\MoradaRoomSeeder', 'les catégories de chambres'],
];

foreach ($seeders as $table => [$classe, $libelle]) {
    if (! Schema::hasTable($table)) {
        echo "   table '$table' absente, ignoré\n";
        continue;
    }

    if (DB::table($table)->count() > 0) {
        echo "   $libelle : déjà présentes, on ne touche à rien\n";
        continue;
    }

    try {
        Artisan::call('db:seed', ['--class' => $classe, '--force' => true]);
        echo "   $libelle : installées\n";
    } catch (\Throwable $e) {
        echo "   $libelle : échec (".$e->getMessage().")\n";
    }
}
echo "\n";

/* ─────────── 5. Compte administrateur ─────────── */
echo "5) Compte administrateur\n";
try {
    $nbAdmins = DB::table('users')->whereIn('role', ['Super', 'Admin'])->count();

    if ($nbAdmins > 0) {
        echo "   $nbAdmins compte(s) existant(s) — aucun mot de passe modifié\n\n";
    } else {
        $motDePasse = 'Morada@'.date('Y');
        DB::table('users')->insert([
            'name' => 'Direction',
            'email' => 'admin@moradalodge.com',
            'password' => Illuminate\Support\Facades\Hash::make($motDePasse),
            'role' => 'Super',
            'random_key' => Illuminate\Support\Str::random(60),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "   Compte créé : admin@moradalodge.com / $motDePasse\n";
        echo "   CHANGEZ CE MOT DE PASSE dès la première connexion.\n\n";
        $avertissements[] = 'Un compte administrateur a été créé avec un mot de passe par défaut.';
    }
} catch (\Throwable $e) {
    echo '   Impossible de vérifier : '.$e->getMessage()."\n\n";
}

/* ─────────── 6. Lien storage ─────────── */
echo "6) Lien public/storage\n";
$cible = realpath(__DIR__.'/../storage/app/public');
$lien = __DIR__.'/storage';

/*
   Prudence volontaire : on ne deplace et on ne supprime JAMAIS ce qui se
   trouve deja a cet emplacement. Certains systemes (Windows, FTP, montages)
   ne savent pas identifier un lien pourtant valide ; un script d'installation
   n'a pas a casser ce qu'il ne comprend pas. On teste donc le lien en lisant
   reellement a travers, et on ne cree que si la place est libre.
*/
if (! $cible) {
    echo "   storage/app/public introuvable\n\n";
    $avertissements[] = "Le dossier storage/app/public est absent.";
} elseif (file_exists($lien) || is_link($lien)) {
    $temoin = $cible.DIRECTORY_SEPARATOR.'.setup-test';
    @file_put_contents($temoin, 'ok');
    $traverse = @file_exists($lien.DIRECTORY_SEPARATOR.'.setup-test');
    @unlink($temoin);

    if ($traverse) {
        echo "   OK : le lien fonctionne\n\n";
    } else {
        echo "   Un element 'public/storage' existe mais ne mene pas aux fichiers.\n";
        echo "   Renommez-le a la main, puis relancez ce script (rien n'a ete modifie).\n\n";
        $avertissements[] = "public/storage existe mais ne pointe pas vers storage/app/public.";
    }
} else {
    if (@symlink($cible, $lien)) {
        echo "   Lien cree\n\n";
    } else {
        echo "   Creation impossible (hebergement sans symlink)\n";
        echo "   Solution : copiez le contenu de storage/app/public dans public/storage\n\n";
        $avertissements[] = "Le lien public/storage n'a pas pu etre cree.";
    }
}

/* ─────────── 7. Dossiers inscriptibles ─────────── */
echo "7) Dossiers inscriptibles\n";
foreach ([
    '../storage/framework/views',
    '../storage/framework/cache',
    '../storage/framework/sessions',
    '../storage/logs',
    '../bootstrap/cache',
    'img/morada',
] as $relatif) {
    $chemin = __DIR__.'/'.$relatif;

    if (! is_dir($chemin)) {
        @mkdir($chemin, 0775, true);
    }

    $nom = str_replace('../', '', $relatif);

    if (is_dir($chemin) && is_writable($chemin)) {
        echo "   $nom : OK\n";
    } else {
        echo "   $nom : NON INSCRIPTIBLE (chmod 775)\n";
        $avertissements[] = "Le dossier $nom n'est pas inscriptible.";
    }
}
echo "\n";

/* ─────────── 8. Fichiers du site vitrine ─────────── */
echo "8) Fichiers du nouveau site\n";
foreach ([
    'css/morada.css' => 'feuille de style',
    'js/morada.js' => 'script',
    'img/default/default-room.png' => 'plaque photo par défaut',
] as $fichier => $libelle) {
    if (is_file(__DIR__.'/'.$fichier)) {
        echo "   $libelle : présent\n";
    } else {
        echo "   $libelle : MANQUANT ($fichier)\n";
        $avertissements[] = "Fichier manquant : public/$fichier — le site s'affichera sans style.";
    }
}

$photos = glob(__DIR__.'/img/morada/*.{jpg,jpeg,png,webp}', GLOB_BRACE) ?: [];
echo '   photos du lodge : '.count($photos)." fichier(s)\n\n";

/* ─────────── 9. Contrôle final ─────────── */
echo "9) Contrôles\n";
echo '   environnement : '.config('app.env')."\n";

if (config('app.debug')) {
    echo "   APP_DEBUG=true — à passer à false en production\n";
    $avertissements[] = 'APP_DEBUG est à true : les erreurs exposent votre configuration.';
} else {
    echo "   APP_DEBUG : false\n";
}

echo '   URL : '.config('app.url')."\n\n";

/* ─────────── Résumé ─────────── */
echo "=====================================\n";

if ($avertissements) {
    echo "  TERMINÉ — à vérifier :\n\n";
    foreach ($avertissements as $i => $a) {
        echo '  '.($i + 1).". $a\n";
    }
} else {
    echo "  TERMINÉ — tout est en ordre\n";
}

echo "\n=====================================\n\n";
echo "Site       : ".config('app.url')."\n";
echo "Back-office: ".config('app.url')."/login\n\n";
echo "IMPORTANT — supprimez ce fichier maintenant :\n";
echo config('app.url').'/setup.php?key='.rawurlencode($SECRET)."&supprimer=1\n";
