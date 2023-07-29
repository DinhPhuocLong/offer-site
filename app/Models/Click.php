<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Click extends Model
{
    use HasFactory;
    protected $fillable = [
        'offer_id',
        'country',
        'os',
        'browser',
        'user_id',
        'uuid',
        'ip',
        'user_agent',
        'is_click_lead',
        'is_converted',
        'offer_payout'
    ];
    public function scopeClickBelongTo(Builder $query): void
    {
        if (auth()->user()->role !== 0) {
            $query->where('user_id', auth()->user()->id);
        }
    }
}
