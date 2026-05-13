@extends('layouts.admin')

@section('admin_content')
        <!-- Glavni sadržaj -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Admin Dashboard</h2>
                <span class="badge bg-dark p-2">Ulogovan kao: {{ Auth::user()->name }}</span>
            </div>

            <div class="row g-4">
                <!-- Kartica 1 -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-dark text-white">
                        <div class="card-body p-4">
                            <i class="bi bi-people fs-1 mb-2"></i>
                            <h4>{{ $userCount }} Korisnika</h4>
                            <p class="mb-0">Pregled svih registrovanih članova.</p>
                        </div>
                    </div>
                </div>

                <!-- Kartica 2 -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-dark text-white">
                        <div class="card-body p-4">
                            <i class="bi bi-file-earmark-post fs-1 mb-2"></i>
                            <h4>{{ $productCount }} Proizvoda</h4>
                            <p class="mb-0">Dodaj, obriši ili izmeni pametne uređaje.</p>
                        </div>
                    </div>
                </div>

                <!-- Kartica 3 -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-dark text-white">
                        <div class="card-body p-4">
                            <i class="bi bi-graph-up fs-1 mb-2"></i>
                            <h4>{{ $categoryCount }} Kategorija</h4>
                            <p class="mb-0">Prati posete i prodaju u realnom vremenu.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-5">
                <div class="card-header bg-white fw-bold py-3">Recent Activities</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Akcija</th>
                                    <th>Tip</th>
                                    <th>Vreme</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities as $activity)
                                    <tr>
                                        <td>
                                            @if($activity->activity_type == 'proizvod')
                                                <i class="bi bi-box-seam me-2 text-dark"></i>
                                            @elseif($activity->activity_type == 'kategorija')
                                                <i class="bi bi-tags me-2 text-dark"></i>
                                            @else
                                                <i class="bi bi-newspaper me-2 text-dark"></i>
                                            @endif
                                            {!! $activity->activity_title !!}
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                {{ ucfirst($activity->activity_type) }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4">Nema nedavnih aktivnosti.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
