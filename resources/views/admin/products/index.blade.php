{{-- resources/views/admin/products/index.blade.php --}}
@extends('layouts.admin')
@section('title','Admin — Produtos')

@section('content')
  <h1 class="h3 mb-3">Produtos</h1>

  {{-- ALERTAS --}}
  @if (session('ok'))
    <div class="alert alert-success" role="alert">
      {{ session('ok') }}
    </div>
  @endif
  @if (session('err'))
    <div class="alert alert-danger" role="alert">
      {{ session('err') }}
    </div>
  @endif

  <form class="row g-3 align-items-end mb-3" method="get">
    <div class="col-md-4">
      <label for="q" class="form-label fw-semibold">Buscar por nome ou código</label>
      <input
        id="q"
        name="q"
        type="text"
        value="{{ $q }}"
        placeholder="Digite para filtrar"
        class="form-control"
      >
    </div>
    <div class="col-md-8 d-flex gap-2 align-items-end">
      <button class="btn btn-primary">Filtrar</button>
      <a href="{{ route('admin.products.create') }}" class="btn btn-success">Novo produto</a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead class="table-light">
        <tr>
          <th>Código</th>
          <th>Descrição</th>
          <th>Preço</th>
          <th>Meia p/ criança</th>
          <th>Ativo</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse ($products as $p)
          <tr>
            <td>{{ $p->sku }}</td>
            <td>{{ $p->name }}</td>
            <td>R$ {{ number_format($p->price/100, 2, ',', '.') }}</td>
            <td>
              <span class="badge text-bg-{{ $p->is_child_half ? 'info' : 'secondary' }}">
                {{ $p->is_child_half ? 'Sim' : 'Não' }}
              </span>
            </td>
            <td>
              <span class="badge text-bg-{{ $p->active ? 'success' : 'danger' }}">
                {{ $p->active ? 'Sim' : 'Não' }}
              </span>
            </td>
            <td class="text-end">
              <a href="{{ route('admin.products.edit',$p) }}" class="btn btn-sm btn-outline-primary">
                Editar
              </a>
              <form method="post" action="{{ route('admin.products.destroy',$p) }}" class="d-inline"
                onsubmit="return confirm('Remover o produto {{ addslashes($p->name) }}?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Excluir</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-4">Nenhum produto cadastrado.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{ $products->onEachSide(1)->links() }}
@endsection
