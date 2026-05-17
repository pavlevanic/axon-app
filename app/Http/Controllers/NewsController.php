<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\News;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $news = News::latest()->get();
        return view('admin.news.index', compact('news'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.news.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNewsRequest $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'type' => 'required|in:hero,normal,promo',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $data = $request->all();
        
        $data['slug'] = Str::slug($request->title);

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images/news'), $imageName);
            $data['image'] = 'images/news/' . $imageName;
        }

        News::create($data);

        return redirect()->route('news.index')->with('success', 'Vest je uspešno kreirana!');
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $news = News::where('slug', $slug)->firstOrFail();

        return view('admin.news.show', compact('news'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $news = News::findOrFail($id);
        return view('admin.news.edit', compact('news'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNewsRequest $request, News $news,$id)
    {
        $news = News::findOrFail($id);

    $request->validate([
        'title' => 'required|max:255',
        'summary' => 'required',
        'content' => 'required',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'type' => 'required|in:normal,hero',
    ]);

    $data = $request->all();
    
    $data['is_active'] = $request->has('is_active') ? 1 : 0;

    if ($request->hasFile('image')) {
        
        if (file_exists(public_path($news->image))) {
            unlink(public_path($news->image));
        }

        $imageName = time().'.'.$request->image->extension();
        $request->image->move(public_path('images/news'), $imageName);
        $data['image'] = 'images/news/' . $imageName;
    }

    $data['slug'] = Str::slug($request->title);

    $news->update($data);

    return redirect()->route('news.index')->with('success', 'Vest uspešno ažurirana!');
    }

    public function uploadImage(Request $request)
{
    if ($request->hasFile('upload')) {

        $file = $request->file('upload');

        $filename = time().'_'.$file->getClientOriginalName();

        $path = $file->storeAs('news_content', $filename, 'public');

        return response()->json([
            'url' => asset('storage/' . $path)
        ]);
    }

    return response()->json(['error' => 'Upload failed'], 400);
}

   
    public function destroy($id)
    {
        $news = News::findOrFail($id);

    if ($news->image) {
        $mainImagePath = public_path($news->image); 
        if (File::exists($mainImagePath)) {
            File::delete($mainImagePath);
        }
    }

    $content = $news->content;
    
    preg_match_all('/<img [^>]*src="([^"]+)"[^>]*>/i', $content, $matches);
    
    if (!empty($matches[1])) {
        foreach ($matches[1] as $imageUrl) {
            $path = parse_url($imageUrl, PHP_URL_PATH);
            $serverPath = public_path($path);

            if (File::exists($serverPath)) {
                File::delete($serverPath);
            }
        }
    }

    $news->delete();

    return redirect()->route('news.index')->with('status', 'Vest i svi povezani fajlovi su uspešno obrisani.');
    }
}
