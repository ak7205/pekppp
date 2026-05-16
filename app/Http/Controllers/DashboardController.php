<?php

namespace App\Http\Controllers;

use App\Models\Aspect;
use App\Models\Period;
use App\Models\Submission;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $period = Period::where('is_active', true)->first();

        $totalIndicators = 0;
        $totalUploaded   = 0;
        $totalApproved   = 0;
        $totalRejected   = 0;
        $totalPending    = 0;
        $aspectData      = [];
        $indeksTotal     = 0;

        $aspects = Aspect::with([
            'indicators.submissions' => function ($query) use ($period) {
                if ($period) {
                    $query->where('period_id', $period->id)
                          ->with(['latestValidation', 'operator']);
                }
            }
        ])->orderBy('order')->get();

        // Progress keseluruhan per aspek
        $progressTable = [];

        foreach ($aspects as $aspect) {
            $indicatorCount  = count($aspect->indicators);
            $totalIndicators += $indicatorCount;

            $approvedCount = 0;
            $aspekScore    = 0;

            $aspekRows = [];

            foreach ($aspect->indicators as $indicator) {
                $submission = $indicator->submissions->first();

                $status       = 'kosong';
                $operatorName = null;
                $storageLink  = $indicator->storage_link;
                $validasiStatus = null;

                if ($submission) {
                    if ($submission->status === 'uploaded') {
                        $totalUploaded++;
                        $status = 'uploaded';
                    }

                    $validation = $submission->latestValidation;
                    if ($validation) {
                        $validasiStatus = $validation->status;
                        if ($validation->status === 'approved') {
                            $totalApproved++;
                            $approvedCount++;
                            if ($validation->score !== null) {
                                $aspekScore += ($validation->score / 5) * $indicator->weight * ($aspect->weight * 100);
                            }
                        } elseif ($validation->status === 'rejected') {
                            $totalRejected++;
                        }
                    } elseif ($submission->status === 'uploaded') {
                        $totalPending++;
                    }

                    $operatorName = $submission->operator?->name;
                }

                $aspekRows[] = [
                    'kode'         => $indicator->code,
                    'nama'         => $indicator->name,
                    'status'       => $status,
                    'validasi'     => $validasiStatus,
                    'score'        => $submission?->latestValidation?->score ?? null,
                    'operator'     => $operatorName,
                    'storage_link' => $storageLink,
                ];
            }

            $progres = $indicatorCount > 0
                ? round(($approvedCount / $indicatorCount) * 100, 1)
                : 0;

            $nilaiAspek = round($aspekScore, 2);
            $indeksTotal += $nilaiAspek;

            $aspectData[] = [
                'nama'    => $aspect->name,
                'bobot'   => $aspect->weight,
                'progres' => $progres,
                'nilai'   => $nilaiAspek,
            ];

            $progressTable[] = [
                'aspek'      => $aspect->name,
                'bobotAspek' => $aspect->weight,
                'nilaiAspek' => round($nilaiAspek, 2),
                'indicators' => $aspekRows,
            ];
        }

        // Kontribusi per user (operator/admin yang upload)
        $userContributions = Submission::with('operator')
            ->where('status', 'uploaded')
            ->when($period, fn($q) => $q->where('period_id', $period->id))
            ->get()
            ->groupBy('operator_id')
            ->map(fn($submissions) => [
                'name'  => $submissions->first()->operator?->name ?? 'Unknown',
                'total' => $submissions->count(),
            ])
            ->values();

        return Inertia::render('Dashboard', [
            'period'            => $period,
            'indeksTotal'       => round($indeksTotal / 100 * 5, 2),
            'totalIndicators'   => $totalIndicators,
            'totalUploaded'     => $totalUploaded,
            'totalApproved'     => $totalApproved,
            'totalRejected'     => $totalRejected,
            'totalPending'      => $totalPending,
            'aspectData'        => $aspectData,
            'progressTable'     => $progressTable,
            'userContributions' => $userContributions,
        ]);
    }
}