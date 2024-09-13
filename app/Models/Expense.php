<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Parables\Cuid\CuidAsPrimaryKey;

class Expense extends Model
{
    protected $keyType = 'string';
    use HasFactory, CuidAsPrimaryKey;

    public $incrementing = false;

    protected $fillable = ['amount','votehead_id','member_id','date','payment_method',
                           'payment_reference','reference','user_id','description'];

    protected $hidden = ['created_at', 'updated_at','is_deleted','user_id'];

    public function votehead():BelongsTo
    {
        return $this->belongsTo(VoteHead::class);
    }

    public function member():BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
