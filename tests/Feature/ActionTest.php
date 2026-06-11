<?php

namespace Tests\Feature;

use App\Models\RoomStatus;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Tests d'ACTIONS (écriture) : connexion, création de chambre, validation.
 *
 * Chaque test s'exécute dans une transaction annulée à la fin
 * (DatabaseTransactions) -> aucune donnée n'est réellement ajoutée.
 * S'exécute sur la base de test isolée moradalodgedb_test (jamais la prod).
 */
class ActionTest extends TestCase
{
    use DatabaseTransactions;

    // ──────────────────────────────────────────────
    //  AUTHENTIFICATION
    // ──────────────────────────────────────────────

    public function test_connexion_avec_identifiants_valides(): void
    {
        // UserFactory définit le mot de passe à "admin".
        $user = User::factory()->create(['email' => 'action-login@test.local']);

        $response = $this->post('/login', [
            'email'    => 'action-login@test.local',
            'password' => 'admin',
        ]);

        $response->assertRedirect('dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_connexion_avec_mauvais_mot_de_passe_echoue(): void
    {
        User::factory()->create(['email' => 'action-bad@test.local']);

        $response = $this->post('/login', [
            'email'    => 'action-bad@test.local',
            'password' => 'mauvais-mot-de-passe',
        ]);

        $response->assertRedirect('login');
        $this->assertGuest();
    }

    // ──────────────────────────────────────────────
    //  CRÉATION DE CHAMBRE
    // ──────────────────────────────────────────────

    public function test_un_admin_peut_creer_une_chambre(): void
    {
        $admin  = User::factory()->create(['role' => 'Super']);
        $type   = Type::query()->first();
        $status = RoomStatus::query()->first();

        $this->assertNotNull($type, 'Aucun type en base de test (seed manquant).');
        $this->assertNotNull($status, 'Aucun statut en base de test (seed manquant).');

        // Numéro unique (<= 10 caractères) pour éviter toute collision.
        $number  = 'T'.substr((string) microtime(true), -7);
        $payload = [
            'type_id'        => $type->id,
            'room_status_id' => $status->id,
            'number'         => $number,
            'capacity'       => 2,
            'price'          => 75000,
            'view'           => 'Vue test',
        ];

        $response = $this->actingAs($admin)->post(route('room.store'), $payload);

        $response->assertRedirect(route('room.index'));
        $this->assertDatabaseHas('rooms', [
            'number' => $number,
            'price'  => 75000,
        ]);
    }

    public function test_creation_chambre_rejette_donnees_invalides(): void
    {
        $admin = User::factory()->create(['role' => 'Super']);

        $response = $this->actingAs($admin)->post(route('room.store'), [
            // tous les champs requis manquants
        ]);

        $response->assertSessionHasErrors([
            'type_id', 'room_status_id', 'number', 'capacity', 'price',
        ]);
    }
}
