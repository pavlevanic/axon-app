<?php

namespace App\Http\Controllers;

use App\Models\Product; 
use App\Models\Category;
use App\Models\Cart;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request; 
use App\Models\ProductImage;

class ProductController extends Controller
{
    public function index()
    {
       $products = Product::with('category')->latest()->paginate(10);

       return view('product.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('product.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
{
  
    $product = Product::create($request->validated() + [
        'created_by' => Auth::id(),
        'updated_by' => Auth::id()
    ]);

    if($request->hasFile('images')){
        $manager = new ImageManager(new Driver());
        $folderPath = 'storage/products/' . $product->slug;

        if(!File::exists(public_path($folderPath))){
            File::makeDirectory(public_path($folderPath), 0755, true);
        }

        $files = $request->file('images');

        foreach($files as $key => $file) {
            $filename = time() . '_' . $key . '.webp';
            $fullPath = public_path($folderPath . '/' . $filename);

            $image = $manager->read($file->getPathname());
            $image->scale(height: 1200)->toWebp(80)->save($fullPath);
            
            $db_path = $folderPath . '/' . $filename;

            $product->images()->create([
                'image_path' => $db_path,
                'position'   => $key
            ]);
        }
    }

    return redirect()->route('product.index')->with('status', 'Uspešno dodato više slika!');
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

 
    $request->validate([
        'name' => "required|min:3",
        'short_desc' => 'nullable|max:255',
        'desc' => 'required',
        'slug' => 'required|unique:products,slug,' . $id,
        'price' => 'required|numeric',
        'discount_price' => 'nullable|numeric|lt:price', 
        'stock' => 'required|integer',
        'status' => 'required|in:draft,published,archive',
        'images.*' => 'nullable|image|mimes:png,jpg,webp|max:2048',
        'category_id' => 'required',
        'specs' => 'nullable|array'
    ]);

    
    $isFeatured = $request->has('is_featured') ? 1 : 0;

    
    $mainImage = $product->image; 
    if ($request->has('main_image_path')) {
        $mainImage = $request->main_image_path;
    }

    $product->update([
        'name' => $request->name,
        'short_desc' => $request->short_desc,
        'desc' => $request->desc,
        'slug' => $request->slug,
        'price' => $request->price,
        'discount_price' => $request->discount_price,
        'stock' => $request->stock,
        'status' => $request->status,
        'is_featured' => $isFeatured,
        'image' => $mainImage, 
        'category_id' => $request->category_id,
        'specs' => $request->specs,
        'updated_by' => Auth::id()
    ]);

    // 5. Ako su dodate NOVE slike u galeriju
    if($request->hasFile('images')){
        $manager = new ImageManager(new Driver());
        $folderPath = 'storage/products/' . $product->slug;

        if(!File::exists(public_path($folderPath))){
            File::makeDirectory(public_path($folderPath), 0755, true);
        }

        foreach($request->file('images') as $key => $file) {
            $image = $manager->read($file->getPathname());
            $image->scale(height: 1200); 
            
            
            $filename = time() . '_extra_' . $key . '.webp'; 
            $fullPath = public_path($folderPath . '/' . $filename);
            
            $image->toWebp(80)->save($fullPath);
            
            // Upis u product_images tabelu
            $product->images()->create([
                'image_path' => $folderPath . '/' . $filename,
                'position'   => $key
            ]);
        }
    }

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
        ->paginate(12); 

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
    
        $folderPath = 'storage/products/' . $product->slug;
        if(File::exists(public_path($folderPath))){
            File::deleteDirectory(public_path($folderPath));
        }
    
        $product->delete();
        return redirect()->route('product.index')->with('status', 'Proizvod obrisan.');
    }
    public function destroyImage($id)
{
    $image = ProductImage::findOrFail($id);

    if (file_exists(public_path($image->image_path))) {
        unlink(public_path($image->image_path));
    }

    $image->delete();

    return response()->json(['success' => 'Slika je obrisana.']);
}
}