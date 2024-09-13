<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parables\Cuid\CuidAsPrimaryKey;

class Year extends Model
{
    use HasFactory;
    use CuidAsPrimaryKey;


    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['name','start_date','end_date','is_closed'];

    protected $hidden = ['created_at','updated_at','created_by'];
}
