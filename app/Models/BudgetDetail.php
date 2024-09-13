<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetDetail extends Model
{
    use HasFactory;
    protected $fillable = ['header_id', 'votehead_id', 'amount', 'description'];

    public function header():BelongsTo
    {
        return $this->belongsTo(BudgetHeader::class,'id');
    }
}
