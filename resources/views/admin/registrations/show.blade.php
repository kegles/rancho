@extends('layouts.admin')
@section('title','Admin — Inscrição #'.$registration->reg_number)

@section('content')
@php
  $reg = $registration;
  $p   = $reg->participant;
  $money = fn (int $c) => 'R$ ' . number_format($c/100, 2, ',', '.');

  // nomes para SKUs técnicos, caso produto não exista
  $techNames = [
    'BASE'        => 'Inscrição (Participante)',
    'BASE_SPOUSE' => 'Inscrição (Cônjuge)',
    'DONATION'    => 'Doação Revendedor',
  ];

  $T_STATUS = [
    'PENDING'  => 'Pendente',
    'PAID'     => 'Pago',
    'CANCELED' => 'Cancelada',
  ];

  $T_TICKET = [
    'FULL' => 'Pacote completo',
    'DAY'  => 'Ingresso diário',
  ];

  $T_CATEGORY = [
    'R' => 'Radioamador',
    'V' => 'Visitante',
    'E' => 'Isento',
  ];

  $T_ROLE = [
    'SPOUSE'     => 'Cônjuge',
    'ACCOMP'     => 'Acompanhante',
    'CHILD'      => 'Criança',
    'PARTICIPANT'=> 'Titular',
  ];

  $T_TRADEROLE = [
    'REVENDEDOR' => 'Revendedor',
    'EXPOSITOR'  => 'Expositor',
  ];

  // helperzinho local com fallback
  $label = fn(array $map, $code) => $map[$code] ?? ($code ?? '—');

@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">
    Inscrição #{{ $reg->reg_number }} — {{ $p->name }}
  </h1>
  <a href="{{ route('admin.reg.index') }}" class="btn btn-sm btn-secondary">Voltar</a>
</div>

<div class="row g-3">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">Participante</div>
      <div class="card-body">
        <div><strong>Nome:</strong> {{ $p->name }}</div>
        <div><strong>Indicativo:</strong> {{ $p->callsign ?? '—' }}</div>
        <div><strong>Cidade:</strong> {{ $p->city ?? '—' }}</div>
        <div><strong>E-mail:</strong> {{ $p->email ?? '—' }}</div>
        <div><strong>Telefone:</strong> {{ $p->phone ?? '—' }}</div>
        <div><strong>Categoria:</strong> {{ $label($T_CATEGORY, $registration->participant->category_code) }}</div>
        @if($p->trade_role)
          <div><strong>Troca-troca:</strong> {{ $label($T_TRADEROLE, $registration->participant->trade_role) }}</div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card">
      <div class="card-header">Inscrição</div>
      <div class="card-body">
        <div><strong>Status:</strong> {{ $label($T_STATUS, $registration->status) }}</div>
        <br />
        <div><strong>Ingresso:</strong> {{ $label($T_TICKET, $registration->ticket_type) }}</div>
        <div><strong>Dias:</strong> {{ $reg->days }}</div>
        <div><strong>Concorre ao sorteio?</strong> {{ $reg->eligible_draw ? 'Sim' : 'Não' }}</div>
        <br />
        <div><strong>Total:</strong> {{ $money($reg->total_price) }}</div>
      </div>
    </div>
  </div>

  @if($reg->attendees->isNotEmpty())
  <div class="col-12">
    <div class="card">
      <div class="card-header">Acompanhantes</div>
      <div class="card-body">
        <ul class="mb-0">
          @foreach($reg->attendees as $a)
            <li>
              <strong>{{ $label($T_ROLE, $a->role) }}</strong>: {{ $a->name }}
              @if(!empty($a->callsign))
                <span class="text-muted">({{ $a->callsign }})</span>
              @endif
            </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
  @endif

  <div class="col-12">
    <div class="card">
      <div class="card-header">Inscrição, refeições e adicionais</div>
      <div class="card-body table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th>Produto</th>
              <th class="text-center">Inteira</th>
              <th class="text-center">Meia</th>
              <th class="text-end">Preço unit.</th>
              <th class="text-end">Subtotal</th>
            </tr>
          </thead>
          <tbody>
          @php $grand = 0; @endphp
          @foreach($reg->items as $item)
            @php
              $unit  = (int) $item->unit_price;
              $full  = (int) $item->qty_full;
              $half  = (int) $item->qty_half;
              $halfU = intdiv($unit, 2);
              $line  = $full*$unit + $half*$halfU;
              $grand += $line;

              $name = optional($item->product)->name
                      ?? ($techNames[$item->sku] ?? $item->sku);
            @endphp
            <tr>
              <td><strong>{{ $name }}</strong></td>
              <td class="text-center">{{ $full }}</td>
              <td class="text-center">{{ $half > 0 ? $half : '—' }}</td>
              <td class="text-end">{{ $money($unit) }}</td>
              <td class="text-end">{{ $money($line) }}</td>
            </tr>
          @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th colspan="4" class="text-end">Total</th>
              <th class="text-end">{{ $money($grand) }}</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
