@extends('layouts.admin')
@section('title','Novo produto')
@section('content')
<h1 class="mt-6 mb-6">Novo produto</h1>
<form method="POST" action="{{ route('admin.products.store') }}" class="space-y-4">
@csrf
@include('admin.products._form')
<div class="pt-4">
<button class="btn btn-primary">Salvar</button>
<a href="{{ route('admin.products.index') }}" class="btn">Cancelar</a>
</div>
</form>
@endsection
