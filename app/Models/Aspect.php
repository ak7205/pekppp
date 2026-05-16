<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aspect extends Model
{
    protected $fillable = ['name', 'weight', 'order'];

    public function indicators()
    {
        return $this->hasMany(Indicator::class)->orderBy('order');
    }
}