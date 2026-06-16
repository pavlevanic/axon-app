<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'summary' => 'required',
            'content' => 'nullable',
            'type' => 'required|in:hero,normal,promo',
            'image' => 'nullable|image|max:2048',
            'image_mobile' => 'nullable|image|max:2048',
            'custom_url' => 'nullable|string|max:255',
            'dark_image' => 'sometimes|boolean',
        ]);

        $data = $this->buildNewsPayload($request, $validated);

        if ($request->hasFile('image')) {
            $data['image'] = $this->storeNewsImage($request->file('image'), '', 'image');
        }

        if ($request->hasFile('image_mobile')) {
            $data['image_mobile'] = $this->storeNewsImage($request->file('image_mobile'), '_mobile', 'image_mobile');
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
    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'summary' => 'required',
            'content' => 'nullable',
            'image' => 'nullable|image|max:2048',
            'image_mobile' => 'nullable|image|max:2048',
            'type' => 'required|in:normal,hero,promo',
            'custom_url' => 'nullable|string|max:255',
            'dark_image' => 'sometimes|boolean',
        ]);

        $data = $this->buildNewsPayload($request, $validated);

        if ($request->hasFile('image')) {
            $this->deleteNewsImage($news->image);
            $data['image'] = $this->storeNewsImage($request->file('image'), '', 'image');
        }

        if ($request->hasFile('image_mobile')) {
            $this->deleteNewsImage($news->image_mobile);
            $data['image_mobile'] = $this->storeNewsImage($request->file('image_mobile'), '_mobile', 'image_mobile');
        }

        $news->update($data);

        return redirect()->route('news.index')->with('success', 'Vest uspešno ažurirana!');
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('upload') && $request->file('upload')->isValid()) {
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

        $this->deleteNewsImage($news->image);
        $this->deleteNewsImage($news->image_mobile);

        $content = $news->content;

        if ($content) {
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
        }

        $news->delete();

        return redirect()->route('news.index')->with('status', 'Vest i svi povezani fajlovi su uspešno obrisani.');
    }

    private function buildNewsPayload(Request $request, array $validated): array
    {
        $content = $validated['content'] ?? null;

        return [
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'summary' => $validated['summary'],
            'content' => !empty($content) ? $content : '<p>&nbsp;</p>',
            'type' => $validated['type'],
            'custom_url' => $validated['custom_url'] ?? null,
            'dark_image' => $request->has('dark_image') ? 1 : 0,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ];
    }

    private function storeNewsImage(UploadedFile $file, string $suffix = '', string $field = 'image'): string
    {
        if (!$file->isValid()) {
            throw ValidationException::withMessages([
                $field => 'Upload slike nije uspeo: ' . $file->getErrorMessage(),
            ]);
        }

        $directory = public_path('images/news');

        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = time() . $suffix . '_' . Str::random(6) . '.' . $extension;

        $file->move($directory, $filename);

        return 'images/news/' . $filename;
    }

    private function deleteNewsImage(?string $path): void
    {
        if (!$path) {
            return;
        }

        $fullPath = public_path($path);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}
