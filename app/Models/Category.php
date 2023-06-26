<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kirschbaum\PowerJoins\PowerJoins;

use App\Traits\CanGetStaticTableName;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;
    use CanGetStaticTableName;
    use PowerJoins;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * The model's default attribute values.
     *
     * @var array
     */
    protected $attributes = [
        'name' => ''
    ];

    // Categories
    /**
     * Get the features for this category
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class);
    }
}
