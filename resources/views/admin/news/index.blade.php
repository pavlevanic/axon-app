@extends('layouts.admin')

@section('admin_content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark m-0">Upravljanje vestima</h2>
            <p class="text-muted">Pregled svih objavljenih vesti na AXON portalu</p>
        </div>
        <a href="{{ route('news.create') }}" class="btn btn-dark text-white btn-lg rounded-0 px-4">
            <i class="bi bi-plus-lg"></i> Nova Vest
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Slika</th>
                            <th>Naslov</th>
                            <th>Tip</th>
                            <th>Status</th>
                            <th>Datum</th>
                            <th class="text-end pe-4">Akcije</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($news as $item)
                            <tr>
                                <td class="ps-4">
                                    <img src="{{ asset($item->image) }}" class="rounded" style="width: 80px; height: 50px; object-fit: cover;">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->title }}</div>
                                    <small class="text-muted">{{ Str::limit($item->summary, 50) }}</small>
                                </td>
                                <td>
                                    @if($item->type == 'hero')
                                        <span class="badge bg-primary text-uppercase" style="font-size: 0.7rem;">Hero</span>
                                    @else
                                        <span class="badge bg-secondary text-uppercase" style="font-size: 0.7rem;">Normal</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->is_active)
                                        <span class="text-success"><i class="bi bi-check-circle-fill"></i> Aktivno</span>
                                    @else
                                        <span class="text-danger"><i class="bi bi-x-circle-fill"></i> Deaktivirano</span>
                                    @endif
                                </td>
                                <td>{{ $item->created_at->format('d.m.Y.') }}</td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="{{ route('news.show', $item->slug) }}" class="btn btn-outline-dark btn-sm">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('news.edit', $item->id) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('news.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Da li ste sigurni?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    Nema pronađenih vesti. <a href="{{ route('news.create') }}">Napravi prvu vest.</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection