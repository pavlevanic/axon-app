<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        
        $category = Category::inRandomOrder()->first();
        $categoryId = $category ? $category->id : null;

        if ($category && $category->image) {
            if (str_starts_with($category->image, 'storage/')) {
                $productImage = $category->image;
            } else {
                $productImage = 'storage/' . $category->image;
            }
        } else {
            $productImage = 'storage/products/player-one-black/1777642327_extra_0.webp';
        }

        $user = User::first();
        $userId = $user ? $user->id : 1;

        $specsArray = [];

        if ($category && !empty($category->attribute_groups)) {
            $groups = is_string($category->attribute_groups) 
                ? json_decode($category->attribute_groups, true) 
                : $category->attribute_groups;

            if (is_array($groups)) {
                foreach ($groups as $groupName => $attributes) {
                    if (is_array($attributes)) {
                        foreach ($attributes as $attrName) {
                            $key = $groupName . '_' . $attrName;
                            $lowerAttr = strtolower($attrName);
                            
                            if (str_contains($lowerAttr, 'cpu') || str_contains($lowerAttr, 'procesor')) {
                                $value = $this->faker->randomElement(['Intel Core i5-14600K', 'AMD Ryzen 7 7800X3D', 'Intel Core i7-14700K']);
                            } elseif (str_contains($lowerAttr, 'gpu') || str_contains($lowerAttr, 'grafička')) {
                                $value = $this->faker->randomElement(['NVIDIA RTX 4060 Ti 8GB', 'NVIDIA RTX 4070 Super 12GB', 'AMD Radeon RX 7800 XT']);
                            } elseif (str_contains($lowerAttr, 'ram') || str_contains($lowerAttr, 'kapacitet')) {
                                $value = $this->faker->randomElement(['16GB DDR5 6000MHz', '32GB DDR5 6000MHz', '1TB NVMe M.2 SSD']);
                            } elseif (str_contains($lowerAttr, 'os') || str_contains($lowerAttr, 'operativni')) {
                                $value = $this->faker->randomElement(['Windows 11 Pro', 'Nema (FreeDOS)']);
                            } elseif (str_contains($lowerAttr, 'garancija')) {
                                $value = $this->faker->randomElement(['2 godine', '3 godine', '24 Meseca']);
                            } elseif (str_contains($lowerAttr, 'proizvođač')) {
                                $value = $this->faker->randomElement(['ASUS', 'Gigabyte', 'MSI', 'Kingston', 'NZXT']);
                            } else {
                                $value = $this->faker->randomElement(['Crna', 'Bela', 'Da', 'M-ATX', '650W 80+ Gold']);
                            }

                            $specsArray[$key] = $value;
                        }
                    }
                }
            }
        }

        if (empty($specsArray)) {
            $specsArray = [
                'Osnovni_Garancija' => '2 godine',
                'Osnovni_Materijal' => 'Čelik i staklo'
            ];
        }

        $price = $this->faker->randomFloat(2, 80, 2500);
        $discountPrice = $this->faker->boolean(30) ? $price * 0.88 : null;

        $isOld = $this->faker->boolean(50);
        $createdAt = $isOld 
            ? $this->faker->dateTimeBetween('-30 days', '-8 days') 
            : now();

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'short_desc' => $this->faker->sentence(10),
            'desc' => '<p>' . implode('</p><p>', $this->faker->paragraphs(3)) . '</p>',
            'price' => $price,
            'discount_price' => $discountPrice,
            'stock' => $this->faker->numberBetween(0, 30),
            'is_featured' => $this->faker->boolean(15),
            'image' => $productImage, 
            'category_id' => $categoryId,
            'created_by' => $userId,
            'updated_by' => $userId,
            'specs' => $specsArray, 
            
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }
}