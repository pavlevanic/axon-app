<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class BuilderProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'builder_products';

    protected $fillable = [
        'name', 'slug', 'brand', 'component_type',
        'image', 'axon_product_slug', 'amazon_url',
        'price', 'discount_price',
        'in_stock', 'is_active',
        'perf_score', 'tdmark_base', 'fps_base_1080',
        'specs', 'short_desc',
    ];

    protected $casts = [
        'specs'          => 'array',
        'price'          => 'decimal:2',
        'discount_price' => 'decimal:2',
        'in_stock'       => 'boolean',
        'is_active'      => 'boolean',
    ];

    protected $hidden = [];

    
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('in_stock', true);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('component_type', $type);
    }

    // Kompatibilnost — isti socket
    public function scopeCompatibleSocket(Builder $query, string $socket): Builder
    {
        return $query->where('socket', $socket);
    }

    // Kompatibilnost — RAM tip
    public function scopeCompatibleRam(Builder $query, string $ramType): Builder
    {
        return $query->where('ram_type', $ramType);
    }

    /*
     * ── ACCESSORS ────────────────────────────────────────────
     */

    // Trenutna ciena (discount ili redovna)
    public function getEffectivePriceAttribute(): float
    {
        if ($this->discount_price > 0 && $this->discount_price < $this->price) {
            return (float) $this->discount_price;
        }
        return (float) $this->price;
    }

    // Da li ima popust
    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_price > 0 && $this->discount_price < $this->price;
    }

    // Link za detalje (axon shop ili amazon)
    public function getDetailUrlAttribute(): ?string
    {
        if ($this->axon_product_slug) {
            return route('product.show', $this->axon_product_slug);
        }
        return $this->amazon_url;
    }

    // Tip linka (axon ili amazon)
    public function getLinkTypeAttribute(): string
    {
        return $this->axon_product_slug ? 'axon' : 'amazon';
    }

    // Spec helper — čitanje jedne spec vrednosti
    public function spec(string $key, mixed $default = null): mixed
    {
        return data_get($this->specs, $key, $default);
    }

    
    public function builderSaves()
    {
        return $this->belongsToMany(BuilderSave::class, 'builder_saves', 'id', 'id')
            ->withTimestamps();
    }

   
    public static function componentTypes(): array
    {
        return [
            'cpu'         => 'Procesor (CPU)',
            'gpu'         => 'Grafička kartica',
            'motherboard' => 'Matična ploča',
            'ram'         => 'RAM memorija',
            'case'        => 'Kućište',
            'cpu_cooler'  => 'CPU Hlađenje',
            'case_fan'    => 'Ventilatori',
            'storage'     => 'Skladištenje (SSD)',
            'psu'         => 'Napajanje (PSU)',
        ];
    }

    public function toBuilderArray(): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'brand'        => $this->brand,
            'type'         => $this->component_type,
            'image'        => $this->image ? asset($this->image) : null,
            'price'        => $this->effective_price,
            'has_discount' => $this->has_discount,
            'orig_price'   => (float) $this->price,
            'detail_url'   => $this->detail_url,
            'link_type'    => $this->link_type,
            'short_desc'   => $this->short_desc,
            'perf_score'   => $this->perf_score,
            'tdmark_base'  => $this->tdmark_base,
            'fps_base_1080'=> $this->fps_base_1080,
            'socket'       => $this->socket,
            'ram_type'     => $this->ram_type,
            'form_factor'  => $this->form_factor,
            'specs'        => $this->specs,
        ];
    }
}