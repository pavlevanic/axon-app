@extends('layouts.admin')

@section('admin_content')
<div class="py-2">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white fw-bold py-3">
            <i class="bi bi-plus-circle me-2"></i>Dodaj Builder komponentu
        </div>
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('builder-products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.builder-products._form')

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('builder-products.index') }}" class="btn btn-light border px-4">Odustani</a>
                    <button type="submit" class="btn btn-dark px-5">Sačuvaj komponentu</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
