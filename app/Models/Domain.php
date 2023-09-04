<?php

namespace App\Models;

use App\Models\Scopes\AncientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;
    protected $fillable = [
        'website_name',
        'domain_url',
        'logo',
        'is_hidden'
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new AncientScope);
    }
    public function offers() {
        return $this->hasMany(Offer::class, 'offer_domain');
    }
}
