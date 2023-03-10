<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostbackError extends Model
{
    use HasFactory;
    protected $fillable = [
        'cid',
        'sender_ip',
        'payout',
    ];
}
