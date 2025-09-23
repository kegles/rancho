@extends('layouts.app')
@section('title','Revisar inscrição')

@section('content')
  <h1 class="h3 mb-3">Revisar inscrição</h1>

  @php
    $d = $draft['data'];
    $c = $draft['computed'];
    $statusLabels = ['PENDING'=>'Pendente','PAID'=>'Pago','CANCELLED'=>'Cancelado'];
    $catLabels = ['V'=>'Visitante','R'=>'Radioamador(a)','E'=>'Convidado(a) especial'];
    $ticketLabels = ['FULL'=>'Todo evento','DAY'=>'Apenas um dia'];
  @endphp

  <div class="card mb-3">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="text-muted">Nome (titular)</div>
          <div class="fw-semibold">{{ $d['name'] }}</div>
        </div>
        <div class="col-md-3">
          <div class="text-muted">Indicativo</div>
          <div class="fw-semibold">{{ $d['callsign'] ?? '—' }}</div>
        </div>
        <div class="col-md-3">
          <div class="text-muted">Telefone</div>
          <div class="fw-semibold">{{ $d['phone'] ?? '—' }}</div>
        </div>
        <div class="col-md-6">
          <div class="text-muted">Cidade</div>
          <div class="fw-semibold">{{ $d['city'] ?? '—' }}</div>
        </div>
        <div class="col-md-6">
          <div class="text-muted">E-mail</div>
          <div class="fw-semibold">{{ $d['email'] ?? '—' }}</div>
        </div>
        <div class="col-md-4">
          <div class="text-muted">Categoria (titular)</div>
          <div class="fw-semibold">{{ $catLabels[$d['category_code']] ?? $d['category_code'] }}</div>
        </div>
        <div class="col-md-4">
          <div class="text-muted">Tipo de inscrição</div>
          <div class="fw-semibold">
            {{ $ticketLabels[$d['ticket_type']] ?? $d['ticket_type'] }}
            @if($d['ticket_type']==='DAY')
              — Dia: {{ $d['days'] }}
            @endif
          </div>
        </div>
        <div class="col-md-4">
          <div class="text-muted">Pessoas</div>
          <div class="fw-semibold">
            Adultos: {{ $c['adultsCount'] }} • Crianças: {{ $c['childrenCount'] }} • Total: {{ $c['totalPeople'] }}
          </div>
        </div>
      </div>
    </div>
  </div>

  @if(count($c['attendees']))
    <div class="card mb-3">
      <div class="card-header">Pessoas adicionais</div>
      <div class="card-body">
        <ul class="mb-0">
          @foreach($c['attendees'] as $a)
            <li>{{ $a['name'] }} <span class="text-muted">— {{ $a['label'] }}</span></li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif

  <div class="card mb-3">
    <div class="card-header">Valores</div>
    <div class="card-body">
      <table class="table align-middle mb-0">
        <tbody>
          <tr>
            <th style="width:320px">Inscrição base</th>
            <td>R$ {{ number_format($c['base']/100, 2, ',', '.') }}</td>
          </tr>
          @foreach ($c['items'] as $it)
          <tr>
            <th>{{ $it['name'] }} <span class="text-muted">× {{ $it['qty'] }}</span></th>
            <td>R$ {{ number_format($it['subtotal']/100, 2, ',', '.') }}</td>
          </tr>
          @endforeach
          <tr class="table-light">
            <th class="fs-5">Total</th>
            <td class="fs-5 fw-bold">R$ {{ number_format($c['total']/100, 2, ',', '.') }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

    <div class="d-flex gap-2">
        {{-- Confirmar: grava no banco --}}
        <form method="post" action="{{ route('registration.confirm') }}">
            @csrf
            <button class="btn btn-success btn-lg">Confirmar inscrição</button>
        </form>

        {{-- Corrigir: volta ao formulário anterior --}}
        <button type="button" class="btn btn-outline-secondary" onclick="history.go(-1)">
            Corrigir inscrição
        </button>
    </div>
@endsection
