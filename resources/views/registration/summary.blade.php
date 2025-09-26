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

            @php
            // formatação de dinheiro em R$
            $money      = fn (int $cents) => 'R$ ' . number_format($cents / 100, 2, ',', '.');
            $grandTotal = 0;

            // fator da meia: 50% por padrão, mas você pode ajustar em config/pricing.php
            $halfFactor = (float) config('pricing.child_half_factor', 0.5);

            // index dos produtos passados pelo controller (ex.: Product::whereIn('sku', ...)->get())
            $productsBySku = (isset($products) && method_exists($products, 'keyBy'))
                ? $products->keyBy('sku')
                : collect();
            @endphp


            @if(empty($c['items']))
            <p class="text-muted">Nenhum item selecionado.</p>
            @else
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                <thead>
                    <tr>
                    <th>Produto</th>
                    <th class="text-center">Inteira</th>
                    <th class="text-center">Meia</th>
                    <th class="text-end">Preço unit.</th>
                    <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($c['items'] as $item)
                    @php
                        // $item pode ser ARRAY (preview) ou OBJETO (depois que persistir)
                        // SKU:
                        $sku = data_get($item, 'sku'); // se seu array usa outra chave, ajuste aqui

                        // Product: primeiro tenta $item['product'] / $item->product; senão procura por SKU
                        $product = data_get($item, 'product');
                        if (!$product && $sku && $productsBySku) {
                            $product = $productsBySku->get($sku);
                        }

                        // Preço unit (centavos): prioridade unit_price do item; senão price do product; senão 0
                        $unit = (int) (data_get($item, 'unit_price') ?? data_get($product, 'price', 0));

                        // Quantidades
                        $qtyFull = (int) data_get($item, 'qty_full', data_get($item, 'qty', 0));
                        $qtyHalf = (int) data_get($item, 'qty_half', 0);

                        // Se produto não aceita meia, zera meia
                        $acceptsHalf = (bool) data_get($product, 'is_child_half', false);
                        if (!$acceptsHalf) {
                            $qtyHalf = 0;
                        }

                        // Preço da meia (centavos) sem FP: 50% -> intdiv; caso contrário round
                        $halfUnit = ($halfFactor === 0.5)
                            ? intdiv($unit, 2)
                            : (int) round($unit * $halfFactor);

                        // Total da linha
                        $lineTotal = ($qtyFull * $unit) + ($qtyHalf * $halfUnit);
                        $grandTotal += $lineTotal;

                        // Nome para exibição
                        $productName = data_get($item, 'name')
                            ?: data_get($product, 'name')
                            ?: ($sku ?? 'Item');

                    @endphp

                    @php $isBase = in_array(data_get($item,'sku'), ['BASE','BASE_SPOUSE']); @endphp
                    <tr @class(['table-light' => $isBase])>
                        <td><strong>{{ $productName }}</strong></td>
                        <td class="text-center">{{ $qtyFull }}</td>
                        <td class="text-center">
                        @if($acceptsHalf) {{ $qtyHalf }} @else — @endif
                        </td>
                        <td class="text-end">{{ $money($unit) }}</td>
                        <td class="text-end">{{ $money($lineTotal) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Total</th>
                        <th class="text-end">{{ $money($grandTotal) }}</th>
                    </tr>
                </tfoot>
                </table>
            </div>
            @endif


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
