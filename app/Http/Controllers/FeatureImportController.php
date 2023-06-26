<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\DB;

class FeatureImportController extends Controller
{
    public function import(Request $request): bool
    {
        $url = "https://cdn.jsdelivr.net/gh/Fyrd/caniuse/";


        $client = new GuzzleClient([
            'base_uri' => $url,
            'timeout'  => 10.0,
        ]);

        $response = $client->request('GET', 'fulldata-json/data-2.0.json');

        // Check if a header exists.
        if (!$response->hasHeader('Content-Length')) {
            echo "Could not load page";
            return false;
        }

        $body = $response->getBody();

        $json = json_decode($body, true);

        // Import/sync categories
        // Could do this with an upsert instead
        echo "Syncing categories\n";
        $categoryIds = [];
        foreach ($json['cats'] as $groupname => $cats) {
            foreach ($cats as $cat) {
                $category = Category::where('name', '=', $cat)
                    ->where('groupname', '=', $groupname)
                    ->first();
                if (!$category) {
                    $category = new Category();
                }
                $category->name = $cat;
                $category->groupname = $groupname;
                $category->save();
                $categoryIds[] = $category->id;
            }
        }
        // Delete unused categories
        Category::whereNotIn('id', $categoryIds)->delete();

        echo "Categories synced\n";

        // Import Features
        echo "Syncing features\n";
        $featureIds = [];
        foreach ($json['data'] as $slug => $featuredata) {

            $feature = Feature::leftJoinRelationship('categories')
            ->where('slug', '=', $slug)->first();

            if (!$feature) {
                $feature = new Feature();
            }

            // Assign properties for feature
            $feature->slug = $slug;
            $feature->title = $featuredata['title'];
            $feature->description = $featuredata['description'];
            $feature->example = '';
            $feature->spec =  $featuredata['spec'];
            $feature->status = $featuredata['status'];
            $feature->json = json_encode($featuredata);

            // Set the feature flags for different browsers
            $this->setUsableFlagsForFeature($feature, $featuredata, $json['agents']);

            // Save feature
            $feature->save();
            $featureIds[] = $feature->id;

            // Sync categories for features
            $categoriesForFeature = Category::whereIn('name', $featuredata['categories'])->get();
            $feature->categories()->sync($categoriesForFeature->pluck('id')->all());

        }
        // Delete unused features
        Feature::whereNotIn('id', $featureIds)->delete();

        // Delete any unused link table entries (there should not be any, but just in case)
        DB::table('category_feature')->whereNotIn('category_id', $categoryIds)->delete();
        DB::table('category_feature')->whereNotIn('feature_id', $featureIds)->delete();
        echo "Features synced\n";

        return true;
    }

    private function setUsableFlagsForFeature($feature, $featuredata, $allbrowserdata): Feature
    {

        // Get latest feature
        $currentBrowserVersions = [];
        foreach ($allbrowserdata as $browserkey => $browserdata) {
            $currentBrowserVersions[$browserkey] = $browserdata['current_version'];
        }

        $worksInCurrentBrowser = [];
        foreach ($featuredata['stats'] as $agent => $versionnumbers) {
            if ($versionnumbers[$currentBrowserVersions[$agent]]) {
                $worksInCurrentBrowser[$agent] = $versionnumbers[$currentBrowserVersions[$agent]];
            } else {
                $worksInCurrentBrowser[$agent] = '?';
            }
        }

        $worksInCurrentBrowser = array_map(function ($status) {
            // Works on this browser
            if ($status == 'y') {
                return 'yes';
            }
            if (strpos($status, 'y') !== false) {
                // Does it require a prefix? Thats not ready
                if (strpos($status, 'x') !== false) {
                    return 'partial';
                }
                return 'yes';
            }
            // Almost supported?
            if (strpos($status, 'a') !== false) {
                return 'partial';
            }
            // Not supported?
            if (strpos($status, 'n') !== false) {
                return 'no';
            }
            // Polyfilled? Thats not support
            if (strpos($status, 'p') !== false) {
                return 'no';
            }
            // Disabled by default is the same as not supported
            if (strpos($status, 'd') !== false) {
                return 'no';
            }
            // Requires prefix (and not otherwise fully supported?) That counts as no support
            if (strpos($status, 'x') !== false) {
                return 'no';
            }
            return 'unknown';
        }, $worksInCurrentBrowser);

        $feature->usable_in_chrome = $worksInCurrentBrowser['chrome'];
        $feature->usable_in_edge = $worksInCurrentBrowser['edge'];
        $feature->usable_in_safari = $worksInCurrentBrowser['safari'];
        $feature->usable_in_firefox = $worksInCurrentBrowser['firefox'];
        $feature->usable_in_opera = $worksInCurrentBrowser['opera'];
        $feature->usable_in_ie = $worksInCurrentBrowser['ie'];
        $feature->usable_in_mobile_chrome = $worksInCurrentBrowser['and_chr'];
        $feature->usable_in_ios = $worksInCurrentBrowser['ios_saf'];
        $feature->usable_in_samsung = $worksInCurrentBrowser['samsung'];
        $feature->usable_in_mobile_firefox = $worksInCurrentBrowser['and_ff'];

        return $feature;
    }
}
