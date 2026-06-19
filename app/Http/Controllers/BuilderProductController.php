<?php

namespace App\Http\Controllers;

use App\Models\BuilderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class BuilderProductController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $allowedSorts = ['name', 'component_type', 'brand', 'price', 'perf_score', 'tdmark_base', 'fps_base_1080'];
    
        $sort = $request->get('sort');
        $direction = $request->get('direction', 'asc') === 'desc' ? 'desc' : 'asc';

        $query = BuilderProduct::select([
        'id', 
        'name', 
        'slug', 
        'brand', 
        'component_type', 
        'price', 
        'discount_price', 
        'image', 
        'is_active',
        'perf_score',
        'tdmark_base',
        'fps_base_1080'
        ]);

        if ($sort && in_array($sort, $allowedSorts)) {
        $query->orderBy($sort, $direction);
        } 
        else {
        $query->orderBy('component_type', 'asc')->orderBy('name', 'asc');
        }

        $products = $query->paginate(20)->appends($request->all());

        $componentTypes = BuilderProduct::componentTypes();

        return view('admin.builder-products.index', compact('products', 'componentTypes', 'sort', 'direction'));
    }

    public function create()
    {
        $componentTypes = BuilderProduct::componentTypes();

        return view('admin.builder-products.create', compact('componentTypes'));
    }

    public function store(Request $request)
    {
        $data = $this->validateProduct($request);

        if ($request->hasFile('image')) {
            $data['image'] = $this->handleImageUpload($request, $data['slug']);
        }

        BuilderProduct::create($data);

        return redirect()
            ->route('builder-products.index')
            ->with('status', 'Builder komponenta je uspešno dodata.');
    }

    public function edit(BuilderProduct $builderProduct)
    {
        $componentTypes = BuilderProduct::componentTypes();

        return view('admin.builder-products.edit', [
            'product'        => $builderProduct,
            'componentTypes' => $componentTypes,
        ]);
    }

    public function update(Request $request, BuilderProduct $builderProduct)
    {
        $data = $this->validateProduct($request, $builderProduct->id);

        if ($request->hasFile('image')) {
            $this->deleteImage($builderProduct->image);
            $data['image'] = $this->handleImageUpload($request, $data['slug']);
        }

        $builderProduct->update($data);

        return redirect()
            ->route('builder-products.index')
            ->with('status', 'Builder komponenta je uspešno ažurirana.');
    }

    public function destroy(BuilderProduct $builderProduct)
    {
        $this->deleteImage($builderProduct->image);
        $builderProduct->delete();

        return redirect()
            ->route('builder-products.index')
            ->with('status', 'Builder komponenta je obrisana.');
    }

    private function validateProduct(Request $request, ?int $id = null): array
    {
        $types = implode(',', array_keys(BuilderProduct::componentTypes()));

        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'slug'               => 'required|string|max:255|unique:builder_products,slug,' . ($id ?? 'NULL'),
            'brand'              => 'nullable|string|max:100',
            'component_type'     => 'required|in:' . $types,
            'price'              => 'required|numeric|min:0',
            'discount_price'     => 'nullable|numeric|min:0',
            'short_desc'         => 'nullable|string|max:500',
            'axon_product_slug'  => 'nullable|string|max:255',
            'amazon_url'         => 'nullable|url|max:500',
            'perf_score'         => 'nullable|integer|min:0|max:1000',
            'tdmark_base'        => 'nullable|integer|min:0',
            'fps_base_1080'      => 'nullable|integer|min:0',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:2048',
            'spec_socket'        => 'nullable|string|max:50',
            'spec_tdp'           => 'nullable|integer|min:0',
            'spec_wattage'       => 'nullable|integer|min:0',
            'spec_ram_type'      => 'nullable|string|max:20',
            'spec_form_factor'   => 'nullable|string|max:50',
            'spec_cores'         => 'nullable|integer|min:0',
            'spec_threads'       => 'nullable|integer|min:0',
            'spec_boost_ghz'     => 'nullable|numeric|min:0',
            'spec_vram_gb'       => 'nullable|integer|min:0',
            'spec_capacity_gb'   => 'nullable|integer|min:0',
            'spec_speed_mhz'     => 'nullable|integer|min:0',
            'spec_max_mobo'      => 'nullable|string|max:50',
            'spec_max_gpu_mm'    => 'nullable|integer|min:0',
            'spec_max_cooler_mm' => 'nullable|integer|min:0',
            'spec_cooler_type'   => 'nullable|string|max:50',
            'spec_radiator_mm'   => 'nullable|integer|min:0',
            'spec_max_tdp'       => 'nullable|integer|min:0',
            'spec_height_mm'     => 'nullable|integer|min:0',
            'spec_size_mm'       => 'nullable|integer|min:0',
            'spec_fan_count'     => 'nullable|integer|min:0',
            'spec_max_rpm'       => 'nullable|integer|min:0',
            'spec_interface'     => 'nullable|string|max:100',
            'spec_read_mbs'      => 'nullable|integer|min:0',
            'spec_write_mbs'     => 'nullable|integer|min:0',
            'spec_efficiency'    => 'nullable|string|max:50',
            'spec_length_mm'     => 'nullable|integer|min:0',
        ]);

        $specs = array_filter([
            'socket'      => $validated['spec_socket'] ?? null,
            'tdp'         => isset($validated['spec_tdp']) ? (int) $validated['spec_tdp'] : null,
            'wattage'     => isset($validated['spec_wattage']) ? (int) $validated['spec_wattage'] : null,
            'ram_type'    => $validated['spec_ram_type'] ?? null,
            'form_factor' => $validated['spec_form_factor'] ?? null,
            'cores'       => isset($validated['spec_cores']) ? (int) $validated['spec_cores'] : null,
            'threads'     => isset($validated['spec_threads']) ? (int) $validated['spec_threads'] : null,
            'boost_ghz'   => isset($validated['spec_boost_ghz']) ? (float) $validated['spec_boost_ghz'] : null,
            'vram_gb'     => isset($validated['spec_vram_gb']) ? (int) $validated['spec_vram_gb'] : null,
            'capacity_gb' => isset($validated['spec_capacity_gb']) ? (int) $validated['spec_capacity_gb'] : null,
            'speed_mhz'   => isset($validated['spec_speed_mhz']) ? (int) $validated['spec_speed_mhz'] : null,
            'max_mobo'    => $validated['spec_max_mobo'] ?? null,
            'max_cooler_mm' => isset($validated['spec_max_cooler_mm']) ? (int) $validated['spec_max_cooler_mm'] : null,
            'type'        => $validated['spec_cooler_type'] ?? null,
            'radiator_mm' => isset($validated['spec_radiator_mm']) ? (int) $validated['spec_radiator_mm'] : null,
            'max_tdp'     => isset($validated['spec_max_tdp']) ? (int) $validated['spec_max_tdp'] : null,
            'height_mm'   => isset($validated['spec_height_mm']) ? (int) $validated['spec_height_mm'] : null,
            'size_mm'     => isset($validated['spec_size_mm']) ? (int) $validated['spec_size_mm'] : null,
            'count'       => isset($validated['spec_fan_count']) ? (int) $validated['spec_fan_count'] : null,
            'max_rpm'     => isset($validated['spec_max_rpm']) ? (int) $validated['spec_max_rpm'] : null,
            'interface'   => $validated['spec_interface'] ?? null,
            'read_mbs'    => isset($validated['spec_read_mbs']) ? (int) $validated['spec_read_mbs'] : null,
            'write_mbs'   => isset($validated['spec_write_mbs']) ? (int) $validated['spec_write_mbs'] : null,
            'efficiency'  => $validated['spec_efficiency'] ?? null,
            'max_gpu_length_mm' => isset($validated['spec_max_gpu_mm']) ? (int) $validated['spec_max_gpu_mm'] : null,
            'length_mm'         => isset($validated['spec_length_mm'])  ? (int) $validated['spec_length_mm']  : null,
        ], fn ($value) => $value !== null && $value !== '');

        if ($request->has('spec_rgb')) {
            $specs['rgb'] = $request->boolean('spec_rgb');
        }
        if ($request->has('spec_modular')) {
            $specs['modular'] = $request->boolean('spec_modular');
        }

        return [
            'name'              => $validated['name'],
            'slug'              => Str::slug($validated['slug']),
            'brand'             => $validated['brand'] ?? null,
            'component_type'    => $validated['component_type'],
            'price'             => $validated['price'],
            'discount_price'    => $validated['discount_price'] ?? 0,
            'short_desc'        => $validated['short_desc'] ?? null,
            'axon_product_slug' => $validated['axon_product_slug'] ?? null,
            'amazon_url'        => $validated['amazon_url'] ?? null,
            'perf_score'        => $validated['perf_score'] ?? 0,
            'tdmark_base'       => $validated['tdmark_base'] ?? 0,
            'fps_base_1080'     => $validated['fps_base_1080'] ?? 0,
            'in_stock'          => true,
            'is_active'         => $request->boolean('is_active', true),
            'specs'             => $specs ?: null,
        ];
    }

    private function handleImageUpload(Request $request, string $slug): string
    {
        $manager = new ImageManager(new Driver());
        $folder  = public_path('storage/builder-products');

        if (! File::exists($folder)) {
            File::makeDirectory($folder, 0755, true);
        }

        $filename = time() . '_' . Str::slug($slug) . '.webp';
        $fullPath = $folder . '/' . $filename;

        $manager->read($request->file('image')->getPathname())
            ->scale(height: 400)
            ->toWebp(80)
            ->save($fullPath);

        return 'storage/builder-products/' . $filename;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
