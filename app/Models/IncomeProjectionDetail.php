<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeProjectionDetail extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'header_id',
        'votehead_id',
        'amount',
    ];
}
