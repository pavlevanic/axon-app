@php
    $product = $product ?? null;
    $specs = $product ? ($product->specs ?? []) : [];
    $isEdit = (bool) $product;
@endphp

<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <label class="form-label fw-bold">Naziv komponente</label>
            <input type="text" name="name" id="bpName" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $product?->name ?? '') }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Slug (URL)</label>
            <input type="text" name="slug" id="bpSlug" class="form-control @error('slug') is-invalid @enderror"
                   value="{{ old('slug', $product?->slug ?? '') }}" required>
            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Brend</label>
                <input type="text" name="brand" class="form-control" value="{{ old('brand', $product?->brand ?? '') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Tip komponente</label>
                <select name="component_type" id="componentType" class="form-select @error('component_type') is-invalid @enderror" required>
                    <option value="">— Izaberi tip —</option>
                    @foreach($componentTypes as $key => $label)
                        <option value="{{ $key }}" @selected(old('component_type', $product?->component_type ?? '') === $key)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('component_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Kratak opis</label>
            <textarea name="short_desc" class="form-control" rows="2">{{ old('short_desc', $product?->short_desc ?? '') }}</textarea>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Cena (€)</label>
                <input type="number" step="0.01" name="price" class="form-control @error('price') is-invalid @enderror"
                       value="{{ old('price', $product?->price ?? '') }}" required>
                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Popust cena (€)</label>
                <input type="number" step="0.01" name="discount_price" class="form-control"
                       value="{{ old('discount_price', $product?->discount_price ?? 0) }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Slika</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                @if($isEdit && $product->image)
                    <small class="text-muted">Trenutna: {{ $product->image }}</small>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Axon shop slug</label>
                <input type="text" name="axon_product_slug" class="form-control"
                       value="{{ old('axon_product_slug', $product?->axon_product_slug ?? '') }}"
                       placeholder="slug-proizvoda-na-sajtu">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Amazon URL</label>
                <input type="url" name="amazon_url" class="form-control"
                       value="{{ old('amazon_url', $product?->amazon_url ?? '') }}">
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-primary mb-3">
            <div class="card-header bg-primary text-white fw-bold py-2">Performanse</div>
            <div class="card-body">
                <div class="mb-3 perf-field" data-perf-for="cpu,gpu,motherboard,ram,case,cpu_cooler,case_fan,storage,psu">
                    <label class="form-label fw-bold">Perf. score (0–1000)</label>
                    <input type="number" name="perf_score" class="form-control"
                           value="{{ old('perf_score', $product?->perf_score ?? 0) }}">
                </div>
                <div class="mb-3 perf-field" data-perf-for="gpu">
                    <label class="form-label fw-bold">3DMark Time Spy (GPU)</label>
                    <input type="number" name="tdmark_base" class="form-control"
                           value="{{ old('tdmark_base', $product?->tdmark_base ?? 0) }}">
                </div>
                <div class="mb-3 perf-field" data-perf-for="gpu">
                    <label class="form-label fw-bold">Bazni FPS @ 1080p (GPU)</label>
                    <input type="number" name="fps_base_1080" class="form-control"
                           value="{{ old('fps_base_1080', $product?->fps_base_1080 ?? 0) }}">
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                           @checked(old('is_active', $product?->is_active ?? true))>
                    <label class="form-check-label fw-bold" for="isActive">Aktivno u builderu</label>
                </div>
            </div>
        </div>
    </div>
</div>

<hr class="my-4">

<h5 class="fw-bold mb-3"><i class="bi bi-cpu me-2"></i>Specifikacije (JSON polja)</h5>
<p class="text-muted small mb-3">Izaberite tip komponente — prikazaće se samo relevantna polja. Čuvaju se u <code>specs</code> JSON koloni.</p>

<div id="specEmptyHint" class="alert alert-light border text-muted small py-3">
    <i class="bi bi-info-circle me-1"></i> Izaberite <strong>Tip komponente</strong> iznad da biste videli specifikacije.
</div>

<div class="row" id="specFieldsRow">
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="cpu,motherboard,cpu_cooler">
        <label class="form-label fw-bold">Socket</label>
        <input type="text" name="spec_socket" class="form-control" placeholder="AM5, LGA1700…"
               value="{{ old('spec_socket', $specs['socket'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="cpu,motherboard,ram">
        <label class="form-label fw-bold">RAM tip</label>
        <input type="text" name="spec_ram_type" class="form-control" placeholder="DDR5"
               value="{{ old('spec_ram_type', $specs['ram_type'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="motherboard,case,storage">
        <label class="form-label fw-bold">Form factor</label>
        <input type="text" name="spec_form_factor" class="form-control" placeholder="ATX, M.2 2280…"
               value="{{ old('spec_form_factor', $specs['form_factor'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="cpu,gpu">
        <label class="form-label fw-bold">TDP (W)</label>
        <input type="number" name="spec_tdp" class="form-control"
               value="{{ old('spec_tdp', $specs['tdp'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="gpu,psu">
        <label class="form-label fw-bold">Wattage (W)</label>
        <input type="number" name="spec_wattage" class="form-control"
               value="{{ old('spec_wattage', $specs['wattage'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="cpu">
        <label class="form-label fw-bold">Jezgra (CPU)</label>
        <input type="number" name="spec_cores" class="form-control"
               value="{{ old('spec_cores', $specs['cores'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="cpu">
        <label class="form-label fw-bold">Niti (CPU)</label>
        <input type="number" name="spec_threads" class="form-control"
               value="{{ old('spec_threads', $specs['threads'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="cpu">
        <label class="form-label fw-bold">Boost GHz (CPU)</label>
        <input type="number" step="0.1" name="spec_boost_ghz" class="form-control"
               value="{{ old('spec_boost_ghz', $specs['boost_ghz'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="gpu">
        <label class="form-label fw-bold">VRAM GB (GPU)</label>
        <input type="number" name="spec_vram_gb" class="form-control"
               value="{{ old('spec_vram_gb', $specs['vram_gb'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="ram,storage">
        <label class="form-label fw-bold">Kapacitet GB</label>
        <input type="number" name="spec_capacity_gb" class="form-control"
               value="{{ old('spec_capacity_gb', $specs['capacity_gb'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="ram">
        <label class="form-label fw-bold">Brzina MHz (RAM)</label>
        <input type="number" name="spec_speed_mhz" class="form-control"
               value="{{ old('spec_speed_mhz', $specs['speed_mhz'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="case">
        <label class="form-label fw-bold">Max matična ploča</label>
        <input type="text" name="spec_max_mobo" class="form-control" placeholder="E-ATX"
               value="{{ old('spec_max_mobo', $specs['max_mobo'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="case">
        <label class="form-label fw-bold">Max GPU mm</label>
        <input type="number" name="spec_max_gpu_mm" class="form-control"
               value="{{ old('spec_max_gpu_mm', $specs['max_gpu_mm'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="case">
        <label class="form-label fw-bold">Max cooler mm</label>
        <input type="number" name="spec_max_cooler_mm" class="form-control"
               value="{{ old('spec_max_cooler_mm', $specs['max_cooler_mm'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="cpu_cooler">
        <label class="form-label fw-bold">Tip hladnjaka</label>
        <input type="text" name="spec_cooler_type" class="form-control" placeholder="AIO, Air"
               value="{{ old('spec_cooler_type', $specs['type'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="cpu_cooler">
        <label class="form-label fw-bold">Radiator mm</label>
        <input type="number" name="spec_radiator_mm" class="form-control"
               value="{{ old('spec_radiator_mm', $specs['radiator_mm'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="cpu_cooler">
        <label class="form-label fw-bold">Max TDP hladnjak</label>
        <input type="number" name="spec_max_tdp" class="form-control"
               value="{{ old('spec_max_tdp', $specs['max_tdp'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="cpu_cooler">
        <label class="form-label fw-bold">Visina mm (Air cooler)</label>
        <input type="number" name="spec_height_mm" class="form-control"
               value="{{ old('spec_height_mm', $specs['height_mm'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="case_fan">
        <label class="form-label fw-bold">Veličina ventilatora mm</label>
        <input type="number" name="spec_size_mm" class="form-control"
               value="{{ old('spec_size_mm', $specs['size_mm'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="case_fan">
        <label class="form-label fw-bold">Broj ventilatora</label>
        <input type="number" name="spec_fan_count" class="form-control"
               value="{{ old('spec_fan_count', $specs['count'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="case_fan">
        <label class="form-label fw-bold">Max RPM</label>
        <input type="number" name="spec_max_rpm" class="form-control"
               value="{{ old('spec_max_rpm', $specs['max_rpm'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="storage">
        <label class="form-label fw-bold">Interfejs (SSD)</label>
        <input type="text" name="spec_interface" class="form-control" placeholder="PCIe 4.0 NVMe"
               value="{{ old('spec_interface', $specs['interface'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="storage">
        <label class="form-label fw-bold">Read MB/s</label>
        <input type="number" name="spec_read_mbs" class="form-control"
               value="{{ old('spec_read_mbs', $specs['read_mbs'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="storage">
        <label class="form-label fw-bold">Write MB/s</label>
        <input type="number" name="spec_write_mbs" class="form-control"
               value="{{ old('spec_write_mbs', $specs['write_mbs'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none" data-spec-for="psu">
        <label class="form-label fw-bold">Efikasnost (PSU)</label>
        <input type="text" name="spec_efficiency" class="form-control" placeholder="80+ Gold"
               value="{{ old('spec_efficiency', $specs['efficiency'] ?? '') }}">
    </div>
    <div class="col-md-3 mb-3 spec-field d-none d-flex align-items-end" data-spec-for="case_fan">
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="spec_rgb" id="specRgb" value="1"
                   @checked(old('spec_rgb', $specs['rgb'] ?? false))>
            <label class="form-check-label fw-bold" for="specRgb">RGB ventilatori</label>
        </div>
    </div>
    <div class="col-md-3 mb-3 spec-field d-none d-flex align-items-end" data-spec-for="psu">
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="spec_modular" id="specModular" value="1"
                   @checked(old('spec_modular', $specs['modular'] ?? false))>
            <label class="form-check-label fw-bold" for="specModular">Modularno PSU</label>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const typeSelect = document.getElementById('componentType');
    const emptyHint  = document.getElementById('specEmptyHint');

    function matchesType(el, type) {
        if (!type) return false;
        const allowed = (el.dataset.specFor || el.dataset.perfFor || '').split(',').map(s => s.trim());
        return allowed.includes(type);
    }

    function toggleFieldsByType() {
        const type = typeSelect?.value || '';

        document.querySelectorAll('.spec-field').forEach(el => {
            el.classList.toggle('d-none', !matchesType(el, type));
        });

        document.querySelectorAll('.perf-field').forEach(el => {
            el.classList.toggle('d-none', !matchesType(el, type));
        });

        if (emptyHint) {
            emptyHint.classList.toggle('d-none', !!type);
        }
    }

    typeSelect?.addEventListener('change', toggleFieldsByType);
    toggleFieldsByType();

    document.getElementById('bpName')?.addEventListener('input', function () {
        const slug = document.getElementById('bpSlug');
        if (slug && !slug.dataset.manual) {
            slug.value = this.value
                .toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }
    });

    document.getElementById('bpSlug')?.addEventListener('input', function () {
        this.dataset.manual = '1';
    });
})();
</script>
@endpush
