<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parables\Cuid\CuidAsPrimaryKey;

class IncomeProjectionHeader extends Model
{
    use HasFactory,CuidAsPrimaryKey;

    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id','year_id'];
}
