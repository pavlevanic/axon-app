<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function components(Request $request)
    {
        $categories = Category::where('id', '!=', 1)->get();

        if (!$request->has('category')) {
            $products  = collect();
            $allSpecs  = [];
            $viewTitle = 'PC Komponente';
            return view('frontend.shop', compact('products', 'categories', 'viewTitle', 'allSpecs'));
        }

        $query = Product::query()->whereHas('category', function ($q) {
            $q->where('id', '!=', 1);
        });

        $query->whereHas('category', function ($q) use ($request) {
            $q->where('slug', $request->category);
        });

        $this->applySpecFilters($query, $request);
        $this->applySorting($query, $request);

        $products = $query->paginate(12)->withQueryString();

        $selectedCategory = Category::where('slug', $request->category)->first();
        $viewTitle = $selectedCategory ? $selectedCategory->name : 'PC Komponente';

        $filterSource = Product::whereHas('category', function ($q) use ($request) {
            $q->where('slug', $request->category);
        })->get();

        $allSpecs = $this->getAutoFilters($filterSource);

        return view('frontend.shop', compact('products', 'categories', 'viewTitle', 'allSpecs'));
    }

    public function prebuilts(Request $request)
    {
        $query = Product::query()->whereHas('category', function ($q) {
            $q->where('id', 1);
        });
        
        $this->applySpecFilters($query, $request);
        $this->applySorting($query, $request); 

        $products  = $query->paginate(12)->withQueryString();
        $viewTitle = 'Gotove Konfiguracije';
        $categories = collect();

        $filterSource = Product::whereHas('category', function ($q) {
            $q->where('id', 1);
        })->get();

        $allSpecs = $this->getAutoFilters($filterSource);

        return view('frontend.shop', compact('products', 'categories', 'viewTitle', 'allSpecs'));
    }

    private function applySpecFilters($query, $request)
    {
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->has('in_stock')) {
            $query->where('stock', '>', 0);
        }
        if ($request->input('on_sale') == '1') {
            $query->whereNotNull('discount_price')->where('discount_price', '>', 0);
        }
        if ($request->has('specs')) {
            foreach ($request->specs as $attrName => $values) {
                $query->where(function ($q) use ($attrName, $values) {
                    foreach ($values as $value) {
                        $q->orWhere('specs->Osnovni_' . $attrName, $value)
                          ->orWhere('specs->' . $attrName, $value);
                    }
                });
            }
        }
    }

    private function applySorting($query, $request)
    {
        $sort = $request->get('sort', 'newest');

        switch ($sort) {
            case 'price_asc':
                $query->orderByRaw('COALESCE(discount_price, price) ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('COALESCE(discount_price, price) DESC');
                break;
            case 'alpha_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'alpha_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }
    }

    private function getAutoFilters($products)
    {
        $allSpecs        = [];
        $attributeCounts = [];

        foreach ($products as $product) {
            $specs = is_array($product->specs)
                ? $product->specs
                : json_decode($product->specs, true);

            if (!$specs) continue;

            foreach ($specs as $fullKey => $value) {
                if (empty($value)) continue;

                $attrName = str_replace('Osnovni_', '', $fullKey);
                $attributeCounts[$attrName] = ($attributeCounts[$attrName] ?? 0) + 1;

                if (!isset($allSpecs[$attrName])) {
                    $allSpecs[$attrName] = [];
                }
                if (!in_array($value, $allSpecs[$attrName])) {
                    $allSpecs[$attrName][] = $value;
                }
            }
        }

        arsort($attributeCounts);
        $topAttributes = array_slice(array_keys($attributeCounts), 0, 4);

        $finalFilters = [];
        foreach ($topAttributes as $attr) {
            sort($allSpecs[$attr]);
            $finalFilters[$attr] = $allSpecs[$attr];
        }

        return $finalFilters;
    }
}