@extends('layouts.admin')

@section('title','Relatório — Inscrições por produto')

@section('content')
  <h1 class="h3 mb-3">Relatório — Inscrições por produto</h1>

  <form method="get" action="{{ route('admin.reports.by_product') }}" class="row g-3 align-items-end mb-4">
    <div class="col-md-6">
      <label class="form-label fw-semibold">Produto</label>
      <select name="product_id" class="form-select" required>
        <option value="">Selecione um produto...</option>
        @foreach ($products as $p)
          <option value="{{ $p->id }}" @selected(request('product_id') == $p->id)>
            {{ $p->sku }} — {{ $p->name }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <button class="btn btn-primary">Gerar relatório</button>
    </div>
  </form>

  @if($product)
    <hr>

    <h2 class="h4 mb-2">
      Produto selecionado:
      <strong>{{ $product->sku }} — {{ $product->name }}</strong>
    </h2>
    <p class="text-muted">
      Total de inscrições que possuem este produto: {{ $regs->count() }}
    </p>

    @forelse ($regs as $r)
      @php
        // Assumindo apenas um item por produto por inscrição; se tiver mais, pode somar com ->sum()
        $item = $r->items->first();
        $qtd  = ($item->qty_full ?? 0) + ($item->qty_half ?? 0);
      @endphp

      <div class="card mb-3">
        <div class="card-header d-flex justify-content-between">
          <span>
            <strong>Inscrição #{{ $r->reg_number }}</strong>
            — Titular: {{ $r->participant->name }}
          </span>
          <span>
            <strong>Qtd. do produto:</strong> {{ $qtd }}
          </span>
        </div>
        <div class="card-body">
          <p class="mb-2">
            <strong>Indicativo:</strong> {{ $r->participant->callsign }}
            &nbsp;|&nbsp;
            <strong>Cidade:</strong> {{ $r->participant->city }}
          </p>

          <h6 class="mt-3">Pessoas desta inscrição</h6>
          <ul class="mb-0">
            <li><strong>Titular:</strong> {{ $r->participant->name }}</li>
            @foreach ($r->attendees as $a)
              @php
                $label = $a->role;
                if ($a->role === 'SPOUSE')  $label = 'Cônjuge';
                if ($a->role === 'ACCOMP')  $label = 'Acompanhante';
                if ($a->role === 'CHILD')   $label = 'Criança';
              @endphp
              <li>
                <strong>{{ $label }}:</strong> {{ $a->name }}
              </li>
            @endforeach
          </ul>
        </div>
      </div>
    @empty
      <p class="text-muted">Nenhuma inscrição encontrada para este produto.</p>
    @endforelse
  @endif
@endsection
