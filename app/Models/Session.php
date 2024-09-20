<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parables\Cuid\CuidAsPrimaryKey;

class Session extends Model
{
    use HasFactory,CuidAsPrimaryKey;

    protected $keyType = 'string';

    protected $fillable = ['id','user_id', 'payload','last_activity'];
    protected $hidden = ['last_activity','user_id','id'];
    public $timestamps = false;
}
