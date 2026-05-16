<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValidationLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'submission_id',
        'actor_id',
        'action',
        'note',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}