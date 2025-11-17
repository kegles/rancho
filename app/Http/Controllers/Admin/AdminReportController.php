<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Registration;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    /**
     * Relatório 1:
     * Fichas de Inscrição – uma "página" por inscrição, com todos os campos.
     */
    public function forms()
    {
        $regs = Registration::with(['participant', 'attendees', 'items.product'])
            ->orderBy('id')
            ->get();

        return view('admin.reports.forms', compact('regs'));
    }

    /**
     * Relatório 2:
     * Inscrições por produto – tela com seleção de produto + listagem.
     */
    public function byProduct(Request $request)
    {
        $products = Product::orderBy('name')->get();

        $product   = null;
        $regs      = collect();
        $productId = $request->get('product_id');

        if ($productId) {
            $product = Product::findOrFail($productId);

            // Todas as inscrições que têm esse produto
            $regs = Registration::with([
                    'participant',
                    'attendees',
                    'items' => function ($q) use ($productId) {
                        $q->where('product_id', $productId);
                    },
                ])
                ->whereHas('items', function ($q) use ($productId) {
                    $q->where('product_id', $productId);
                })
                ->orderBy('id')
                ->get();
        }

        return view('admin.reports.by_product', compact('products', 'product', 'regs'));
    }
}
