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
                @php $cat = old('category_code','R'); @endphp
                <select name="category_code" class="form-select" required>
                    <option value="R" @selected($cat==='R')>Radioamador(a)</option>
                    <option value="V" @selected($cat==='V')>Visitante</option>
                    <option value="E" @selected($cat==='E')>Convidado(a) especial</option>
                </select>
            </div>

        </div>

        <div class="row g-3 mt-4 mb-4 bg-light border pb-3">
            {{-- Cônjuge --}}
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input border border-secondary" type="checkbox" id="has_spouse"
                        name="has_spouse" value="1" {{ old('has_spouse') ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_spouse">Meu cônjuge vai comigo</label>
                </div>
                <div id="spouse_block" class="col-12" style="display:none">
                    <div class="row mt-3">
                        <div class="col-7">
                            <label class="form-label fw-bold">Nome do cônjuge</label>
                            <input class="form-control" name="spouse_name" value="{{ old('spouse_name') }}">
                        </div>
                        <div class="col-5">
                            <label class="form-label">Indicativo <span class="text-muted">(opcional)</span></label>
                            <input class="form-control text-uppercase" name="spouse_callsign"
                                value="{{ old('spouse_callsign') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-2">
            {{-- Crianças --}}
            <div class="col-md-3">
                <label class="form-label">Número de crianças</label>
                @php $childrenOld = (int) old('children_count', 0); @endphp
                <select id="children_count" name="children_count" class="form-select">
                    @for ($i = 0; $i <= 10; $i++)
                    <option value="{{ $i }}" @selected($i === $childrenOld)>{{ $i }}</option>
                    @endfor
                </select>
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
                @php $companionsOld = (int) old('companions_count', 0); @endphp
                <select id="companions_count" name="companions_count" class="form-select">
                    @for ($i = 0; $i <= 10; $i++)
                    <option value="{{ $i }}" @selected($i === $companionsOld)>{{ $i }}</option>
                    @endfor
                </select>
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
                    <span>Refeições e adicionais</span>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge text-bg-secondary" id="adults_badge">Adultos: 1</span>
                        <span class="badge text-bg-secondary" id="children_badge">Crianças: 0</span>
                    </div>
                </div>

                @php
                    $adults   = (int) old('adults', $registration->adults ?? 0);
                    $children = (int) old('children', $registration->children ?? 0);
                @endphp

                <div class="card-body">
                    @foreach ($products as $p)
                        @if (in_array($p->sku, ['BASE','BASE_SPOUSE','DONATION'])) @continue @endif

                        @php
                        $sku = $p->sku;

                        // valores padrão para primeira carga (serão recalculados via JS)
                        $adults   = 1 + (old('has_spouse') ? 1 : 0) + (int) old('companions_count', 0);
                        $children = (int) old('children_count', 0);

                        $defaultFull = $p->is_child_half ? $adults : ($adults + $children);
                        $defaultHalf = $p->is_child_half ? $children : 0;

                        $qtyFull = (int) old("products.$sku.qty_full", $defaultFull);
                        $qtyHalf = (int) old("products.$sku.qty_half", $defaultHalf);
                        @endphp

                        <div class="row align-items-center g-2 mb-2 product-row"
                            data-sku="{{ $sku }}"
                            data-ischildhalf="{{ $p->is_child_half ? 1 : 0 }}">
                        <div class="col-md-5">
                            <label class="form-label mb-0" for="qty_full_{{ $sku }}">
                            <strong>{{ $p->name }}</strong>
                            — R$ {{ number_format($p->price/100, 2, ',', '.') }}
                            </label>
                        </div>

                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                            <span class="input-group-text">Inteira</span>
                            @php $curFull = max(0, $qtyFull); @endphp
                            <select id="qty_full_{{ $sku }}" name="products[{{ $sku }}][qty_full]" class="form-select form-select-sm qty-full">
                                {{-- opções serão recriadas pelo JS; estas iniciais são só para primeira renderização --}}
                                @for ($i = 0; $i <= max(0,$defaultFull); $i++)
                                <option value="{{ $i }}" @selected($i === $curFull)>{{ $i }}</option>
                                @endfor
                            </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            @if ($p->is_child_half)
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Meia</span>
                                @php $curHalf = max(0, $qtyHalf); @endphp
                                <select id="qty_half_{{ $sku }}" name="products[{{ $sku }}][qty_half]" class="form-select form-select-sm qty-half">
                                @for ($i = 0; $i <= max(0,$defaultHalf); $i++)
                                    <option value="{{ $i }}" @selected($i === $curHalf)>{{ $i }}</option>
                                @endfor
                                </select>
                            </div>
                            @else
                            <input type="hidden" name="products[{{ $sku }}][qty_half]" value="0">
                            @endif
                        </div>
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

<div id="old-meta"
     data-companions='@json(old("companions_names", []))'
     data-children='@json(old("children_names", []))'
     style="display:none"></div>

@push('scripts')
<script>
const MAX_LIST_INPUTS = 20; // limite para listas de nomes
var metaEl = document.getElementById('old-meta');

function getEl(id){ return document.getElementById(id); }

function readOld(name){
    try {
        if (!metaEl) return [];
        var raw = metaEl.dataset[name] || '[]';
        return JSON.parse(raw);
    } catch(e){ return []; }
}

function computeCounts(){
  var spouseEl   = getEl('has_spouse');
  var companions = getEl('companions_count');
  var childrenEl = getEl('children_count');

  var hasSpouse  = (spouseEl && spouseEl.checked) ? 1 : 0;
  var compVal    = (companions && companions.value) ? companions.value : '0';
  var childVal   = (childrenEl && childrenEl.value) ? childrenEl.value : '0';

  var companionsCount = parseInt(compVal, 10) || 0;
  var childrenCount   = parseInt(childVal, 10) || 0;
  var adults          = 1 + hasSpouse + companionsCount; // 1 = titular

  return { adults: adults, children: childrenCount, total: adults + childrenCount };
}

function updatePeopleBadge(){
  var counts = computeCounts();
  var elAdults   = getEl('adults_badge');
  var elChildren = getEl('children_badge');
  if (elAdults)   elAdults.textContent   = 'Adultos: '  + counts.adults;
  if (elChildren) elChildren.textContent = 'Crianças: ' + counts.children;
}

function optionRange(selectEl, max, selected){
  if (!selectEl) return;
  var prev = (typeof selected !== 'undefined')
               ? parseInt(String(selected),10) || 0
               : (parseInt(String(selectEl.value || 0),10) || 0);
  var newVal = Math.min(Math.max(prev, 0), Math.max(0, max|0));
  var frag = document.createDocumentFragment();
  for (var i=0; i<=max; i++){
    var opt = document.createElement('option');
    opt.value = String(i);
    opt.textContent = String(i);
    opt.selected = true;
    frag.appendChild(opt);
  }
  selectEl.innerHTML = '';
  selectEl.appendChild(frag);
}

function syncProductLimits(){
  var counts = computeCounts();
  var adults = counts.adults;
  var children = counts.children;

  var rows = document.querySelectorAll('.product-row');
  for (var r=0; r<rows.length; r++){
    var row = rows[r];
    var isHalf = row.getAttribute('data-ischildhalf') === '1';

    var fullSel = row.querySelector('.qty-full');
    var halfSel = row.querySelector('.qty-half');

    // regras:
    // - is_child_half = true  -> inteira: 0..adultos, meia: 0..crianças
    // - is_child_half = false -> inteira: 0..(adultos+crianças), meia: 0
    var maxFull = isHalf ? adults : (adults + children);
    optionRange(fullSel, maxFull);

    if (halfSel){
      var maxHalf = isHalf ? children : 0;
      optionRange(halfSel, maxHalf);
    }
  }
}

function toggleSpouse() {
  var chk   = typeof getEl === 'function' ? getEl('has_spouse') : document.getElementById('has_spouse');
  var block = typeof getEl === 'function' ? getEl('spouse_block') : document.getElementById('spouse_block');
  if (!block) {
    updatePeopleBadge();
    syncProductLimits();
    return;
  }
  var show = !!(chk && chk.checked);
  // Mostrar/ocultar cobrindo os casos: inline style, hidden, d-none (Bootstrap)
  if (show) {
    block.style.removeProperty('display'); // volta ao display padrão do CSS
    block.hidden = false;
    block.classList.remove('d-none');
    // Focar após o repaint
    var input = (typeof getEl === 'function' ? getEl('spouse_name') : document.getElementById('spouse_name'))
             || document.querySelector('[name="spouse_name"]');

    if (input) {
      setTimeout(function () {
        input.focus();
        if (input.select && input.value) input.select(); // seleciona texto se já houver
        // input.scrollIntoView({block:'center', behavior:'smooth'}); // opcional
      }, 0);
    }
  } else {
    block.style.display = 'none';
    block.hidden = true;
    block.classList.add('d-none');
  }
  updatePeopleBadge();
  syncProductLimits();
}


function renderList(containerId, count, baseName, oldValues){
  if (!oldValues) oldValues = [];
  var c = getEl(containerId);
  if (!c) return;
  c.innerHTML = '';
  count = Math.min(parseInt(count || 0, 10) || 0, MAX_LIST_INPUTS);
  for (var i=0; i<count; i++){
    var div = document.createElement('div');
    div.className = 'mb-2';
    var val = (oldValues[i] || '').toString().replace(/"/g,'&quot;');
    div.innerHTML = ''
      + '<label class="form-label">'+ baseName +' #'+ (i+1) +'</label>'
      + '<input class="form-control" name="'+ containerId.replace('_names','') +'_names['+ i +']" value="'+ val +'">';
    c.appendChild(div);
  }
}

function syncDonation(){
  var roleSelect = getEl('trade_role');
  var donationDiv = getEl('donation-field');
  var isRevendedor = roleSelect && roleSelect.value === 'REVENDEDOR';
  if (donationDiv) donationDiv.style.display = isRevendedor ? 'block' : 'none';
}

function syncDay(){
  var ticketEl = getEl('ticket_type');
  var dayBlock = getEl('day-only');
  var daySel   = getEl('day_select');
  var isDay    = (ticketEl && ticketEl.value === 'DAY');
  if (dayBlock) dayBlock.style.display = isDay ? 'block' : 'none';
  if (daySel)   daySel.required = isDay;
}

document.addEventListener('DOMContentLoaded', function(){
  // spouse
  toggleSpouse();
  var spouseEl = getEl('has_spouse');
  if (spouseEl) spouseEl.addEventListener('change', toggleSpouse);

  // companions
  var companionsSelect = getEl('companions_count');
  var oldCompanions = readOld('companions'); // vem de data-companions
  renderList('companions_names', companionsSelect ? companionsSelect.value : 0, 'Acompanhante', oldCompanions);
  if (companionsSelect) companionsSelect.addEventListener('change', function(){
    renderList('companions_names', this.value, 'Acompanhante');
    updatePeopleBadge();
    syncProductLimits();
  });

  // children
  var childrenSelect = getEl('children_count');
  var oldChildren   = readOld('children');   // vem de data-children
  renderList('children_names', childrenSelect ? childrenSelect.value : 0, 'Criança', oldChildren);
  if (childrenSelect) childrenSelect.addEventListener('change', function(){
    renderList('children_names', this.value, 'Criança');
    updatePeopleBadge();
    syncProductLimits();
  });

  // DAY selector
  syncDay();
  var ticket = getEl('ticket_type');
  if (ticket) ticket.addEventListener('change', syncDay);

  // trade_role
  syncDonation();
  var trade = getEl('trade_role');
  if (trade) trade.addEventListener('change', syncDonation);

  // primeira sincronização
  updatePeopleBadge();
  syncProductLimits();
});
</script>
@endpush


