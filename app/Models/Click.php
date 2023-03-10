<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'is_click_lead'
    ];
}
