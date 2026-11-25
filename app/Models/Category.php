<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Catégorie de la carte du restaurant (Plats, Grillades, Bar...).
 */
class Category extends Model
{
    protected $fillable = ['name', 'slug', 'sort_order'];

    protected static function booted()
    {
        static::saving(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /** Icône proposée selon le nom, pour la carte publique */
    public function getIconAttribute()
    {
        $name = Str::lower($this->name);

        return match (true) {
            Str::contains($name, ['boisson', 'bar', 'cocktail', 'vin']) => 'fa-glass-martini-alt',
            Str::contains($name, ['dessert', 'glace', 'patis']) => 'fa-ice-cream',
            Str::contains($name, ['salade', 'entrée', 'entree', 'légume']) => 'fa-leaf',
            Str::contains($name, ['grillade', 'viande', 'poisson', 'mer']) => 'fa-drumstick-bite',
            Str::contains($name, ['rapide', 'snack', 'burger']) => 'fa-clock',
            Str::contains($name, ['europ', 'pizza', 'pasta']) => 'fa-hamburger',
            default => 'fa-utensils',
        };
    }
}
