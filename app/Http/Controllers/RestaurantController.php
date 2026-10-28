<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Menu;
use App\Models\RestaurantOrder;
use App\Models\RestaurantOrderItem;
use App\Models\RestaurantReservation;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RestaurantController extends Controller
{
    /**
     * Hub Restaurant : vue d'ensemble du service (commandes, réservations, carte).
     */
    public function index()
    {
        $today = Carbon::today();

        $stats = [
            'orders_today' => RestaurantOrder::today()->count(),
            'open_orders' => RestaurantOrder::open()->count(),
            'revenue_today' => RestaurantOrder::where('status', 'paid')
                ->whereDate(DB::raw('COALESCE(paid_at, created_at)'), $today)
                ->sum('total'),
            'reservations_today' => RestaurantReservation::today()
                ->whereNotIn('status', ['cancelled', 'no_show'])
                ->count(),
            'pending_reservations' => RestaurantReservation::where('status', 'pending')
                ->whereDate('reservation_date', '>=', $today)
                ->count(),
            'menus_total' => Menu::count(),
            'menus_unavailable' => Menu::where('is_available', false)->count(),
        ];

        // Commandes en cours, les plus anciennes d'abord : ce sont celles qui attendent le plus
        $openOrders = RestaurantOrder::with(['customer', 'room', 'items.menu'])
            ->open()
            ->orderBy('created_at')
            ->get();

        $recentOrders = RestaurantOrder::with(['customer', 'room'])
            ->whereIn('status', ['delivered', 'paid', 'cancelled'])
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get();

        $todayReservations = RestaurantReservation::today()
            ->orderBy('reservation_time')
            ->get();

        $upcomingReservations = RestaurantReservation::upcoming()
            ->whereDate('reservation_date', '>', $today)
            ->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->limit(6)
            ->get();

        // Top des plats du mois : utile pour la carte et les approvisionnements
        $topMenus = RestaurantOrderItem::select(
            'menu_id',
            DB::raw('SUM(quantity) as quantity_sold'),
            DB::raw('SUM(quantity * price) as revenue')
        )
            ->whereHas('order', function ($query) {
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->where('status', '!=', 'cancelled');
            })
            ->groupBy('menu_id')
            ->orderByDesc('quantity_sold')
            ->limit(5)
            ->with('menu')
            ->get();

        return view('restaurant.index', compact(
            'stats', 'openOrders', 'recentOrders',
            'todayReservations', 'upcomingReservations', 'topMenus', 'today'
        ));
    }

    // ==================== CARTE / MENUS ====================

    public function menus(Request $request)
    {
        $menus = Menu::with('category')
            ->category($request->category)
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->search.'%')
                        ->orWhere('description', 'like', '%'.$request->search.'%');
                });
            })
            ->when($request->availability === 'available', fn ($q) => $q->where('is_available', true))
            ->when($request->availability === 'unavailable', fn ($q) => $q->where('is_available', false))
            ->orderBy('category_id')
            ->orderBy('name')
            ->paginate(12)
            ->appends($request->all());

        $categories = Category::withCount('menus')->ordered()->get();

        return view('restaurant.menus', compact('menus', 'categories'));
    }

    public function create()
    {
        $totalMenus = Menu::count();
        $lastAdded = Menu::latest()->first();
        $categories = Category::ordered()->get();
        $menu = null;

        return view('restaurant.menu-form', compact('menu', 'categories', 'totalMenus', 'lastAdded'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menus', 'public');
        }

        $validated['is_available'] = $request->boolean('is_available', true);

        Menu::create($validated);

        return redirect()->route('restaurant.menus')
            ->with('success', 'Plat ajouté à la carte.');
    }

    public function edit($id)
    {
        $menu = Menu::findOrFail($id);
        $totalMenus = Menu::count();
        $lastAdded = Menu::latest()->first();
        $categories = Category::ordered()->get();

        return view('restaurant.menu-form', compact('menu', 'categories', 'totalMenus', 'lastAdded'));
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $validated['image'] = $request->file('image')->store('menus', 'public');
        }

        $validated['is_available'] = $request->boolean('is_available');

        $menu->update($validated);

        return redirect()->route('restaurant.menus')
            ->with('success', 'Plat mis à jour.');
    }

    /** Basculer disponible / épuisé sans quitter la carte */
    public function toggleAvailability($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->update(['is_available' => ! $menu->is_available]);

        return response()->json([
            'success' => true,
            'is_available' => $menu->is_available,
            'message' => $menu->is_available ? 'Plat de nouveau disponible.' : 'Plat marqué comme épuisé.',
        ]);
    }

    /**
     * Suppression douce : l'historique des commandes reste lisible.
     */
    public function destroy(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Plat retiré de la carte.']);
        }

        return redirect()->route('restaurant.menus')->with('success', 'Plat retiré de la carte.');
    }

    // ==================== COMMANDES ====================

    public function orders(Request $request)
    {
        $orders = RestaurantOrder::with(['customer', 'room', 'items.menu'])
            ->status($request->status)
            ->when($request->filled('type'), fn ($q) => $q->where('order_type', $request->type))
            ->when($request->filled('date'), fn ($q) => $q->whereDate('created_at', $request->date))
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('order_number', 'like', '%'.$request->search.'%')
                        ->orWhere('table_number', 'like', '%'.$request->search.'%')
                        ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', '%'.$request->search.'%'));
                });
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->appends($request->all());

        $customers = Customer::orderBy('name')->get();
        $menus = Menu::available()->with('category')->orderBy('category_id')->orderBy('name')->get();
        $occupiedRooms = $this->occupiedRooms();

        $pendingOrders = RestaurantOrder::where('status', 'pending')->count();
        $preparingOrders = RestaurantOrder::where('status', 'preparing')->count();
        $deliveredOrders = RestaurantOrder::where('status', 'delivered')->count();
        $todayRevenue = RestaurantOrder::where('status', 'paid')
            ->whereDate(DB::raw('COALESCE(paid_at, created_at)'), today())
            ->sum('total');
        $monthlyOrders = RestaurantOrder::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('restaurant.orders', compact(
            'orders', 'customers', 'menus', 'occupiedRooms',
            'pendingOrders', 'preparingOrders', 'deliveredOrders',
            'todayRevenue', 'monthlyOrders'
        ));
    }

    public function showOrder(Request $request, $id)
    {
        $order = RestaurantOrder::with(['items.menu', 'customer', 'room', 'createdBy'])->findOrFail($id);

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view('restaurant.partials.order-details', compact('order'))->render(),
            ]);
        }

        return view('restaurant.order-show', compact('order'));
    }

    public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'room_id' => 'nullable|exists:rooms,id',
            'order_type' => 'required|in:'.implode(',', array_keys(RestaurantOrder::TYPES)),
            'guest_name' => 'nullable|string|max:255',
            'table_number' => 'nullable|string|max:20',
            'items' => 'required|json',
            'notes' => 'nullable|string|max:1000',
            'payment_method' => 'nullable|string|max:50',
        ]);

        $items = json_decode($validated['items'], true);

        if (! is_array($items) || count($items) === 0) {
            return back()->with('error', 'Ajoutez au moins un plat à la commande.')->withInput();
        }

        DB::beginTransaction();

        try {
            $order = RestaurantOrder::create([
                'customer_id' => $validated['customer_id'] ?? null,
                'guest_name' => $validated['guest_name'] ?? null,
                'room_id' => $validated['room_id'] ?? null,
                'transaction_id' => $this->activeTransactionId($validated['room_id'] ?? null),
                'order_type' => $validated['order_type'],
                'table_number' => $validated['table_number'] ?? null,
                'total' => 0,
                'notes' => $validated['notes'] ?? null,
                'payment_method' => $validated['payment_method'] ?? null,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            $total = 0;

            foreach ($items as $item) {
                $menu = Menu::findOrFail($item['menu_id']);
                $quantity = max(1, (int) ($item['quantity'] ?? 1));

                RestaurantOrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'quantity' => $quantity,
                    'price' => $menu->price, // prix figé au moment de la commande
                ]);

                $total += $menu->price * $quantity;
            }

            $order->update(['total' => $total]);

            DB::commit();

            return redirect()->route('restaurant.orders')
                ->with('success', 'Commande '.$order->order_number.' enregistrée.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Erreur lors de la création de la commande : '.$e->getMessage())
                ->withInput();
        }
    }

    public function updateOrder(Request $request, $id)
    {
        $order = RestaurantOrder::findOrFail($id);

        $request->validate([
            'status' => 'required|in:'.implode(',', array_keys(RestaurantOrder::STATUSES)),
            'payment_method' => 'nullable|string|max:50',
        ]);

        $attributes = ['status' => $request->status];

        if ($request->status === 'delivered' && ! $order->served_at) {
            $attributes['served_at'] = now();
        }

        if ($request->status === 'paid') {
            $attributes['paid_at'] = $order->paid_at ?? now();
            if ($request->filled('payment_method')) {
                $attributes['payment_method'] = $request->payment_method;
            }
        }

        $order->update($attributes);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => $order->status,
                'status_label' => $order->status_label,
                'message' => 'Commande '.$order->order_number.' : '.$order->status_label.'.',
            ]);
        }

        return back()->with('success', 'Statut mis à jour.');
    }

    public function cancelOrder(Request $request, $id)
    {
        $order = RestaurantOrder::findOrFail($id);

        if ($order->status === 'paid') {
            $message = 'Une commande déjà payée ne peut pas être annulée.';

            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $message], 422)
                : back()->with('error', $message);
        }

        $order->update(['status' => 'cancelled']);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Commande annulée.']);
        }

        return back()->with('success', 'Commande annulée.');
    }

    // ==================== RÉSERVATIONS DE TABLE ====================

    public function reservations(Request $request)
    {
        $reservations = RestaurantReservation::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('date'), fn ($q) => $q->whereDate('reservation_date', $request->date))
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->search.'%')
                        ->orWhere('phone', 'like', '%'.$request->search.'%');
                });
            })
            ->orderByDesc('reservation_date')
            ->orderBy('reservation_time')
            ->paginate(15)
            ->appends($request->all());

        $stats = [
            'today' => RestaurantReservation::today()->whereNotIn('status', ['cancelled', 'no_show'])->count(),
            'pending' => RestaurantReservation::where('status', 'pending')->count(),
            'confirmed' => RestaurantReservation::where('status', 'confirmed')
                ->whereDate('reservation_date', '>=', today())->count(),
            'covers_today' => RestaurantReservation::today()
                ->whereIn('status', ['confirmed', 'seated'])->sum('persons'),
        ];

        return view('restaurant.reservations', compact('reservations', 'stats'));
    }

    public function storeReservation(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'persons' => 'required|integer|min:1|max:50',
            'table_type' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Saisie au comptoir : la table est réservée de fait
        $validated['status'] = 'confirmed';
        $validated['source'] = 'admin';
        $validated['handled_by'] = Auth::id();

        RestaurantReservation::create($validated);

        return redirect()->route('restaurant.reservations')
            ->with('success', 'Réservation enregistrée.');
    }

    public function updateReservation(Request $request, $id)
    {
        $reservation = RestaurantReservation::findOrFail($id);

        $request->validate([
            'status' => 'required|in:'.implode(',', array_keys(RestaurantReservation::STATUSES)),
        ]);

        $reservation->update([
            'status' => $request->status,
            'handled_by' => Auth::id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => $reservation->status,
                'status_label' => $reservation->status_label,
                'message' => 'Réservation : '.strtolower($reservation->status_label).'.',
            ]);
        }

        return back()->with('success', 'Réservation mise à jour.');
    }

    /** Facture imprimable d'une commande */
    public function invoice($id)
    {
        $order = RestaurantOrder::with(['items.menu', 'customer', 'room', 'createdBy'])->findOrFail($id);

        return view('restaurant.invoice', compact('order'));
    }

    /** Nom d'un client de passage, modifiable après coup */
    public function updateGuestName(Request $request, $id)
    {
        $request->validate(['guest_name' => 'required|string|max:255']);

        $order = RestaurantOrder::findOrFail($id);
        $order->update(['guest_name' => trim($request->guest_name)]);

        return response()->json([
            'success' => true,
            'guest_name' => $order->guest_name,
            'message' => 'Nom du client enregistré.',
        ]);
    }

    // ==================== VENTES ====================

    /**
     * Chiffre d'affaires du restaurant. Seules les commandes servies ou
     * payées comptent : les commandes annulees ou en attente ne sont pas du CA.
     */
    public function sales()
    {
        $countable = ['delivered', 'paid'];

        $topItems = RestaurantOrderItem::with('menu')
            ->select(
                'menu_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(quantity * price) as total_revenue')
            )
            ->whereHas('order', fn ($q) => $q->whereIn('status', $countable))
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        $dailyRevenue = RestaurantOrder::whereIn('status', $countable)
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as nb_orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $monthlyRevenue = RestaurantOrder::whereIn('status', $countable)
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as nb_orders')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $salesByCategory = RestaurantOrderItem::with('menu.category')
            ->select(
                'menu_id',
                DB::raw('SUM(quantity * price) as category_revenue'),
                DB::raw('SUM(quantity) as category_qty')
            )
            ->whereHas('order', fn ($q) => $q->whereIn('status', $countable))
            ->groupBy('menu_id')
            ->get()
            ->groupBy(fn ($item) => $item->menu->category->name ?? 'Sans catégorie')
            ->map(fn ($group) => [
                'revenue' => $group->sum('category_revenue'),
                'qty' => $group->sum('category_qty'),
            ])
            ->sortByDesc('revenue');

        $totals = [
            'revenue' => RestaurantOrder::whereIn('status', $countable)->sum('total'),
            'orders' => RestaurantOrder::whereIn('status', $countable)->count(),
            'today' => RestaurantOrder::whereIn('status', $countable)->whereDate('created_at', today())->sum('total'),
            'month' => RestaurantOrder::whereIn('status', $countable)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total'),
        ];

        $totals['average'] = $totals['orders'] > 0 ? $totals['revenue'] / $totals['orders'] : 0;

        return view('restaurant.sales', compact(
            'topItems', 'dailyRevenue', 'monthlyRevenue', 'salesByCategory', 'totals'
        ));
    }

    // ==================== API AJAX ====================

    public function getCustomers()
    {
        $customers = Customer::with(['currentTransaction.room'])
            ->orderBy('name')
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'room_id' => $customer->currentTransaction->room_id ?? null,
                    'room_number' => $customer->currentTransaction->room->number ?? null,
                ];
            });

        return response()->json($customers);
    }

    public function getMenus(Request $request)
    {
        $menus = Menu::available()
            ->category($request->category)
            ->with('category:id,name')
            ->orderBy('category_id')
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'image', 'category_id']);

        return response()->json($menus);
    }

    /** Chambres actuellement occupées, pour le room service */
    private function occupiedRooms()
    {
        return Transaction::with(['room', 'customer'])
            ->where('status', 'active')
            ->get()
            ->filter(fn ($transaction) => $transaction->room !== null)
            ->map(function ($transaction) {
                return [
                    'transaction_id' => $transaction->id,
                    'room_id' => $transaction->room_id,
                    'room_number' => $transaction->room->number,
                    'customer_id' => $transaction->customer_id,
                    'customer_name' => $transaction->customer->name ?? 'Client',
                ];
            })
            ->sortBy('room_number')
            ->values();
    }

    /** Séjour en cours associé à une chambre, s'il existe */
    private function activeTransactionId($roomId)
    {
        if (! $roomId) {
            return null;
        }

        return Transaction::where('room_id', $roomId)
            ->where('status', 'active')
            ->value('id');
    }
}
