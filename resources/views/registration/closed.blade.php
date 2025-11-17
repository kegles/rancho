@extends('layouts.app')

@section('title','Inscrição — Rancho do Radioamador Gaúcho')

@section('content')
  <div class="row">
    <div class="col-lg-10 col-xl-8">
      <h1 class="h3 mb-3">Inscrições encerradas</h1>
        <div class="alert alert-danger">{{ session('error') }}</div>
    </div>
  </div>
@endsection




