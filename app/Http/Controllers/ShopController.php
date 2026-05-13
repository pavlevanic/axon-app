<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    // Filteri koji se uvek vide (Cena, Stanje, Popust su u applySpecFilters)
    
    public function components(Request $request)
    {
        $query = Product::query()->whereHas('category', function($q) {
            $q->where('slug', '!=', 'prebuilt-pc');
        });

        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $this->applySpecFilters($query, $request);

        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::where('slug', '!=', 'prebuilt-pc')->get();
        
        // Uzimamo samo proizvode iz trenutne kategorije (ili sve komponente) da izvučemo top atribute
        $filterSource = Product::whereHas('category', function($q) use ($request) {
            $q->where('slug', '!=', 'prebuilt-pc');
            if ($request->has('category')) {
                $q->where('slug', $request->category);
            }
        })->get();

        $allSpecs = $this->getAutoFilters($filterSource);
        $viewTitle = "PC Komponente";

        return view('frontend.shop', compact('products', 'categories', 'viewTitle', 'allSpecs'));
    }

    public function prebuilts(Request $request)
    {
        $query = Product::query()->whereHas('category', function($q) {
            $q->where('slug', 'prebuilt-pc');
        });

        $this->applySpecFilters($query, $request);

        $products = $query->latest()->paginate(12)->withQueryString();
        $viewTitle = "Gotove Konfiguracije";
        $categories = collect(); 

        $filterSource = Product::whereHas('category', function($q) {
            $q->where('slug', 'prebuilt-pc');
        })->get();

        $allSpecs = $this->getAutoFilters($filterSource);

        return view('frontend.shop', compact('products', 'categories', 'viewTitle', 'allSpecs'));
    }

    private function applySpecFilters($query, $request)
    {
        // 1. Cena
        if ($request->filled('min_price')) $query->where('price', '>=', $request->min_price);
        if ($request->filled('max_price')) $query->where('price', '<=', $request->max_price);

        // 2. Stanje (ako je quantity > 0)
        if ($request->has('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // 3. Popust (ako postoji discount_price)
        if ($request->has('on_sale')) {
            $query->whereNotNull('discount_price')->where('discount_price', '>', 0);
        }

        // 4. Dinamički spec filteri
        if ($request->has('specs')) {
            foreach ($request->specs as $attrName => $values) {
                $query->where(function($q) use ($attrName, $values) {
                    foreach ($values as $value) {
                        $q->orWhere('specs->Osnovni_' . $attrName, $value)
                          ->orWhere('specs->' . $attrName, $value);
                    }
                });
            }
        }
    }

    /**
     * AUTOMATSKO IZVLAČENJE TOP 4 ATRIBUTA
     */
    private function getAutoFilters($products)
    {
        $allSpecs = [];
        $attributeCounts = []; // Pomoćni niz da izbrojimo koliko se puta koji atribut javlja

        foreach ($products as $product) {
            $specs = is_array($product->specs) ? $product->specs : json_decode($product->specs, true);
            if (!$specs) continue;

            foreach ($specs as $fullKey => $value) {
                if (empty($value)) continue;

                // Čistimo ključ od "Osnovni_"
                $attrName = str_replace('Osnovni_', '', $fullKey);
                
                // Brojimo pojavljivanja atributa
                $attributeCounts[$attrName] = ($attributeCounts[$attrName] ?? 0) + 1;

                // Skladištimo vrednosti
                if (!isset($allSpecs[$attrName])) {
                    $allSpecs[$attrName] = [];
                }
                if (!in_array($value, $allSpecs[$attrName])) {
                    $allSpecs[$attrName][] = $value;
                }
            }
        }

        // Sortiramo atribute tako da oni koji se najčešće javljaju budu prvi
        arsort($attributeCounts);

        // Uzimamo samo top 4 atributa (npr. CPU, GPU, RAM, Socket)
        $topAttributes = array_slice(array_keys($attributeCounts), 0, 4);

        // Filtriramo finalni niz specifikacija
        $finalFilters = [];
        foreach ($topAttributes as $attr) {
            sort($allSpecs[$attr]); // Sortiramo vrednosti (npr. AMD, pa Intel)
            $finalFilters[$attr] = $allSpecs[$attr];
        }

        return $finalFilters;
    }
}