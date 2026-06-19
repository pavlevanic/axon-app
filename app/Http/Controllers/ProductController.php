<?php

namespace App\Http\Controllers;

use App\Models\Product; 
use App\Models\Category;
use App\Models\Cart;
use App\Models\ProductImage;
use App\Http\Requests\StoreProductRequest;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request; 

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $sortable = ['id', 'name', 'price', 'stock'];
        $sort      = in_array($request->query('sort'), $sortable) ? $request->query('sort') : 'id';
        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';
    
        $products = Product::with('category')
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();
    
        return view('product.index', compact('products', 'sort', 'direction'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('product.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
{
    $validatedData = $request->validated();

    if (isset($validatedData['images'])) {
        unset($validatedData['images']);
    }

    $specs = $request->input('specs', []);

    $product = Product::create(array_merge($validatedData, [
        'specs'      => $specs,
        'created_by' => Auth::id(),
        'updated_by' => Auth::id()
    ]));

    if ($request->hasFile('images')) {
        $manager = new ImageManager(new Driver());
        $folderPath = 'storage/products/' . $product->slug;

        if (!File::exists(public_path($folderPath))) {
            File::makeDirectory(public_path($folderPath), 0755, true);
        }

        $mainImagePath = null;
        
        $files = array_values($request->file('images'));

        foreach ($files as $key => $file) {
            $filename = time() . '_' . $key . '.webp';
            $fullPath = public_path($folderPath . '/' . $filename);

            $image = $manager->read($file->getPathname());
            $image->scale(height: 1200)->toWebp(80)->save($fullPath);
            
            $db_path = $folderPath . '/' . $filename;

            if ($key === 0) {
                $mainImagePath = $db_path;
            } else {
                $product->images()->create([
                    'image_path' => $db_path,
                    'position'   => $key
                ]);
            }
        }

        if ($mainImagePath) {
            $product->image = $mainImagePath;
            $product->save(); 
        }
    }

    return redirect()->route('product.index')->with('status', 'AXON proizvod uspešno kreiran!');
}

    public function edit($id)
    {
        $product = Product::with('images', 'category')->findOrFail($id);
        $categories = Category::all();

        return view('product.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    $validated = $request->validate([
        'name' => "required|min:3",
        'short_desc' => 'nullable|max:255',
        'desc' => 'required',
        'slug' => 'required|unique:products,slug,' . $id,
        'price' => 'required|numeric',
        'discount_price' => 'nullable|numeric|lt:price', 
        'stock' => 'required|integer',
        'status' => 'required|in:draft,published,archive',
        'images.*' => 'nullable|image|mimes:png,jpg,webp|max:2048',
        'category_id' => 'required|exists:categories,id',
        'specs' => 'nullable|array' 
    ]);

    if (isset($validated['images'])) {
        unset($validated['images']);
    }

    $oldSlug = $product->slug;
    $newSlug = $validated['slug'];

    if ($oldSlug !== $newSlug) {
        $oldFolder = 'storage/products/' . $oldSlug;
        $newFolder = 'storage/products/' . $newSlug;

        if (File::exists(public_path($oldFolder))) {
            File::move(public_path($oldFolder), public_path($newFolder));
            
            foreach ($product->images as $img) {
                $updatedPath = str_replace($oldFolder, $newFolder, $img->image_path);
                $img->update(['image_path' => $updatedPath]);
            }
        }
        
        if ($product->image) {
            $product->image = str_replace($oldFolder, $newFolder, $product->image);
        }
    }

    $isFeatured = $request->has('is_featured') ? 1 : 0;
    
    $mainImage = $request->get('main_image_path', $product->image);

    if ($request->hasFile('images')) {
        $manager = new ImageManager(new Driver());
        $folderPath = 'storage/products/' . $newSlug;

        if (!File::exists(public_path($folderPath))) {
            File::makeDirectory(public_path($folderPath), 0755, true);
        }

        $newFiles = array_values($request->file('images'));

        foreach ($newFiles as $key => $file) {
            $image = $manager->read($file->getPathname());
            $image->scale(height: 1200); 
            
            $filename = time() . '_extra_' . $key . '.webp'; 
            $fullPath = public_path($folderPath . '/' . $filename);
            
            $image->toWebp(80)->save($fullPath);
            $db_path = $folderPath . '/' . $filename;
            
            if (empty($mainImage) && $key === 0) {
                $mainImage = $db_path;
            } else {
                $product->images()->create([
                    'image_path' => $db_path,
                    'position'   => $key
                ]);
            }
        }
    }

    $specs = $request->input('specs', []);

    $product->update(array_merge($validated, [
        'specs'       => $specs, 
        'is_featured' => $isFeatured,
        'image'       => $mainImage, 
        'updated_by'  => Auth::id()
    ]));

    return redirect()->route('product.index')->with('status', 'AXON proizvod uspešno ažuriran!');
}

    public function search(Request $request)
    {
        $query = $request->input('query');

        $products = Product::where('status', 'published')
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('short_desc', 'LIKE', "%{$query}%")
                  ->orWhere('desc', 'LIKE', "%{$query}%");
            })
            ->paginate(12)
            ->withQueryString();

        return view('product.search-results', compact('products', 'query'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
                          ->where('status', 'published')
                          ->firstOrFail();

        return view('product.show', compact('product'));
    }

    public function destroy($id) 
    {
        $product = Product::find($id);
    
        if (!$product) {
            return redirect()->back()->with('error', 'Proizvod sa ID ' . $id . ' nije nađen u bazi.');
        }
    
        if (!empty($product->slug) && $product->slug !== '/') {
            $folderPath = 'storage/products/' . $product->slug;
            if (File::exists(public_path($folderPath))) {
                File::deleteDirectory(public_path($folderPath));
            }
        }
    
        $product->delete();
        return redirect()->route('product.index')->with('status', 'Proizvod obrisan.');
    }

    public function destroyImage($id)
    {
        $image = ProductImage::findOrFail($id);

        if (File::exists(public_path($image->image_path))) {
            File::delete(public_path($image->image_path)); 
        }

        $image->delete();

        return response()->json(['success' => 'Slika je obrisana.']);
    }
}