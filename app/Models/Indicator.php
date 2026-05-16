<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indicator extends Model
{
    protected $fillable = [
        'aspect_id', 'code', 'name', 'description', 'weight',
        'score_type', 'storage_link',
        'template_text', 'template_file_url', 'template_pdf_url', 'order',
    ];

    public function aspect()
    {
        return $this->belongsTo(Aspect::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}