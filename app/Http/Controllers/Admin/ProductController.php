<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string)$request->get('q',''));
        $products = Product::query()
        ->when($q, fn($qb) => $qb->where('name','like',"%{$q}%")->orWhere('sku','like',"%{$q}%"))
        ->orderBy('sort_order','asc')
        ->paginate(200)
        ->withQueryString();
        return view('admin.products.index', compact('products','q'));
    }


    public function create(): View
    {
        $product = new Product(['active'=>true,'is_child_half'=>false,'price'=>0]);
        return view('admin.products.create', compact('product'));
    }


    public function store(ProductStoreRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $product = new Product();
            $product->fill($request->only(['sku','name','is_child_half','sort_order','active','optional']));
            $product->price_brl = $request->input('price_brl'); // mutator converte p/ centavos
            $product->save();

            return redirect()
                ->route('admin.products.index')
                ->with('ok', 'Produto criado com sucesso.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.products.index')
                ->with('err', 'Falha ao criar produto: '.$e->getMessage());
        }
    }


    public function edit(Product $product): View
    {
        return view('admin.products.edit', compact('product'));
    }


    public function update(ProductUpdateRequest $request, Product $product): \Illuminate\Http\RedirectResponse
    {
        try {
            $product->fill($request->only(['sku','name','is_child_half','sort_order','active','optional']));
            $product->price_brl = $request->input('price_brl');
            $product->save();

            return redirect()
                ->route('admin.products.index')
                ->with('ok', 'Produto atualizado com sucesso.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.products.index')
                ->with('err', 'Falha ao atualizar produto: '.$e->getMessage());
        }
    }


    public function destroy(Product $product): \Illuminate\Http\RedirectResponse
    {
        try {
            $name = $product->name;
            $product->delete();

            return redirect()
                ->route('admin.products.index')
                ->with('ok', "Produto '{$name}' removido.");
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.products.index')
                ->with('err', 'Falha ao remover produto: '.$e->getMessage());
        }
    }

}
