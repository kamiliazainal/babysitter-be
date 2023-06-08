<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChildrenDetail extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'name', 'age', 'gender'];

    public function booking():BelongsTo
    {
        return $this->belongsTo(BookingDetail::class, 'id', 'booking_id');
    }
}
