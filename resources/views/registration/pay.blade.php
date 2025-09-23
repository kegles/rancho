@extends('layouts.app')
@section('title', 'Pagamento — PIX')

@section('content')
  <div class="container py-4">
    <h1 class="h3 mb-3">Pagamento da inscrição</h1>

    @if (session('ok'))
      <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    <div class="row justify-content-center">
      <div class="col-md-6 text-center">

        {{-- QR Code --}}
        <img src="{{ $qrUrl }}" alt="QR Code PIX" class="img-fluid mb-3" style="max-width: 360px">

        {{-- Instruções --}}
        <p class="lead">Abra o aplicativo do seu banco, escolha <strong>PIX &rarr; Pagar com QR Code</strong> e aponte para o código acima.</p>

        {{-- Código PIX (copia e cola) --}}
        <div class="card mt-3 text-start">
          <div class="card-body">
            <label class="form-label fw-semibold">Código PIX (copia e cola)</label>
            <textarea id="pixCode" class="form-control" rows="4" readonly>{{ $pixCode }}</textarea>
            <div class="d-grid d-md-flex mt-2 gap-2">
              <button class="btn btn-primary" type="button" onclick="copyPix()">Copiar Código PIX</button>
            </div>
          </div>
        </div>

        {{-- Resumo --}}
        <div class="mt-4">
          <p class="mb-1"><strong>Nº inscrição:</strong> {{ $reg->reg_number }}</p>
          <p class="mb-1"><strong>Participante:</strong> {{ $reg->participant->name }}</p>
          <p class="mb-0"><strong>Total:</strong> R$ {{ number_format($reg->total_price / 100, 2, ',', '.') }}</p>
        </div>
      </div>
    </div>
  </div>

  <script>
    function copyPix() {
      const el = document.getElementById('pixCode');
      el.select();
      el.setSelectionRange(0, 99999);
      document.execCommand('copy');
      // feedback simples:
      const btns = document.getElementsByTagName('button');
      if (btns.length) {
        const b = btns[0];
        const old = b.innerText;
        b.innerText = 'Copiado!';
        setTimeout(()=> b.innerText = old, 1200);
      }
    }
  </script>
@endsection
