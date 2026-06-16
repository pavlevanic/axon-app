@extends('layouts.admin')

@section('admin_content')
<div class="py-2">
    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark m-0">PC Builder — Komponente</h2>
            <p class="text-muted mb-0">Upravljanje komponentama za PC Builder alat</p>
        </div>
        <a href="{{ route('builder-products.create') }}" class="btn btn-primary text-white btn-sm rounded-0 px-4">
            <i class="bi bi-plus-lg"></i> Dodaj novi proizvod
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Naziv</th>
                            <th>Tip</th>
                            <th>Brend</th>
                            <th>Cena</th>
                            <th>Perf.</th>
                            <th>3DMark</th>
                            <th>FPS 1080p</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Akcije</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $item->name }}</div>
                                    @if($item->short_desc)
                                        <small class="text-muted">{{ Str::limit($item->short_desc, 60) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary text-uppercase" style="font-size: 0.7rem;">
                                        {{ $componentTypes[$item->component_type] ?? $item->component_type }}
                                    </span>
                                </td>
                                <td>{{ $item->brand ?? '—' }}</td>
                                <td>
                                    @if($item->has_discount)
                                        <span class="text-decoration-line-through text-muted small">€{{ number_format($item->price, 2) }}</span><br>
                                        <span class="fw-bold text-danger">€{{ number_format($item->discount_price, 2) }}</span>
                                    @else
                                        <span class="fw-bold">€{{ number_format($item->price, 2) }}</span>
                                    @endif
                                </td>
                                <td>{{ $item->perf_score ?: '—' }}</td>
                                <td>{{ $item->tdmark_base ?: '—' }}</td>
                                <td>{{ $item->fps_base_1080 ?: '—' }}</td>
                                <td>
                                    @if($item->is_active && $item->in_stock)
                                        <span class="text-success"><i class="bi bi-check-circle-fill"></i> Aktivno</span>
                                    @elseif($item->is_active)
                                        <span class="text-warning"><i class="bi bi-exclamation-circle-fill"></i> Nema na stanju</span>
                                    @else
                                        <span class="text-danger"><i class="bi bi-x-circle-fill"></i> Neaktivno</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="{{ route('builder-products.edit', $item) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('builder-products.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Obrisati ovu komponentu?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm rounded-0">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    Nema komponenti. <a href="{{ route('builder-products.create') }}">Dodaj prvu komponentu.</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($products->hasPages())
            <div class="card-footer bg-white">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
