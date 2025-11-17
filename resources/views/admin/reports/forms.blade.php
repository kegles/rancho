@extends('layouts.admin')

@section('title','Relatório — Fichas de Inscrição')

@section('content')
  <style>
    /* Quebra de página ao imprimir */
    .page-break {
      page-break-after: always;
    }
  </style>

  <h1 class="h3 mb-3">Relatório — Fichas de Inscrição</h1>
  <p class="text-muted">
    Total de inscrições: {{ $regs->count() }}
  </p>

  @forelse ($regs as $r)
    <div class="card mb-4 page-break">
      <div class="card-header d-flex justify-content-between">
        <span><strong>Inscrição #{{ $r->reg_number }}</strong></span>
        <span class="text-muted">
          Status:
          <span class="badge text-bg-{{ $r->status === 'PAID' ? 'success' : 'warning' }}">
            {{ $r->status === 'PAID' ? 'Pago' : 'Pendente' }}
          </span>
        </span>
      </div>
      <div class="card-body">
        <h5 class="mb-3">Dados do titular</h5>
        <div class="row mb-2">
          <div class="col-md-6">
            <strong>Nome:</strong> {{ $r->participant->name }}
          </div>
          <div class="col-md-3">
            <strong>Indicativo:</strong> {{ $r->participant->callsign }}
          </div>
          <div class="col-md-3">
            <strong>Telefone:</strong> {{ $r->participant->phone }}
          </div>
        </div>

        <div class="row mb-2">
          <div class="col-md-6">
            <strong>Cidade:</strong> {{ $r->participant->city }}
          </div>
          <div class="col-md-6">
            <strong>E-mail:</strong> {{ $r->participant->email }}
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4">
            <strong>Categoria:</strong> {{ $r->participant->category_code }}
          </div>
          <div class="col-md-4">
            <strong>Tipo de inscrição:</strong> {{ $r->ticket_type }}
          </div>
          <div class="col-md-4">
            <strong>Dias:</strong> {{ $r->days }}
          </div>
        </div>

        {{-- Acompanhantes / Esposas / Crianças --}}
        <h5 class="mt-4 mb-2">Acompanhantes</h5>
        @if ($r->attendees->isEmpty())
          <p class="text-muted mb-3">Nenhum acompanhante informado.</p>
        @else
          <ul>
            @foreach ($r->attendees as $a)
              <li>
                @php
                  $label = $a->role;
                  if ($a->role === 'SPOUSE')  $label = 'Cônjuge';
                  if ($a->role === 'ACCOMP')  $label = 'Acompanhante';
                  if ($a->role === 'CHILD')   $label = 'Criança';
                @endphp
                <strong>{{ $label }}:</strong> {{ $a->name }}
                @if(!empty($a->callsign))
                    ({{ $a->callsign }})
                @endif
              </li>
            @endforeach
          </ul>
        @endif

        {{-- Produtos / Itens --}}
        <h5 class="mt-4 mb-2">Produtos / Itens</h5>
        @if ($r->items->isEmpty())
          <p class="text-muted">Nenhum item adicional.</p>
        @else
          <table class="table table-sm">
            <thead>
              <tr>
                <th>SKU</th>
                <th>Produto</th>
                <th class="text-end">Inteira</th>
                <th class="text-end">Meia</th>
                <th class="text-end">Unitário</th>
                <th class="text-end">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($r->items as $item)
                @php
                  $unit = $item->unit_price;
                  $subtotal = $item->qty_full * $unit + $item->qty_half * intdiv($unit, 2);
                @endphp
                <tr>
                  <td>{{ $item->sku }}</td>
                  <td>{{ $item->product->name ?? '' }}</td>
                  <td class="text-end">{{ $item->qty_full }}</td>
                  <td class="text-end">{{ $item->qty_half }}</td>
                  <td class="text-end">R$ {{ number_format($unit/100, 2, ',', '.') }}</td>
                  <td class="text-end">R$ {{ number_format($subtotal/100, 2, ',', '.') }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @endif

        <div class="mt-3 text-end">
          <strong>Total da inscrição:</strong>
          R$ {{ number_format($r->total_price/100, 2, ',', '.') }}
        </div>
      </div>
    </div>
  @empty
    <p class="text-muted">Nenhuma inscrição encontrada.</p>
  @endforelse
@endsection
