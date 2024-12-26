<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'receipt',
    ];

    protected $appends = ['file_url'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function getFileUrlAttribute()
    {
        return url('storage/receipts/' . $this->receipt);
    }
}
