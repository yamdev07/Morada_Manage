<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\RestaurantOrder;
use App\Models\RestaurantOrderItem;
use App\Models\RestaurantReservation;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Tests du module Restaurant AVEC des données en base.
 *
 * Les smoke-tests ouvrent les pages sur une base vide, ce qui masque les
 * erreurs qui n'apparaissent qu'en présence de commandes (relations Eloquent
 * manquantes, colonnes inexistantes...). Ces tests créent donc de vraies
 * données, dans une transaction annulée à la fin (DatabaseTransactions).
 */
class RestaurantModuleTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        $admin = User::whereIn('role', ['Super', 'Admin'])->first();

        if (! $admin) {
            $this->markTestSkipped('Aucun utilisateur Super/Admin en base.');
        }

        return $admin;
    }

    private function category(): Category
    {
        return Category::firstOrCreate(
            ['slug' => 'plats-test'],
            ['name' => 'Plats (test)', 'sort_order' => 1]
        );
    }

    private function menu(array $attributes = []): Menu
    {
        return Menu::create(array_merge([
            'name' => 'Poisson braisé (test)',
            'category_id' => $this->category()->id,
            'price' => 4500,
            'description' => 'Plat de test',
            'is_available' => true,
        ], $attributes));
    }

    private function orderWithItems(array $attributes = []): RestaurantOrder
    {
        $menu = $this->menu();

        $order = RestaurantOrder::create(array_merge([
            'order_type' => 'dining',
            'table_number' => '7',
            'total' => 9000,
            'status' => 'pending',
        ], $attributes));

        RestaurantOrderItem::create([
            'order_id' => $order->id,
            'menu_id' => $menu->id,
            'quantity' => 2,
            'price' => $menu->price,
        ]);

        return $order->fresh('items');
    }

    // ──────────────────────────────────────────────
    //  PAGES AVEC DONNÉES
    // ──────────────────────────────────────────────

    public function test_le_hub_saffiche_avec_des_commandes_en_base(): void
    {
        $order = $this->orderWithItems();

        $response = $this->actingAs($this->admin())->get('/restaurant');

        $response->assertOk();
        $response->assertSee($order->order_number);
    }

    public function test_la_page_commandes_saffiche_avec_des_commandes_en_base(): void
    {
        $order = $this->orderWithItems();

        $response = $this->actingAs($this->admin())->get('/restaurant/orders');

        $response->assertOk();
        $response->assertSee($order->order_number);
    }

    public function test_la_carte_affiche_les_plats(): void
    {
        $menu = $this->menu(['name' => 'Amiwo de test']);

        $response = $this->actingAs($this->admin())->get('/restaurant/menus');

        $response->assertOk();
        $response->assertSee('Amiwo de test');
        $this->assertNotNull($menu->id);
    }

    public function test_le_detail_dune_commande_est_consultable(): void
    {
        $order = $this->orderWithItems();

        $response = $this->actingAs($this->admin())->get('/restaurant/orders/'.$order->id);

        $response->assertOk();
        $response->assertSee($order->order_number);
    }

    // ──────────────────────────────────────────────
    //  COMMANDES
    // ──────────────────────────────────────────────

    public function test_une_commande_calcule_son_total_depuis_la_carte(): void
    {
        $menu = $this->menu(['price' => 3500]);

        $response = $this->actingAs($this->admin())->post('/restaurant/orders', [
            'order_type' => 'dining',
            'table_number' => '3',
            'items' => json_encode([['menu_id' => $menu->id, 'quantity' => 3]]),
        ]);

        $response->assertRedirect(route('restaurant.orders'));

        $order = RestaurantOrder::latest('id')->first();

        $this->assertNotNull($order->order_number, 'La commande doit recevoir un numéro.');
        // Le total vient des prix en base, jamais du formulaire
        $this->assertEquals(10500, (float) $order->total);
        $this->assertEquals(1, $order->items->count());
    }

    public function test_une_commande_sans_article_est_refusee(): void
    {
        $this->actingAs($this->admin())
            ->post('/restaurant/orders', [
                'order_type' => 'dining',
                'items' => json_encode([]),
            ])
            ->assertSessionHas('error');
    }

    public function test_encaisser_une_commande_enregistre_le_paiement(): void
    {
        $order = $this->orderWithItems();

        $this->actingAs($this->admin())
            ->putJson('/restaurant/orders/'.$order->id, [
                'status' => 'paid',
                'payment_method' => 'cash',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $order->refresh();

        $this->assertEquals('paid', $order->status);
        $this->assertEquals('cash', $order->payment_method);
        $this->assertNotNull($order->paid_at);
    }

    public function test_une_commande_payee_ne_peut_plus_etre_annulee(): void
    {
        $order = $this->orderWithItems(['status' => 'paid']);

        $this->actingAs($this->admin())
            ->putJson('/restaurant/orders/'.$order->id.'/cancel')
            ->assertStatus(422);

        $this->assertEquals('paid', $order->fresh()->status);
    }

    // ──────────────────────────────────────────────
    //  CARTE
    // ──────────────────────────────────────────────

    public function test_un_plat_peut_etre_marque_epuise(): void
    {
        $menu = $this->menu();

        $this->actingAs($this->admin())
            ->patchJson('/restaurant/menus/'.$menu->id.'/availability')
            ->assertOk()
            ->assertJson(['is_available' => false]);

        $this->assertFalse((bool) $menu->fresh()->is_available);
    }

    public function test_un_plat_retire_conserve_lhistorique_des_commandes(): void
    {
        $order = $this->orderWithItems();
        $menu = $order->items->first()->menu;

        $this->actingAs($this->admin())
            ->deleteJson('/restaurant/menus/'.$menu->id)
            ->assertOk();

        $this->assertNotNull($menu->fresh()->deleted_at ?? Menu::withTrashed()->find($menu->id)->deleted_at);
        // La ligne de commande retrouve toujours son plat
        $this->assertEquals($menu->name, $order->fresh('items.menu')->items->first()->menu->name);
    }

    // ──────────────────────────────────────────────
    //  RÉSERVATIONS DE TABLE (SITE PUBLIC)
    // ──────────────────────────────────────────────

    public function test_une_reservation_du_site_est_enregistree_en_base(): void
    {
        $response = $this->postJson('/restaurant/reservation', [
            'name' => 'Client Test',
            'phone' => '97000000',
            'date' => now()->addDay()->format('Y-m-d'),
            'time' => '19:30',
            'persons' => 4,
            'table_type' => 'Terrasse',
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $reservation = RestaurantReservation::latest('id')->first();

        $this->assertEquals('Client Test', $reservation->name);
        $this->assertEquals('pending', $reservation->status);
        $this->assertEquals('site', $reservation->source);
    }

    public function test_une_reservation_peut_etre_confirmee_depuis_le_back_office(): void
    {
        $reservation = RestaurantReservation::create([
            'name' => 'Client Test 2',
            'phone' => '97000001',
            'reservation_date' => now()->addDay()->format('Y-m-d'),
            'reservation_time' => '20:00',
            'persons' => 2,
            'status' => 'pending',
            'source' => 'site',
        ]);

        $this->actingAs($this->admin())
            ->putJson('/restaurant/reservations/'.$reservation->id, ['status' => 'confirmed'])
            ->assertOk();

        $this->assertEquals('confirmed', $reservation->fresh()->status);
    }

    // ──────────────────────────────────────────────
    //  SITE PUBLIC
    // ──────────────────────────────────────────────

    public function test_la_carte_publique_affiche_les_plats_du_back_office(): void
    {
        $this->menu(['name' => 'Plat vitrine test', 'price' => 6000]);

        $response = $this->get('/restaurant-vitrine');

        $response->assertOk();
        $response->assertSee('Plat vitrine test');
        $response->assertSee('6 000 FCFA');
    }

    public function test_un_plat_epuise_napparait_pas_sur_le_site(): void
    {
        $this->menu(['name' => 'Plat epuise test', 'is_available' => false]);

        $this->get('/restaurant-vitrine')
            ->assertOk()
            ->assertDontSee('Plat epuise test');
    }

    public function test_un_plat_sans_categorie_reste_visible_sur_le_site(): void
    {
        $this->menu(['name' => 'Plat sans categorie test', 'category_id' => null]);

        $this->get('/restaurant-vitrine')
            ->assertOk()
            ->assertSee('Plat sans categorie test');
    }

    // ──────────────────────────────────────────────
    //  CATÉGORIES DE LA CARTE
    // ──────────────────────────────────────────────

    public function test_une_categorie_peut_etre_creee_et_ordonnee(): void
    {
        $this->actingAs($this->admin())
            ->post('/restaurant/categories', ['name' => 'Grillades test', 'sort_order' => 3])
            ->assertRedirect();

        $category = Category::where('name', 'Grillades test')->first();

        $this->assertNotNull($category);
        $this->assertEquals('grillades-test', $category->slug);
        $this->assertEquals(3, $category->sort_order);
    }

    public function test_supprimer_une_categorie_ne_supprime_pas_ses_plats(): void
    {
        $category = Category::create(['name' => 'Ephemere test', 'slug' => 'ephemere-test']);
        $menu = $this->menu(['name' => 'Plat orphelin test', 'category_id' => $category->id]);

        $this->actingAs($this->admin())
            ->delete('/restaurant/categories/'.$category->id)
            ->assertRedirect();

        $menu->refresh();

        $this->assertNull(Category::find($category->id));
        $this->assertNotNull($menu, 'Le plat ne doit pas être supprimé avec sa catégorie.');
        $this->assertNull($menu->category_id);
    }

    // ──────────────────────────────────────────────
    //  VENTES & FACTURE
    // ──────────────────────────────────────────────

    public function test_la_page_ventes_compte_les_commandes_payees(): void
    {
        $order = $this->orderWithItems(['status' => 'paid', 'total' => 9000, 'paid_at' => now()]);

        $response = $this->actingAs($this->admin())->get('/restaurant/sales');

        $response->assertOk();
        $response->assertSee($order->items->first()->menu->name);
    }

    public function test_une_commande_annulee_nest_pas_comptee_dans_le_ca(): void
    {
        $before = \App\Models\RestaurantOrder::whereIn('status', ['delivered', 'paid'])->sum('total');

        $this->orderWithItems(['status' => 'cancelled', 'total' => 50000]);

        $after = \App\Models\RestaurantOrder::whereIn('status', ['delivered', 'paid'])->sum('total');

        $this->assertEquals($before, $after);
    }

    public function test_la_facture_dune_commande_est_imprimable(): void
    {
        $order = $this->orderWithItems(['status' => 'paid', 'payment_method' => 'cash', 'paid_at' => now()]);

        $response = $this->actingAs($this->admin())->get('/restaurant/orders/'.$order->id.'/invoice');

        $response->assertOk();
        $response->assertSee($order->order_number);
        $response->assertSee('Facture');
    }

    public function test_le_nom_dun_client_de_passage_peut_etre_enregistre(): void
    {
        $order = $this->orderWithItems();

        $this->actingAs($this->admin())
            ->putJson('/restaurant/orders/'.$order->id.'/guest-name', ['guest_name' => 'Monsieur Kossi'])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertEquals('Monsieur Kossi', $order->fresh()->guest_name);
    }
}
