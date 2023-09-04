<?php

namespace App\Models;

use App\Models\Scopes\AncientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;
    protected $fillable = [
        'offer_name',
        'offer_link',
        'offer_payout',
        'country_allowed',
        'network_id',
        'offer_domain',
        'is_hidden',
        'offer_type'
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new AncientScope);
    }


    public function network()
    {
        return $this->belongsTo(Network::class, 'network_id');
    }

    public function domain() {
        return $this->belongsTo(Domain::class, 'offer_domain');
    }
}
