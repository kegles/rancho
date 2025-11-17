<!doctype html>
<html lang="pt-br" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','Rancho do Radioamador Gaúcho')</title>

  {{-- Bootstrap 5.3 via CDN (CSS) --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* ajustes sutis opcionais */
    .brand { letter-spacing:.2px }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
  <div class="container">
    <a class="navbar-brand fw-semibold brand" href="{{ url('/') }}">Rancho do Radioamador Gaúcho</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="nav" class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item me-4">
                <a href="{{ route('admin.reg.index') }}"
                @class(['nav-link', 'active fw-bold' => request()->routeIs('admin.reg.*')])
                @if (request()->routeIs('admin.reg.*')) aria-current="page" @endif>
                Inscrições
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.products.index') }}"
                @class(['nav-link', 'active fw-bold' => request()->routeIs('admin.products.*')])
                @if (request()->routeIs('admin.products.*')) aria-current="page" @endif>
                Produtos
                </a>
            </li>
            {{-- Novo menu Relatórios --}}
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle @if(request()->routeIs('admin.reports.*')) active fw-bold @endif"
                href="#"
                role="button"
                data-bs-toggle="dropdown"
                aria-expanded="false">
                    Relatórios
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.reports.forms') }}">
                            Fichas de Inscrição
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.reports.by_product') }}">
                            Inscrições por produto
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
  </div>
</nav>

<main class="container my-4">
  @yield('content')
</main>

<footer class="border-top py-3 mt-5">
  <div class="container small text-muted d-flex justify-content-between">
    <span>Copyleft {{ date('Y') }} - <a href="{{ route('registration.form') }}">Voltar ao registro</a></span>
    <span><a href="https://www.kegles.com.br/contrate" target="_blank">PY3NT</a></span>
  </div>
</footer>

{{-- Bootstrap 5.3 via CDN (JS bundle + Popper) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
