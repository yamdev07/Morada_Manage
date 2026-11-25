<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Carte du restaurant Morada Lodge.
 *
 * Ces plats et ces catégories étaient auparavant écrits en dur dans la page
 * vitrine ; ils vivent désormais en base pour être modifiables depuis le
 * back-office. Le seeder est idempotent : relancé, il met à jour l'existant
 * sans créer de doublon.
 */
class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $carte = [
            'Mets Africains' => [
                ['Amiwo au poulet', 5000, 'Plat traditionnel à base de maïs avec poulet'],
                ['Atassi avec Dja', 3500, 'Riz parfumé accompagné de sauce tomate'],
                ['Bomiwo au poulet', 5500, 'Spécialité à base de pâte de maïs et poulet'],
                ['MAN au Télibo', 6000, 'Plat signature du chef avec poisson fumé'],
            ],
            'Spécialités Européennes' => [
                ['Spaghettis bolognaise', 2500, 'Pâtes italiennes avec sauce à la viande maison'],
                ['Steak de bœuf', 4500, 'Entrecôte grillée, frites et sauce au choix'],
            ],
            'Grillades & Fruits de Mer' => [
                ['Brochette de bœuf', 4000, 'Brochettes marinées avec légumes grillés'],
                ['Brochette de poisson', 4500, 'Brochettes de poisson frais et épices'],
                ['Poisson braisé', 4500, 'Poisson entier grillé au feu de bois'],
                ['Gambas sautées à l\'ail', 12000, 'Crevettes géantes sautées à l\'ail et persil'],
            ],
            'Restauration Rapide' => [
                ['Shawarma (poulet ou viande)', 2000, 'Wrap garni de viande, légumes et sauce'],
                ['Frites au poulet', 2500, 'Frites croustillantes avec poulet pané'],
                // Prix à 0 = « Sur commande » sur le site public
                ['Burgers variés', 0, 'Selon l\'inspiration du chef — demandez-nous'],
            ],
            'Salades Fraîches' => [
                ['Salades composées', 0, 'Salades fraîches accompagnées de sauces maison — prix selon la carte du jour'],
            ],
            'Bar & Cocktails' => [
                ['Vins sélectionnés', 0, 'Une sélection de vins pour accompagner vos repas'],
                ['Cocktails maison', 0, 'Cocktails originaux préparés par notre barman'],
            ],
        ];

        foreach (array_keys($carte) as $position => $categoryName) {
            $category = Category::updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName, 'sort_order' => $position]
            );

            foreach ($carte[$categoryName] as [$name, $price, $description]) {
                Menu::updateOrCreate(
                    ['name' => $name],
                    [
                        'category_id' => $category->id,
                        'price' => $price,
                        'description' => $description,
                        'is_available' => true,
                    ]
                );
            }
        }

        // Les catégories génériques créées lors de la reprise de données
        // n'ont plus de plat rattaché : on les retire pour garder une carte lisible.
        Category::whereIn('slug', ['plats', 'entrees', 'desserts', 'boissons'])
            ->doesntHave('menus')
            ->delete();
    }
}
