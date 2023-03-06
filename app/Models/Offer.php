<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'offer_link',
        'offer_payout',
        'country_allowed',
        'network_id',
        'is_hidden'
    ];
}
