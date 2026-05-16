<?php

namespace App\Http\Controllers;

use App\Models\Aspect;
use App\Models\Period;
use App\Models\Submission;
use App\Models\Validation;
use App\Models\ValidationLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ValidasiController extends Controller
{
    public function index()
    {
        $period = Period::where('is_active', true)->first();

        $aspects = Aspect::with([
            'indicators.submissions' => function ($query) use ($period) {
                if ($period) {
                    $query->where('period_id', $period->id)
                          ->with(['latestValidation', 'operator']);
                }
            }
        ])->orderBy('order')->get();

        return Inertia::render('Validasi/Index', [
            'period'  => $period,
            'aspects' => $aspects,
        ]);
    }

    public function review(Request $request, Submission $submission)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'note'   => 'nullable|string',
            'score'  => 'nullable|numeric|min:0|max:5',
        ]);

        // Hapus validasi sebelumnya jika ada
        $submission->validations()->delete();

        // Buat validasi baru
        Validation::create([
            'submission_id' => $submission->id,
            'validator_id'  => auth()->id(),
            'status'        => $request->status,
            'note'          => $request->note,
            'score'         => $request->status === 'approved' ? $request->score : null,
            'validated_at'  => now(),
        ]);

        // Catat di log
        ValidationLog::create([
            'submission_id' => $submission->id,
            'actor_id'      => auth()->id(),
            'action'        => $request->status,
            'note'          => $request->note,
            'created_at'    => now(),
        ]);

        return back()->with('success', 'Validasi berhasil disimpan.');
    }
}