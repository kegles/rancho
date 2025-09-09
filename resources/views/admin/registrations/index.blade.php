@extends('layouts.app')
@section('title','Admin — Inscrições')

@section('content')
  <h1 class="h3 mb-3">Inscrições</h1>

  @php
    $statusLabels = [
      'PENDING'   => 'Pendente',
      'PAID'      => 'Pago',
      'CANCELLED' => 'Cancelado',
    ];
    $categoryLabels = [
      'R'     => 'Radioamador',
      'C'     => 'Cristal',
      'A'     => 'Acompanhante',
      'CH'    => 'Criança',
      'GUEST' => 'Convidado',
      'ORG'   => 'Organização',
    ];
  @endphp

  <form class="row g-3 align-items-end mb-3" method="get">
    <div class="col-md-3">
      <label for="status" class="form-label fw-semibold">Status</label>
      <select id="status" name="status" class="form-select">
        <option value="">(todos)</option>
        @foreach ($statusLabels as $value => $label)
          <option value="{{ $value }}" @selected(request('status')===$value)>{{ $label }}</option>
        @endforeach
      </select>
    </div>

    <div class="col-md-3">
      <label for="category" class="form-label fw-semibold">Categoria</label>
      <select id="category" name="category" class="form-select">
        <option value="">(todas)</option>
        @foreach ($categoryLabels as $value => $label)
          <option value="{{ $value }}" @selected(request('category')===$value)>{{ $label }}</option>
        @endforeach
      </select>
    </div>

    <div class="col-md-6 d-flex gap-2 align-items-end">
      <button class="btn btn-primary">Filtrar</button>
      <a class="btn btn-outline-secondary" href="{{ route('admin.reg.export') }}">Exportar CSV</a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th><th>Reg</th><th>Status</th><th>Nome</th><th>Indicativo</th>
          <th>Cidade</th><th>Categoria</th><th>Tipo</th><th>Total</th><th>Sorteio</th><th></th>
        </tr>
      </thead>
      <tbody>
      @foreach ($regs as $r)
        <tr>
          <td>{{ $r->id }}</td>
          <td>{{ $r->reg_number }}</td>
          <td>
            @php $label = $statusLabels[$r->status] ?? $r->status; @endphp
            <span class="badge text-bg-{{ $r->status==='PAID'?'success':($r->status==='PENDING'?'warning':'secondary') }}">
              {{ $label }}
            </span>
          </td>
          <td>{{ $r->participant->name }}</td>
          <td>{{ $r->participant->callsign }}</td>
          <td>{{ $r->participant->city }}</td>
          <td>{{ $categoryLabels[$r->participant->category_code] ?? $r->participant->category_code }}</td>
          <td>{{ $r->ticket_type }}</td>
          <td>R$ {{ number_format($r->total_price/100,2,',','.') }}</td>
          <td>{{ $r->eligible_draw ? 'SIM' : 'NÃO' }}</td>
            <td class="text-end">
                <div class="d-inline-flex gap-2">
                    @if ($r->status!=='PAID')
                    <form method="post" action="{{ route('admin.reg.paid',$r->id) }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-success">Marcar como pago</button>
                    </form>
                    @endif

                    {{-- Botão Excluir (soft delete) --}}
                    <form method="post" action="{{ route('admin.reg.destroy', $r->id) }}"
                        onsubmit="return confirm('Confirma a exclusão da inscrição de {{ addslashes($r->participant->name) }}?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Excluir</button>
                    </form>
                </div>
            </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>

  {{ $regs->onEachSide(1)->links() }}
@endsection
