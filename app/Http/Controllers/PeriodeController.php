<?php

namespace App\Http\Controllers;

use App\Models\Period;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PeriodeController extends Controller
{
    public function index()
    {
        $periods = Period::orderBy('created_at', 'desc')->get();

        return Inertia::render('Periode/Index', [
            'periods' => $periods,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        Period::create([
            'name'       => $request->name,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'is_active'  => false,
        ]);

        return back()->with('success', 'Periode berhasil ditambahkan.');
    }

    public function activate(Period $period)
    {
        // Nonaktifkan semua periode dulu
        Period::query()->update(['is_active' => false]);

        // Aktifkan periode yang dipilih
        $period->update(['is_active' => true]);

        return back()->with('success', 'Periode berhasil diaktifkan.');
    }

    public function destroy(Period $period)
    {
        if ($period->is_active) {
            return back()->with('error', 'Periode aktif tidak bisa dihapus.');
        }

        $period->delete();

        return back()->with('success', 'Periode berhasil dihapus.');
    }
}