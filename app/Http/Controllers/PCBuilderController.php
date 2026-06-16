<?php

namespace App\Http\Controllers;

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