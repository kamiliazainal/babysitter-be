<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_datetime', 'end_datetime',
        'parent_name', 'parent_phone', 'parent_email', 'parent_address',
        'spouse_name', 'spouse_phone'];

    public function children():HasMany
    {
        return $this->hasMany(ChildrenDetail::class, 'booking_id', 'id');
    }
}
