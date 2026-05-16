<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Validation extends Model
{
    protected $fillable = [
        'submission_id',
        'validator_id',
        'status',
        'note',
        'score',
        'validated_at',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
        'score'        => 'float',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validator_id');
    }
}