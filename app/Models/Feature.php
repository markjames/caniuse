<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kirschbaum\PowerJoins\PowerJoins;

use App\Enum\Status as StatusEnum;
use App\Collections\FeatureCollection;
use App\Traits\CanGetStaticTableName;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection as SupportCollection;

class Feature extends Model
{
    use HasFactory;
    use MassPrunable;
    use CanGetStaticTableName;
    use PowerJoins;

    /**
     * The model's default attribute values.
     *
     * @var array
     */
    protected $attributes = [
        'slug' => '',
        'title' => '',
        'description' => '',
        'shortdescription' => '',
        'example' => '',
        'spec' => '',
        'status' => '', // W3C Recommendation, W3C Proposed Recommendation, W3C Candidate Recommendation, W3C Working Draft, WHATWG Living Standard, Other, Unofficial / Note,
        'usable_in_chrome' => 'unknown',
        'usable_in_edge' => 'unknown',
        'usable_in_safari' => 'unknown',
        'usable_in_firefox' => 'unknown',
        'usable_in_opera' => 'unknown',
        'usable_in_ie' => 'unknown',
        'usable_in_mobile_chrome' => 'unknown',
        'usable_in_ios' => 'unknown',
        'usable_in_samsung' => 'unknown',
        'usable_in_mobile_firefox' => 'unknown',
        'json' => 'array'
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['json'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'json' => 'array',
    ];

    public function supportValues(): SupportCollection
    {

        return collect($this->attributes)->filter(function ($value, string $key) {
            return (strpos($key, 'usable_in') === 0);
        })->mapWithKeys(function ($value, string $key) {
            return [str_replace('_', ' ', str_replace('usable_in_', '', $key)) => $value];
        });
    }

    public function documentationUrl(): string
    {
        $result = $this->spec;
        foreach ($this->json['links'] as $link) {
            if (strpos($link['url'], 'developer.mozilla')) {
                $result = $link['url'];
            }
        }
        return $result;
    }

    // Categories
    /**
     * Get the category that this feature is in
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array<int, \Illuminate\Database\Eloquent\Model>  $models
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Database\Eloquent\Model>
     */
    public function newCollection(array $models = []): Collection
    {
        return new FeatureCollection($models);
    }


    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        // TODO: Decide if we want to prune old features
        // not currently pruned
        return static::where('id', '<', 0);
    }
}
