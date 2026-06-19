<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Category;
use App\Models\BuilderProduct;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PCBuilderController extends Controller
{
   
    private array $steps = [
        'cpu', 'gpu', 'motherboard', 'ram',
        'case', 'cpu_cooler', 'case_fan', 'storage', 'psu',
    ];


    public function index(): View
    {
        $allProducts = BuilderProduct::active()
            ->orderBy('component_type')
            ->orderByDesc('perf_score')
            ->get()
            ->groupBy('component_type')
            ->map(fn($group) => $group->map->toBuilderArray()->values());

        $steps = BuilderProduct::componentTypes();

        $builderData = [
            'steps'    => array_keys($steps),
            'stepNames'=> $steps,
            'products' => $allProducts,
        ];

        return view('frontend.pc-builder', compact('builderData', 'steps'));
    }

   
    public function getComponents(Request $request, string $type): JsonResponse
    {
        if (!in_array($type, $this->steps)) {
            return response()->json(['error' => 'Nepoznat tip komponente'], 422);
        }

        $query = BuilderProduct::active()->inStock()->ofType($type);

        // Kompatibilnost — filtriranje po socket-u ako je CPU izabran
        if ($request->filled('cpu_socket') && in_array($type, ['motherboard', 'cpu_cooler'])) {
            $query->compatibleSocket($request->cpu_socket);
        }

        // Kompatibilnost — RAM tip prema matičnoj ploči
        if ($request->filled('ram_type') && $type === 'ram') {
            $query->compatibleRam($request->ram_type);
        }

        $products = $query->orderByDesc('perf_score')->get()->map->toBuilderArray();

        return response()->json([
            'type'     => $type,
            'products' => $products,
        ]);
    }

    public function addBuildToCart(Request $request)
{
    $request->validate([
        'component_ids'   => 'required|array|min:1',
        'component_ids.*' => 'integer|exists:builder_products,id',
    ]);

    $components = BuilderProduct::whereIn('id', $request->component_ids)->get();

    if ($components->isEmpty()) {
        return back()->with('error', 'Build je prazan.');
    }

    $totalPrice = $components->sum(fn ($c) => $c->effective_price);

    $componentSnapshot = $components->map(fn ($c) => [
        'id'    => $c->id,
        'name'  => $c->name,
        'type'  => $c->component_type,
        'price' => $c->effective_price,
        'image' => $c->image,
    ])->values()->toArray();

    $buildName = 'Custom PC Build #' . strtoupper(Str::random(6));

    $caseImage = $components->firstWhere('component_type', 'case')?->image;

    $product = Product::create([
        'name'             => $buildName,
        'slug'             => Str::slug($buildName) . '-' . uniqid(),
        'short_desc'       => 'Konfigurisan PC sastavljen putem PC Builder-a',
        'desc'             => $this->buildDescriptionFromComponents($components),
        'price'            => $totalPrice,
        'discount_price'   => 0,
        'stock'            => 1,
        'is_featured'      => false,
        'is_custom_build'  => true,
        'image'            => $caseImage,
        'category_id'      => Category::where('slug', 'custom-build')->value('id'),
        'specs'            => null,
        'build_components' => $componentSnapshot,
        'created_by'       => auth()->id(),
        'updated_by'       => auth()->id(),
    ]);

    Cart::create([
        'user_id'    => auth()->id(),
        'product_id' => $product->id,
        'quantity'   => 1,
    ]);

    return redirect()->route('cart.index')->with('status', 'Build je dodat u korpu!');
}
private function buildDescriptionFromComponents($components): string
{
    $lines = $components->map(fn ($c) => '- ' . $c->name . ' (' . $c->component_type . ')');
    return "Konfigurisan PC build:\n" . $lines->implode("\n");
}
 
    public function saveBuild(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'components'  => 'required|array',
            'total_price' => 'required|numeric|min:0',
            'name'        => 'nullable|string|max:80',
        ]);

        $ids = array_filter(array_values($validated['components']));
        $found = BuilderProduct::whereIn('id', $ids)->count();

        if ($found !== count($ids)) {
            return response()->json(['error' => 'Neke komponente nisu pronađene'], 422);
        }

        $save = \App\Models\BuilderSave::create([
            'user_id'          => auth()->id(),
            'name'             => $validated['name'] ?? 'Moj Build',
            'components'       => $validated['components'],
            'total_price'      => $validated['total_price'],
            'share_token'      => \Str::random(32),
        ]);

        return response()->json([
            'success'    => true,
            'share_url'  => route('builder.share', $save->share_token),
        ]);
    }
}