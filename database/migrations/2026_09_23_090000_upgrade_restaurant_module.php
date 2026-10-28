<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            if (! Schema::hasColumn('menus', 'is_available')) {
                $table->boolean('is_available')->default(true)->after('image');
            }
            if (! Schema::hasColumn('menus', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('restaurant_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('restaurant_orders', 'order_number')) {
                $table->string('order_number')->nullable()->unique()->after('id');
            }
            if (! Schema::hasColumn('restaurant_orders', 'transaction_id')) {
                $table->unsignedBigInteger('transaction_id')->nullable()->after('room_id');
            }
            if (! Schema::hasColumn('restaurant_orders', 'order_type')) {
                $table->string('order_type')->default('dining')->after('transaction_id');
            }
            if (! Schema::hasColumn('restaurant_orders', 'table_number')) {
                $table->string('table_number')->nullable()->after('order_type');
            }
            if (! Schema::hasColumn('restaurant_orders', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
            if (! Schema::hasColumn('restaurant_orders', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('notes');
            }
            if (! Schema::hasColumn('restaurant_orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_method');
            }
            if (! Schema::hasColumn('restaurant_orders', 'served_at')) {
                $table->timestamp('served_at')->nullable()->after('paid_at');
            }
            if (! Schema::hasColumn('restaurant_orders', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('served_at');
            }
        });

        // Numéroter les commandes déjà enregistrées
        DB::table('restaurant_orders')->whereNull('order_number')->orderBy('id')->get()
            ->each(function ($order) {
                DB::table('restaurant_orders')->where('id', $order->id)->update([
                    'order_number' => 'CMD-'.str_pad($order->id, 5, '0', STR_PAD_LEFT),
                ]);
            });

        Schema::table('restaurant_reservations', function (Blueprint $table) {
            if (! Schema::hasColumn('restaurant_reservations', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('restaurant_reservations', 'source')) {
                $table->string('source')->default('site')->after('status');
            }
            if (! Schema::hasColumn('restaurant_reservations', 'handled_by')) {
                $table->unsignedBigInteger('handled_by')->nullable()->after('source');
            }
        });

        // Élargir les statuts de réservation (enum MySQL)
        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE restaurant_reservations MODIFY status
                 ENUM('pending','confirmed','seated','completed','cancelled','no_show')
                 NOT NULL DEFAULT 'pending'"
            );
        }
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            if (Schema::hasColumn('menus', 'is_available')) {
                $table->dropColumn('is_available');
            }
            if (Schema::hasColumn('menus', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('restaurant_orders', function (Blueprint $table) {
            foreach (['transaction_id', 'order_type', 'table_number', 'notes', 'payment_method', 'paid_at', 'served_at', 'created_by'] as $column) {
                if (Schema::hasColumn('restaurant_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('restaurant_orders', function (Blueprint $table) {
            if (Schema::hasColumn('restaurant_orders', 'order_number')) {
                $table->dropUnique(['order_number']);
                $table->dropColumn('order_number');
            }
        });

        Schema::table('restaurant_reservations', function (Blueprint $table) {
            foreach (['email', 'source', 'handled_by'] as $column) {
                if (Schema::hasColumn('restaurant_reservations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
