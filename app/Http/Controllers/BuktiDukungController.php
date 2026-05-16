<?php

namespace App\Http\Controllers;

use App\Models\Aspect;
use App\Models\Period;
use App\Models\Submission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BuktiDukungController extends Controller
{
    public function index()
    {
        $period = Period::where('is_active', true)->first();

        $aspects = Aspect::with([
            'indicators.submissions' => function ($query) use ($period) {
                if ($period) {
                    $query->where('period_id', $period->id)
                          ->with('latestValidation');
                }
            }
        ])->orderBy('order')->get();

        return Inertia::render('BuktiDukung/Index', [
            'period'  => $period,
            'aspects' => $aspects,
        ]);
    }

    public function toggle(Request $request, $indicatorId)
    {
        $period = Period::where('is_active', true)->firstOrFail();

        $submission = Submission::firstOrCreate(
            [
                'indicator_id' => $indicatorId,
                'period_id'    => $period->id,
            ],
            [
                'operator_id' => auth()->id(),
                'status'      => 'empty',
            ]
        );

        // Cek apakah sudah approved — tidak bisa diubah
        if ($submission->latestValidation?->status === 'approved') {
            return back()->with('error', 'Bukti dukung yang sudah diapprove tidak dapat diubah.');
        }

        if ($submission->status === 'empty') {
            $submission->update([
                'status'      => 'uploaded',
                'operator_id' => auth()->id(),
                'marked_at'   => now(),
            ]);
        } else {
            $submission->update([
                'status'    => 'empty',
                'marked_at' => null,
            ]);
        }

        return back()->with('success', 'Status bukti dukung diperbarui.');
    }
}