<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parables\Cuid\CuidAsPrimaryKey;

class Income extends Model
{
    protected $keyType = 'string';
    use HasFactory, CuidAsPrimaryKey;

    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'member_id',
        'votehead_id',
        'amount',
        'date',
        'description',
        'user_id'
    ];

    protected $hidden = ['created_at','user_id'];
}
