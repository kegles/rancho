@extends('layouts.app')

@section('title','Inscrição — Rancho do Radioamador Gaúcho')

@section('content')
  <div class="row">
    <div class="col-lg-10 col-xl-8">
      <h1 class="h3 mb-3">Inscrição</h1>

      @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
      </div>
      @endif

      <form method="post" action="{{ route('registration.preview') }}" class="needs-validation" novalidate>
        @csrf

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label fw-bold">Nome</label>
            <input name="name" value="{{ old('name') }}" required class="form-control" autofocus>
          </div>
          <div class="col-md-3">
            <label class="form-label">Indicativo</label>
            <input name="callsign" value="{{ old('callsign') }}" class="form-control text-uppercase">
          </div>
          <div class="col-md-3">
            <label class="form-label">Telefone</label>
            <input name="phone" value="{{ old('phone') }}" class="form-control">
          </div>
        </div>
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label">Cidade</label>
            <input name="city" value="{{ old('city') }}" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control">
          </div>
        </div>
        <div class="row g-3 mb-3">

            <div class="col-md-6">
                <label class="form-label fw-bold">Tipo de inscrição (titular)</label>
                @php $cat = old('category_code','V'); @endphp
                <select name="category_code" class="form-select" required>
                    <option value="V" @selected($cat==='V')>Visitante</option>
                    <option value="R" @selected($cat==='R')>Radioamador(a)</option>
                    <option value="E" @selected($cat==='E')>Convidado(a) especial</option>
                </select>
            </div>

        </div>

        <div class="row g-3 mt-4 mb-4">
            {{-- Cônjuge --}}
            <div class="col-12">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" role="switch" id="has_spouse"
                        name="has_spouse" value="1" {{ old('has_spouse') ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_spouse">Meu cônjuge vai comigo</label>
                </div>
                <div id="spouse_block" class="col-6" style="display:none">
                    <label class="form-label fw-bold">Nome do cônjuge</label>
                    <input class="form-control" name="spouse_name" value="{{ old('spouse_name') }}">
                </div>
            </div>
        </div>

        <div class="row g-3 mt-2">
            {{-- Crianças --}}
            <div class="col-md-3">
                <label class="form-label">Número de crianças</label>
                <input type="number" min="0" class="form-control" id="children_count" name="children_count"
                        value="{{ old('children_count', 0) }}">
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-12 mt-3 mb-0 pt-0 pb-0">
                <div class="form-text mt-0 mb-0 pt-0 pb-0">
                    * <strong>Crianças (de 7 à 12 anos)</strong> pagam meia inscrição e meio valor das refeições.
                </div>
            </div>
            <div class="col-md-6 mt-2" id="children_names"></div>
        </div>

        <div class="row g-3 mt-2">
            {{-- Acompanhantes --}}
            <div class="col-md-3">
                <label class="form-label">Demais acompanhantes</label>
                <input type="number" min="0" class="form-control" id="companions_count" name="companions_count"
                        value="{{ old('companions_count', 0) }}">
            </div>
        </div>
        <div class="row g-3 mt-2">
            <div class="col-md-6" id="companions_names" class="mt-2"></div>
        </div>

        <div class="row g-3 mt-0">
            <div class="col-md-8">
                <label class="form-label fw-bold">Tipo de inscrição</label>
                @php $tt = old('ticket_type','FULL'); @endphp
                <select name="ticket_type" class="form-select" id="ticket_type" required>
                    <option value="FULL" @selected($tt==='FULL')>Todo evento (Ingresso: R$ 50,00 - Crianças: R$ 25,00)</option>
                    <option value="DAY"  @selected($tt==='DAY')>Apenas um dia (Ingresso: R$ 30,00 - Crianças: R$ 15,00)</option>
                </select>
            </div>
            <div class="col-md-4" id="day-only" style="display:none">
                <label class="form-label fw-bold">Qual dia?</label>
                @php $dy = old('days',''); @endphp
                <select name="days" id="day_select" class="form-select">
                    <option value="22" @selected($dy==='22')>Dia 22 (sábado)</option>
                    <option value="23" @selected($dy==='23')>Dia 23 (domingo)</option>
                </select>
            </div>
        </div>


        <div class="row g-3 mt-0 mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Itens adicionais</span>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge text-bg-secondary" id="adults_badge">Adultos: 1</span>
                        <span class="badge text-bg-secondary" id="children_badge">Crianças: 0</span>
                    </div>
                    </div>
                    <div class="card-body">
                    @foreach ($products as $p)
                        @php
                        $sku = $p->sku;
                        $checked = old("products.$sku.selected") ? true : false;
                        @endphp
                        <div class="row align-items-center g-2 mb-2">
                        <div class="col-md-8">
                            <div class="form-check">
                            <input class="form-check-input product-check" type="checkbox"
                                    name="products[{{ $sku }}][selected]" value="1"
                                    id="chk_{{ $sku }}" @checked($checked)>
                            <label class="form-check-label" for="chk_{{ $sku }}">
                                <strong>{{ $p->name }}</strong> — R$ {{ number_format($p->price/100,2,',','.') }}
                            </label>
                            </div>
                        </div>
                        {{-- Mantemos um qty oculto só para compatibilidade, mas será ignorado no backend --}}
                        <input type="hidden" name="products[{{ $sku }}][qty]" value="1">
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>


            {{-- Troca-Troca --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Troca-Troca (opcional)</div>
                    <div class="card-body row g-3">
                    <div class="col-md-6 pb-5">
                        <label class="form-label">Sou:</label>
                        @php $role = old('trade_role',''); @endphp
                        <select name="trade_role" id="trade_role" class="form-select">
                            <option value="" @selected($role==='')>Não vou participar</option>
                            <option value="AMADOR" @selected($role==='AMADOR')>Radioamador (itens pessoais usados)</option>
                            <option value="REVENDEDOR" @selected($role==='REVENDEDOR')>Revendedor (doação mínima R$ 150,00)</option>
                        </select>
                    </div>
                    <div class="col-md-6" id="donation-field" style="display:none">
                        <label class="form-label fw-bold">Compromisso de doação (em reais)</label>
                        <input type="number" name="trade_donation_pledge"
                            value="{{ old('trade_donation_pledge')??'150' }}" min="150" step="1"  class="form-control">
                        <div class="form-text">Valor mínimo: R$ 150,00</div>
                    </div>
                    </div>
                </div>
            </div>



          <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary">Próximo passo</button>
          </div>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
<script>
function toggleSpouse(){
  const chk = document.getElementById('has_spouse');
  document.getElementById('spouse_block').style.display = chk.checked ? 'block' : 'none';
  updatePeopleBadge();
}

function renderList(containerId, count, baseName, oldValues = []){
  const c = document.getElementById(containerId);
  c.innerHTML = '';
  count = parseInt(count || 0, 10);
  for (let i=0;i<count;i++){
    const div = document.createElement('div');
    div.className = 'mb-2';
    div.innerHTML = `
      <label class="form-label">${baseName} #${i+1}</label>
      <input class="form-control" name="${containerId.replace('_names','')}_names[${i}]" value="${oldValues[i] ?? ''}">
    `;
    c.appendChild(div);
  }
}

function updatePeopleBadge(){
  const hasSpouse = document.getElementById('has_spouse').checked ? 1 : 0;
  const companions = parseInt(document.getElementById('companions_count').value || 0,10);
  const children   = parseInt(document.getElementById('children_count').value || 0,10);
  const adults = 1 + hasSpouse + companions; // 1 = titular
  const elAdults   = document.getElementById('adults_badge');
  const elChildren = document.getElementById('children_badge');
  if (elAdults)   elAdults.textContent   = 'Adultos: '  + adults;
  if (elChildren) elChildren.textContent = 'Crianças: ' + children;
}

/* === mostra/esconde campo doação quando trade_role = REVENDEDOR === */
function syncDonation(){
  const roleSelect = document.getElementById('trade_role');
  const donationDiv = document.getElementById('donation-field');
  const isRevendedor = roleSelect && roleSelect.value === 'REVENDEDOR';
  if (donationDiv) donationDiv.style.display = isRevendedor ? 'block' : 'none';
}

/* === NOVO: exibe o seletor de dia quando ticket_type = DAY === */
function syncDay(){
  const ticketEl = document.getElementById('ticket_type');
  const dayBlock = document.getElementById('day-only');
  const daySel   = document.getElementById('day_select');
  const isDay    = ticketEl && ticketEl.value === 'DAY';
  if (dayBlock) dayBlock.style.display = isDay ? 'block' : 'none';
  if (daySel)   daySel.required = isDay;
}

document.addEventListener('DOMContentLoaded', function(){
  // spouse
  toggleSpouse();
  document.getElementById('has_spouse').addEventListener('change', toggleSpouse);

  // companions
  const companionsCount = document.getElementById('companions_count');
  const oldCompanions = @json(old('companions_names', []));
  renderList('companions_names', companionsCount.value, 'Acompanhante', oldCompanions);
  companionsCount.addEventListener('input', function(){
    renderList('companions_names', this.value, 'Acompanhante');
    updatePeopleBadge();
  });

  // children
  const childrenCount = document.getElementById('children_count');
  const oldChildren = @json(old('children_names', []));
  renderList('children_names', childrenCount.value, 'Criança', oldChildren);
  childrenCount.addEventListener('input', function(){
    renderList('children_names', this.value, 'Criança');
    updatePeopleBadge();
  });

  // NOVO: DAY selector
  syncDay();
  document.getElementById('ticket_type').addEventListener('change', syncDay);

  // NOVO: trade_role
  syncDonation();
  document.getElementById('trade_role').addEventListener('change', syncDonation);

  updatePeopleBadge();
});
</script>
@endpush

