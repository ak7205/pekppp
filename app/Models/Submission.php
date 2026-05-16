<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'indicator_id',
        'period_id',
        'operator_id',
        'status',
        'marked_at',
    ];

    protected $casts = [
        'marked_at' => 'datetime',
    ];

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function validations()
    {
        return $this->hasMany(Validation::class);
    }

    public function latestValidation()
    {
        return $this->hasOne(Validation::class)->latestOfMany();
    }
}