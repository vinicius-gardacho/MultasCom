<?php

namespace App\Http\Controllers;

use App\Models\OrgaoAplicador;
use Illuminate\View\View;

class OrgaoAplicadorController extends Controller
{
    public function index(): View
    {
        return view('orgaos.index', [
            'orgaos' => OrgaoAplicador::query()
                ->withCount('multas')
                ->withSum('multas', 'valor')
                ->orderBy('nome')
                ->get(),
        ]);
    }
}
