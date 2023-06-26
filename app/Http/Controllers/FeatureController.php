<?php

namespace App\Http\Controllers;

use App\Enums\Status as StatusEnum;
use App\Models\Category;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FeatureController extends Controller
{
    /**
     * Display a list of features
     */
    public function list(): View
    {
        $features = Feature::select('features.*','categories.name as category_name','categories.groupname as category_groupname')
        ->joinRelationship('categories')
        ->orderByPowerJoins('categories.groupname','asc')
        ->orderByPowerJoins('categories.name','asc')
        ->orderBy('features.title','asc')
        ->get();

        $categories = Category::all();

        // Nest into a navigation
        $menuItems = collect($features)->groupBy('category_groupname')->sortKeys()->map( function($item) {
            return $item->groupBy('category_name');
        });

        return view('feature.list', [
            'categorygroups' => $menuItems,
            'categories' => $categories,
            'features' => $features
        ]);
    }

    /**
     * Show a specific Feature
     */
    public function show(string $slug): View
    {
        return view('feature.show', [
            'feature' => Feature::where('slug', '=', $slug)->with('categories')->firstOrFail()
        ]);
    }
}
