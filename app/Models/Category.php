<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Category extends Authenticatable implements Sortable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SortableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'order',
    ];

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }

    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];
}
