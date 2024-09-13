<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YealyContribution extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $fillable = [
        'amount',
        'year_id'
    ];
}
