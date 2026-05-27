<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class GenerateCategoryThumbs extends Command
{
    protected $signature   = 'categories:thumbs';
    protected $description = 'Generiše thumbnailove za postojeće kategorije';

    public function handle(): void
    {
        $manager   = new ImageManager(new Driver());
        $thumbPath = storage_path('app/public/categories/thumbs');

        if (!File::exists($thumbPath)) {
            File::makeDirectory($thumbPath, 0755, true);
        }

        $categories = Category::whereNotNull('image')->get();
        $bar        = $this->output->createProgressBar($categories->count());

        foreach ($categories as $cat) {
            $original = storage_path('app/public/' . $cat->image);
            
            $this->info("Putanja: {$original}");
            $this->info("Postoji: " . (File::exists($original) ? 'DA' : 'NE'));
            
            if (!File::exists($original)) {
                $this->warn("Slika ne postoji: {$cat->name}");
                $bar->advance();
                continue;
            }

            $filename = basename($cat->image);
            $manager->read($original)
                ->cover(200, 200)
                ->toWebp(75)
                ->save($thumbPath . '/' . $filename);

            $cat->update(['image_thumb' => 'categories/thumbs/' . $filename]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Završeno!');
    }
}