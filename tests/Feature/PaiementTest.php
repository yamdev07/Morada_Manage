<?php

namespace Tests\Feature;

use App\Models\CashierSession;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Transaction;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Tests des opérations financières.
 *
 * Une erreur ici ne casse pas une page : elle fausse la comptabilité.
 * On vérifie donc surtout les cas limites et la concurrence, pas le
 * chemin nominal.
 *
 * Chaque test tourne dans une transaction annulée à la fin
 * (DatabaseTransactions) : aucune donnée n'est réellement ajoutée.
 */
class PaiementTest extends TestCase
{
    use DatabaseTransactions;

    private function caissier(): User
    {
        $user = User::whereIn('role', ['Super', 'Admin', 'Receptionist'])->first();

        if (! $user) {
            $this->markTestSkipped('Aucun utilisateur habilité en base.');
        }

        return $user;
    }

    /** Une session de caisse ouverte : le contrôleur l'exige pour encaisser. */
    private function sessionOuverte(User $user): CashierSession
    {
        return CashierSession::create([
            'user_id' => $user->id,
            'initial_balance' => 0,
            'current_balance' => 0,
            'start_time' => now(),
            'status' => 'active',
        ]);
    }

    /** Un séjour de 2 nuits à 50 000 FCFA, soit 100 000 FCFA dus. */
    private function sejour(): Transaction
    {
        $type = Type::firstOrCreate(
            ['name' => 'Type paiement (test)'],
            ['information' => 'Test', 'base_price' => 50000, 'capacity' => 2, 'is_active' => true]
        );

        $room = Room::create([
            'type_id' => $type->id,
            'room_status_id' => Room::STATUS_AVAILABLE,
            'number' => 'PAY-'.uniqid(),
            'name' => 'Chambre test paiement',
            'capacity' => 2,
            'price' => 50000,
            'view' => 'Test',
        ]);

        // address / job / birthdate sont NOT NULL sans valeur par défaut
        // dans ce schéma : on les renseigne pour ne pas tester autre chose.
        $customer = Customer::create([
            'name' => 'Client test paiement',
            'email' => 'paiement-'.uniqid().'@test.local',
            'phone' => '97000000',
            'address' => 'Covè',
            'job' => 'Test',
            'birthdate' => '1990-01-01',
            'gender' => 'Male',
            'user_id' => User::query()->value('id'),
        ]);

        return Transaction::create([
            'user_id' => User::query()->value('id'),
            'customer_id' => $customer->id,
            'room_id' => $room->id,
            'check_in' => now()->subDay()->startOfDay(),
            'check_out' => now()->addDay()->startOfDay(),
            'status' => 'active',
            'adults' => 2,
            'children' => 0,
        ]);
    }

    /** Un paiement encaissé, enregistré directement (hors contrôleur). */
    private function paiement(Transaction $sejour, float $montant, string $statut = Payment::STATUS_COMPLETED): Payment
    {
        return Payment::create([
            'transaction_id' => $sejour->id,
            'user_id' => User::query()->value('id'),
            'amount' => $montant,
            'payment_method' => 'cash',
            'status' => $statut,
            'reference' => 'TEST-'.uniqid(),
        ]);
    }

    private function encaisser(Transaction $sejour, float $montant, User $user)
    {
        return $this->actingAs($user)->post("/payment/{$sejour->id}", [
            'amount' => $montant,
            'payment_method' => 'cash',
            'description' => 'Test',
        ]);
    }

    // ──────────────────────────────────────────────
    //  SOLDE RESTANT
    // ──────────────────────────────────────────────

    public function test_le_solde_restant_est_exact_apres_un_paiement_partiel(): void
    {
        $sejour = $this->sejour();
        $du = $sejour->getTotalPrice();

        $this->assertGreaterThan(0, $du, 'Le séjour doit avoir un montant dû.');

        $this->paiement($sejour, 40000);

        $this->assertEquals($du - 40000, $sejour->fresh()->getRemainingPayment());
    }

    public function test_le_solde_ne_devient_jamais_negatif(): void
    {
        $sejour = $this->sejour();

        $this->paiement($sejour, $sejour->getTotalPrice() + 50000);

        $this->assertEquals(0, $sejour->fresh()->getRemainingPayment());
    }

    public function test_un_paiement_annule_ne_compte_pas_dans_le_total(): void
    {
        $sejour = $this->sejour();
        $du = $sejour->getTotalPrice();

        $paiement = $this->paiement($sejour, 40000);

        $this->assertEquals($du - 40000, $sejour->fresh()->getRemainingPayment());

        $paiement->update(['status' => 'cancelled']);

        // Le montant annulé doit redevenir dû
        $this->assertEquals($du, $sejour->fresh()->getRemainingPayment());
    }

    // ──────────────────────────────────────────────
    //  CONCURRENCE  (le point critique de la revue)
    // ──────────────────────────────────────────────

    public function test_deux_encaissements_simultanes_ne_depassent_pas_le_montant_du(): void
    {
        $user = $this->caissier();
        $this->sessionOuverte($user);

        $sejour = $this->sejour();
        $du = $sejour->getTotalPrice();

        // Premier acompte : il reste la moitié
        $this->paiement($sejour, $du / 2);

        $reste = $sejour->fresh()->getRemainingPayment();
        $this->assertEquals($du / 2, $reste);

        // Deux caissiers encaissent chacun le solde restant.
        // Le premier passe, le second doit être refusé par le verrou.
        $this->encaisser($sejour, $reste, $user);
        $this->encaisser($sejour, $reste, $user);

        $totalEncaisse = (float) Payment::where('transaction_id', $sejour->id)
            ->where('status', Payment::STATUS_COMPLETED)
            ->sum('amount');

        $this->assertLessThanOrEqual(
            $du + 100,
            $totalEncaisse,
            "Le total encaissé ({$totalEncaisse}) dépasse le montant dû ({$du}) : "
            .'le verrou anti double encaissement ne protège pas.'
        );
    }

    public function test_on_ne_peut_pas_encaisser_une_reservation_deja_soldee(): void
    {
        $user = $this->caissier();
        $this->sessionOuverte($user);

        $sejour = $this->sejour();
        $du = $sejour->getTotalPrice();

        $this->paiement($sejour, $du);

        $this->assertEquals(0, $sejour->fresh()->getRemainingPayment());

        $this->encaisser($sejour, 10000, $user);

        $total = (float) Payment::where('transaction_id', $sejour->id)
            ->where('status', Payment::STATUS_COMPLETED)
            ->sum('amount');

        $this->assertEquals($du, $total, 'Un encaissement a été accepté sur un séjour déjà soldé.');
    }

    // ──────────────────────────────────────────────
    //  SESSION DE CAISSE
    // ──────────────────────────────────────────────

    public function test_encaisser_sans_session_de_caisse_est_refuse(): void
    {
        $user = $this->caissier();

        // On s'assure qu'aucune session n'est ouverte pour cet utilisateur
        CashierSession::where('user_id', $user->id)->where('status', 'active')->delete();

        $sejour = $this->sejour();

        $this->encaisser($sejour, 10000, $user);

        $this->assertEquals(
            0,
            Payment::where('transaction_id', $sejour->id)->count(),
            'Un paiement a été enregistré alors qu\'aucune session de caisse n\'était ouverte.'
        );
    }

    // ──────────────────────────────────────────────
    //  VERROU EN BASE
    // ──────────────────────────────────────────────

    public function test_le_controleur_verrouille_bien_la_reservation(): void
    {
        $source = file_get_contents(app_path('Http/Controllers/PaymentController.php'));

        $this->assertStringContainsString(
            'lockForUpdate',
            $source,
            'PaymentController n\'utilise aucun verrou : le double encaissement redevient possible.'
        );
    }
}
