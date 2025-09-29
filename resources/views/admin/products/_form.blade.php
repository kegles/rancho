{{-- resources/views/admin/products/_form.blade.php --}}
<div class="row g-3">
  {{-- SKU --}}
  <div class="col-md-3">
    <label for="sku" class="form-label fw-semibold">Código</label>
    <input
      id="sku"
      name="sku"
      type="text"
      class="form-control @error('sku') is-invalid @enderror"
      value="{{ old('sku', $product->sku ?? '') }}"
      required
      autofocus
    >
    @error('sku')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  {{-- Nome --}}
  <div class="col-md-9">
    <label for="name" class="form-label fw-semibold">Descrição</label>
    <input
      id="name"
      name="name"
      type="text"
      class="form-control @error('name') is-invalid @enderror"
      value="{{ old('name', $product->name ?? '') }}"
      required
    >
    @error('name')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  {{-- Preço (R$) --}}
  <div class="col-md-6">
    <label for="price_brl" class="form-label fw-semibold">Preço (R$)</label>
    <input
      id="price_brl"
      name="price_brl"
      type="text"
      inputmode="decimal"
      class="form-control @error('price_brl') is-invalid @enderror"
      value="{{ old('price_brl', $product->price_brl ?? '0,00') }}"
      required
      placeholder="0,00"
    >
    <div class="form-text">Informe em reais; será convertido para centavos.</div>
    @error('price_brl')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  {{-- Ordenação --}}
<div class="col-md-3">
  <label class="form-label">Ordem de exibição</label>
  @php
    $current = (int) old('sort_order', $product->sort_order ?? 1);
    $current = max(1, min(50, $current)); // garante 1..50
  @endphp
  <select name="sort_order" class="form-select">
    @for ($i = 1; $i <= 50; $i++)
      <option value="{{ $i }}" @selected($i === $current)>{{ $i }}º</option>
    @endfor
  </select>
</div>


  {{-- Flags --}}
  <div class="col-12 d-flex flex-wrap gap-4">
    {{-- Meia para crianças --}}
    <div class="form-check">
      {{-- hidden para enviar 0 quando desmarcado --}}
      <input type="hidden" name="is_child_half" value="0">
      <input
        class="form-check-input"
        type="checkbox"
        id="is_child_half"
        name="is_child_half"
        value="1"
        @checked(old('is_child_half', $product->is_child_half ?? false))
      >
      <label class="form-check-label" for="is_child_half">
        Aplicar meia (50%) para crianças
      </label>
    </div>

    {{-- Ativo --}}
    <div class="form-check">
      {{-- hidden para enviar 0 quando desmarcado --}}
      <input type="hidden" name="active" value="0">
      <input
        class="form-check-input"
        type="checkbox"
        id="active"
        name="active"
        value="1"
        @checked(old('active', $product->active ?? true))
      >
      <label class="form-check-label" for="active">
        Ativo
      </label>
    </div>
  </div>
</div>
