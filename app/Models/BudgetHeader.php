<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Parables\Cuid\CuidAsPrimaryKey;

class BudgetHeader extends Model
{
    protected $keyType = 'string';
    use HasFactory,CuidAsPrimaryKey;
    public $incrementing = false;

    protected $fillable = ['name','year_id'];
    protected $hidden = ['created_at','updated_at'];

    public function details():HasMany
    {
        return $this->hasMany(BudgetDetail::class,'header_id');
    }
}
