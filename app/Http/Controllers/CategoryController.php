<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File; 
use Intervention\Image\ImageManager; 
use Intervention\Image\Drivers\Gd\Driver; 

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
        return view('category.index', compact('categories'));
    }

    public function create()
    {
        return view('category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'slug' => 'required|unique:categories,slug|max:255',
            'desc' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'group_names' => 'nullable|array',
            'group_attributes' => 'nullable|array'
        ]);

        $data = $this->prepareAttributeData($request);
        $imagePath = null;

        if ($request->hasFile('image')) {
            $paths     = $this->handleImageUpload($request);
            $imagePath = $paths['original'];
            $thumbPath = $paths['thumb'];
        }
        
        Category::create([
            'name'             => $request->name,
            'slug'             => $request->slug,
            'desc'             => $request->desc,
            'image'            => $imagePath,
            'image_thumb'      => $thumbPath ?? null,
            'attribute_groups' => $data['groups'],
            'attribute_names'  => $data['all_names'],
            'created_by'       => Auth::id(),
            'updated_by'       => Auth::id()
        ]);

        return redirect()->route('category.index')->with('status', 'Kategorija je uspešno kreirana!');
    }

    public function edit(Category $category)
    {
        return view('category.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'desc' => 'nullable|string',
        ]);

        $data = $this->prepareAttributeData($request);
        $imagePath = $category->image; 

        $imagePath = $category->image;
$thumbPath = $category->image_thumb;

if ($request->hasFile('image')) {
    // Brisanje stare originalne slike
    if ($category->image && File::exists(storage_path('app/public/' . $category->image))) {
        File::delete(storage_path('app/public/' . $category->image));
    }
    // Brisanje starog thumbnailа
    if ($category->image_thumb && File::exists(storage_path('app/public/' . $category->image_thumb))) {
        File::delete(storage_path('app/public/' . $category->image_thumb));
    }

    $paths     = $this->handleImageUpload($request);
    $imagePath = $paths['original'];
    $thumbPath = $paths['thumb'];
}

$category->update([
    'name'             => $request->name,
    'slug'             => $request->slug,
    'desc'             => $request->desc,
    'image'            => $imagePath,
    'image_thumb'      => $thumbPath,
    'attribute_groups' => $data['groups'],
    'attribute_names'  => $data['all_names'],
    'updated_by'       => Auth::id()
]);

        return redirect()->route('category.index')->with('status', 'Kategorija uspešno ažurirana!');
    }

    public function destroy(Category $category)
    {
        if ($category->image && File::exists(storage_path('app/public/' . $category->image))) {
            File::delete(storage_path('app/public/' . $category->image));
        }

        $category->delete();
        return redirect()->route('category.index')->with('status', 'Uspešno obrisano');
    }

    private function handleImageUpload(Request $request)
    {
        $manager         = new ImageManager(new Driver());
    $destinationPath = storage_path('app/public/categories');
    $thumbPath       = storage_path('app/public/categories/thumbs');

    if (!File::exists($destinationPath)) {
        File::makeDirectory($destinationPath, 0755, true);
    }
    if (!File::exists($thumbPath)) {
        File::makeDirectory($thumbPath, 0755, true);
    }

    $file     = $request->file('image');
    $filename = time() . '_' . $request->slug . '.webp';

    $manager->read($file->getPathname())
        ->scale(height: 600)
        ->toWebp(80)
        ->save($destinationPath . '/' . $filename);

    $manager->read($file->getPathname())
        ->cover(200, 200)
        ->toWebp(75)
        ->save($thumbPath . '/' . $filename);

    return [
        'original' => 'categories/' . $filename,
        'thumb'    => 'categories/thumbs/' . $filename,
    ];
    }

    private function prepareAttributeData($request)
    {
        $groups = [];
        $allNames = [];

        if ($request->has('group_names')) {
            foreach ($request->group_names as $key => $name) {
                if (!empty($name)) {
                    $attrsString = $request->group_attributes[$key] ?? '';
                    $attrsArray = array_filter(array_map('trim', explode(',', $attrsString)));
                    $groups[$name] = $attrsArray;
                    foreach ($attrsArray as $attr) {
                        if (!in_array($attr, $allNames)) {
                            $allNames[] = $attr;
                        }
                    }
                }
            }
        }
        return ['groups' => $groups, 'all_names' => $allNames];
    }

    public function show(Category $category) 
    {
        return view('category.show', compact('category'));
    }
}