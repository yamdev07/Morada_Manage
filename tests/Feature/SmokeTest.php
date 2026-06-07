<?php

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Smoke-tests en LECTURE SEULE : on ouvre les pages clés et on vérifie
 * qu'aucune ne renvoie d'erreur serveur (500). Aucune écriture en base
 * (pas de migration, pas de factory) -> sans danger pour les données de dev.
 *
 * Objectif : détecter instantanément une page cassée (vue manquante,
 * variable indéfinie, requête invalide, division par zéro, etc.).
 */
class SmokeTest extends TestCase
{
    /** Pages publiques (sans authentification). */
    public static function publicPages(): array
    {
        return [
            'accueil'            => ['/'],
            'chambres (vitrine)' => ['/chambres'],
            'services'           => ['/services'],
            'restaurant vitrine' => ['/restaurant-vitrine'],
            'réservation'        => ['/reservation'],
            'contact'            => ['/contact'],
            'login'              => ['/login'],
            'mot de passe oublié'=> ['/forgot-password'],
        ];
    }

    /** Pages de l'espace admin (nécessitent un compte Super/Admin). */
    public static function adminPages(): array
    {
        return [
            'dashboard'              => ['/dashboard'],
            'chambres'               => ['/room'],
            'créer chambre'          => ['/room/create'],
            'statuts de chambre'     => ['/roomstatus'],
            'types de chambre'       => ['/type'],
            'créer type'             => ['/type/create'],
            'clients'                => ['/customer'],
            'créer client'           => ['/customer/create'],
            'restaurant'             => ['/restaurant'],
            'créer menu'             => ['/restaurant/create'],
            'commandes restaurant'   => ['/restaurant/orders'],
            'paiements'              => ['/payment'],
            'paiements (liste)'      => ['/payments'],
            'rapports'               => ['/reports'],
            'housekeeping'           => ['/housekeeping'],
            'housekeeping dashboard' => ['/housekeeping/dashboard'],
            'housekeeping rapports'  => ['/housekeeping/reports'],
            'check-in'               => ['/checkin'],
            'check-in dashboard'     => ['/checkin-dashboard'],
            'check-in direct'        => ['/checkin/direct'],
            'caisse dashboard'       => ['/cashier/dashboard'],
            'caisse sessions'        => ['/cashier/sessions'],
            'caisse ouvrir session'  => ['/cashier/sessions/create'],
            'disponibilité dashboard'=> ['/availability/dashboard'],
            'calendrier'             => ['/availability/calendar'],
            'inventaire'             => ['/availability/inventory'],
            'recherche dispo'        => ['/availability/search'],
            'activité'               => ['/activity'],
            'activité statistiques'  => ['/activity/statistics'],
            'notifications'          => ['/notification'],
            'profil'                 => ['/profile'],
            'profil édition'         => ['/profile/edit'],
            'utilisateurs'           => ['/user'],
            'créer utilisateur'      => ['/user/create'],
            'équipements'            => ['/facility'],
            'transactions'           => ['/transaction'],
            'créer transaction'      => ['/transaction/create'],
            'mes réservations'       => ['/my-reservations'],
        ];
    }

    #[DataProvider('publicPages')]
    public function test_public_page_does_not_crash(string $uri): void
    {
        $status = $this->get($uri)->status();

        $this->assertLessThan(
            500,
            $status,
            "La page publique GET {$uri} a renvoyé une erreur serveur ({$status})."
        );
    }

    #[DataProvider('adminPages')]
    public function test_admin_page_does_not_crash(string $uri): void
    {
        $admin = User::whereIn('role', ['Super', 'Admin'])->first();

        if (! $admin) {
            $this->markTestSkipped('Aucun utilisateur Super/Admin en base pour tester l\'espace admin.');
        }

        $status = $this->actingAs($admin)->get($uri)->status();

        $this->assertLessThan(
            500,
            $status,
            "La page admin GET {$uri} a renvoyé une erreur serveur ({$status})."
        );
    }
}
