@extends('layouts.admin')
@section('title','Editar produto')
@section('content')
<h1 class="mt-6 mb-6">Editar produto</h1>
<form method="POST" action="{{ route('admin.products.update',$product) }}" class="space-y-4">
@csrf @method('PUT')
@include('admin.products._form',['product'=>$product])
<div class="pt-4">
<button class="btn btn-primary">Salvar alterações</button>
<a href="{{ route('admin.products.index') }}" class="btn">Voltar</a>
</div>
</form>
@endsection
