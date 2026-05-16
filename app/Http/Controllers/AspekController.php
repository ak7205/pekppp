<?php

namespace App\Http\Controllers;

use App\Models\Aspect;
use App\Models\Indicator;
use Inertia\Inertia;
use App\Imports\AspekIndikatorImport;
use App\Exports\AspekIndikatorExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class AspekController extends Controller
{
    public function index()
    {
        $aspects = Aspect::with('indicators')->orderBy('order')->get();

        return Inertia::render('Aspek/Index', [
            'aspects' => $aspects,
        ]);
    }

    public function storeAspek(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'weight' => 'required|numeric|min:0|max:1',
            'order'  => 'required|integer',
        ]);

        Aspect::create($request->only('name', 'weight', 'order'));

        return back()->with('success', 'Aspek berhasil ditambahkan.');
    }

    public function storeIndikator(Request $request, Aspect $aspect)
    {
        $request->validate([
            'code'       => 'required|string|max:50',
            'name'       => 'required|string',
            'weight'     => 'required|numeric|min:0|max:1',
            'score_type' => 'required|in:likert,status',
            'storage_link'       => 'nullable|url',
            'template_text'      => 'nullable|string',
            'template_file_url'  => 'nullable|url',
            'template_pdf_url'   => 'nullable|url',
            'order'      => 'required|integer',
        ]);

        $aspect->indicators()->create($request->all());

        return back()->with('success', 'Indikator berhasil ditambahkan.');
    }

    public function destroyAspek(Aspect $aspect)
    {
        $aspect->delete();
        return back()->with('success', 'Aspek berhasil dihapus.');
    }

    public function destroyIndikator(Indicator $indicator)
    {
        $indicator->delete();
        return back()->with('success', 'Indikator berhasil dihapus.');
    }

    public function updateIndikator(Request $request, Indicator $indicator)
    {
        $request->validate([
            'code'              => 'required|string|max:50',
            'name'              => 'required|string',
            'weight'            => 'required|numeric|min:0|max:1',
            'description'       => 'nullable|string',
            'score_type'        => 'required|in:likert,status',
            'storage_link'      => 'nullable|url',
            'template_text'     => 'nullable|string',
            'template_file_url' => 'nullable|url',
            'template_pdf_url'  => 'nullable|url',
            'order'             => 'required|integer',
        ]);

        $indicator->update($request->all());

        return back()->with('success', 'Indikator berhasil diperbarui.');
    }

    public function updateAspek(Request $request, Aspect $aspect)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'weight' => 'required|numeric|min:0|max:1',
            'order'  => 'required|integer',
        ]);

        $aspect->update($request->only('name', 'weight', 'order'));

        return back()->with('success', 'Aspek berhasil diperbarui.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        Excel::import(new AspekIndikatorImport, $request->file('file'));

        return back()->with('success', 'Data berhasil diimport.');
    }

    public function export()
    {
        return Excel::download(new AspekIndikatorExport, 'aspek-indikator.xlsx');
    }

}