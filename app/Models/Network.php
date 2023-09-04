<?php

namespace App\Models;

use App\Models\Scopes\AncientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'aff_sub',
        'payout',
        'is_unique_click',
        'is_unique_lead',
        'is_hidden',
        'is_daily_click_reset'
    ];
    protected static function booted(): void
    {
        static::addGlobalScope(new AncientScope);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class, 'network_id');
    }
}
