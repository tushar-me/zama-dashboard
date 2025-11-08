<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SizeChart extends Model
{
    use HasUuids;

   protected $fillable = ['title', 'mockup_id', 'chart_data'];
    protected $casts = [
        'chart_data' => 'array',
    ];
    public function mockup()
    {
        return $this->belongsTo(Mockup::class);
    }
}
