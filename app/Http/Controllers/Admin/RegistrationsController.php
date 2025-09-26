<?php

// app/Http/Controllers/Admin/RegistrationsController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegistrationsController extends Controller
{

    public function show($id)
    {
        $registration = Registration::with([
            'participant',
            'attendees',
            'items.product',
        ])->findOrFail($id);

        return view('admin.registrations.show', compact('registration'));
    }

    public function index(Request $req)
    {
        $q = Registration::with('participant')->orderByDesc('id');

        if ($req->filled('status')) $q->where('status', $req->string('status'));
        if ($req->filled('category')) $q->whereHas('participant', fn($qq)=>$qq->where('category_code',$req->string('category')));

        $regs = $q->paginate(20)->withQueryString();
        return view('admin.registrations.index', compact('regs'));
    }

    public function markPaid(int $id)
    {
        $r = Registration::findOrFail($id);
        $r->update(['status'=>'PAID']);
        return back()->with('ok','Inscrição marcada como paga.');
    }

    public function exportCsv(): StreamedResponse
    {
        $file = 'inscricoes-'.now()->format('Ymd-His').'.csv';
        $headers = ['Content-Type' => 'text/csv'];

        return response()->streamDownload(function(){
            $out = fopen('php://output','w');
            fputcsv($out, ['reg_number','status','name','callsign','city','category','ticket','days','base','total','eligible_draw']);
            Registration::with('participant')->chunk(500, function($chunk) use ($out){
                foreach($chunk as $r){
                    fputcsv($out, [
                        $r->reg_number, $r->status,
                        $r->participant->name, $r->participant->callsign, $r->participant->city,
                        $r->participant->category_code, $r->ticket_type, $r->days,
                        number_format($r->base_price/100, 2, ',', '.'),
                        number_format($r->total_price/100,2, ',', '.'),
                        $r->eligible_draw ? 'SIM' : 'NÃO',
                    ]);
                }
            });
            fclose($out);
        }, $file, $headers);
    }

    public function destroy(int $id)
    {
        $r = \App\Models\Registration::with('participant')->findOrFail($id);
        $r->delete(); // soft delete
        return back()->with('ok', 'Inscrição excluída (soft delete) com sucesso.');
    }
}
