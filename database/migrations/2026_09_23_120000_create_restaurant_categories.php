<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Les catégories de la carte deviennent des données (et non une liste figée
 * dans le code) : le restaurant peut créer « Grillades », « Bar »... lui-même.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasColumn('menus', 'category_id')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable()->after('name')
                    ->constrained('categories')->nullOnDelete();
            });
        }

        // Reprendre les catégories déjà saisies en texte
        if (Schema::hasColumn('menus', 'category')) {
            $labels = [
                'entree' => 'Entrées',
                'plat' => 'Plats',
                'dessert' => 'Desserts',
                'boisson' => 'Boissons',
            ];

            $existing = DB::table('menus')->whereNotNull('category')->distinct()->pluck('category');

            foreach ($existing as $index => $value) {
                $name = $labels[$value] ?? Str::ucfirst($value);
                $slug = Str::slug($name);

                $categoryId = DB::table('categories')->where('slug', $slug)->value('id');

                if (! $categoryId) {
                    $categoryId = DB::table('categories')->insertGetId([
                        'name' => $name,
                        'slug' => $slug,
                        'sort_order' => $index,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('menus')->where('category', $value)->update(['category_id' => $categoryId]);
            }

            Schema::table('menus', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }

        // Nom libre pour un client de passage, sans fiche client
        if (! Schema::hasColumn('restaurant_orders', 'guest_name')) {
            Schema::table('restaurant_orders', function (Blueprint $table) {
                $table->string('guest_name')->nullable()->after('customer_id');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('menus', 'category')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->string('category')->nullable()->after('name');
            });

            DB::table('menus')->whereNotNull('category_id')->get()->each(function ($menu) {
                $slug = DB::table('categories')->where('id', $menu->category_id)->value('slug');
                DB::table('menus')->where('id', $menu->id)->update(['category' => $slug ?? 'plat']);
            });
        }

        if (Schema::hasColumn('menus', 'category_id')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            });
        }

        if (Schema::hasColumn('restaurant_orders', 'guest_name')) {
            Schema::table('restaurant_orders', function (Blueprint $table) {
                $table->dropColumn('guest_name');
            });
        }

        Schema::dropIfExists('categories');
    }
};
