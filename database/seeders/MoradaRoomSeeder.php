<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Type;
use Illuminate\Database\Seeder;

/**
 * Catégories et chambres de Morada Lodge.
 *
 * Reprend l'offre réelle qui figurait en dur dans l'ancienne page d'accueil
 * (Bungalows Majestueux, Chambre Confort, Suite Présidentielle) pour la faire
 * vivre en base : c'est désormais le back-office qui pilote le site.
 *
 * Idempotent : relancé, il met à jour sans créer de doublon.
 */
class MoradaRoomSeeder extends Seeder
{
    public function run(): void
    {
        $catalogue = [
            [
                'name' => 'Chambre Confort',
                'information' => "Élégantes et pratiques, nos chambres Confort sont parfaites pour un séjour "
                    ."alliant simplicité et raffinement à prix accessible. Lit queen size, climatisation "
                    ."et salle de bain privative.",
                'base_price' => 20000,
                'capacity' => 2,
                'amenities' => ['Lit queen size confortable', 'Wi-Fi haut débit gratuit', 'Minibar et télévision HD'],
                'bed_type' => 'queen size',
                'bed_count' => 1,
                'size' => '22',
                'sort_order' => 1,
                'rooms' => [
                    ['number' => '101', 'name' => 'Confort 101', 'capacity' => 2, 'price' => 20000, 'view' => 'Vue cour intérieure'],
                    ['number' => '102', 'name' => 'Confort 102', 'capacity' => 2, 'price' => 20000, 'view' => 'Vue cour intérieure'],
                    ['number' => '103', 'name' => 'Confort 103', 'capacity' => 1, 'price' => 20000, 'view' => 'Vue jardin'],
                ],
            ],
            [
                'name' => 'Bungalows Majestueux',
                'information' => "Authenticité et sérénité dans des hébergements traditionnels réinventés avec "
                    ."tout le confort moderne. Chaque bungalow dispose de sa terrasse privée ouvrant "
                    ."directement sur le jardin.",
                'base_price' => 125000,
                'capacity' => 4,
                'amenities' => ['Terrasse privée avec vue jardin', 'Climatisation et ventilateur', 'Salle de bain privée luxueuse'],
                'bed_type' => 'king size',
                'bed_count' => 1,
                'size' => '45',
                'sort_order' => 2,
                'rooms' => [
                    ['number' => '201', 'name' => 'Bungalow Baobab', 'capacity' => 4, 'price' => 125000, 'view' => 'Terrasse privée - vue jardin'],
                    ['number' => '202', 'name' => 'Bungalow Manguier', 'capacity' => 3, 'price' => 125000, 'view' => 'Terrasse privée - vue jardin'],
                ],
            ],
            [
                'name' => 'Suite Présidentielle',
                'information' => "L'expérience ultime du luxe : deux pièces avec salon privé, balcon "
                    ."panoramique sur la vallée et service de conciergerie dédié à toute heure.",
                'base_price' => 250000,
                'capacity' => 4,
                'amenities' => ['Suite 2 pièces avec salon privé', 'Balcon avec vue panoramique', 'Service de conciergerie 24 h/24'],
                'bed_type' => 'king size',
                'bed_count' => 1,
                'size' => '70',
                'sort_order' => 3,
                'rooms' => [
                    ['number' => '301', 'name' => 'Suite Présidentielle', 'capacity' => 4, 'price' => 250000, 'view' => 'Balcon panoramique - vallée du Zou'],
                ],
            ],
        ];

        foreach ($catalogue as $entry) {
            $rooms = $entry['rooms'];
            unset($entry['rooms']);

            $type = Type::updateOrCreate(
                ['name' => $entry['name']],
                $entry + ['is_active' => true]
            );

            foreach ($rooms as $room) {
                Room::updateOrCreate(
                    ['number' => $room['number']],
                    $room + [
                        'type_id' => $type->id,
                        'room_status_id' => Room::STATUS_AVAILABLE,
                    ]
                );
            }
        }
    }
}
