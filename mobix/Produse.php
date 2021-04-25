<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Cviebrock\EloquentSluggable\Sluggable;

/**
 * @method static orderBy(string $string, string $string1)
 */
class Produse extends Model
{
    public $table = 'produse';

    use Sluggable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'denumire',
        'created_at',
        'updated_at',
        'slug',
    ];

    public static function totalProduse()
    {
        return Produse::all()->count();
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'produse' => 'denumire'
            ]
        ];
    }

    /* Cascade deleting. (Marian Pop - 25.05.2020)
     * --------------------------------------------------*
     * Cand stergem un produs, se vor sterge si corpurile
     * atasate de produsul respectiv.
     * --------------------------------------------------*
     */

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($produse) {
            $produse->corpuri()->delete();
        });
    }
    public function corpuri(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Corpuri::class, 'prod_id');
    }

    /* --------------------------------------------------*/
}
