@extends('layouts.app')
@section('title','Crachá - Rancho do Radioamador Gaucho')

@section('content')
  <div class="d-print-block">
    <div class="p-3 border rounded-3" style="width: 360px;">
      <div class="d-flex justify-content-between small text-muted">
        <span>Rancho do Radioamador Gaúcho</span>
        <span class="fs-1 fw-bold">{{ $reg->badge_letter }}</span>
      </div>
      <div class="fs-4 fw-bold">{{ $reg->participant->name }}</div>
      <div>{{ $reg->participant->callsign }}</div>
      <div class="mb-2">{{ $reg->participant->city }}</div>
      <hr>
      <div class="small text-muted">Reg: {{ $reg->reg_number }} • Dias: {{ $reg->days }}</div>
    </div>
    <div class="mt-3">
      <button class="btn btn-primary d-print-none" onclick="window.print()">Imprimir</button>
    </div>
  </div>
@endsection
