<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'car_brand',
        'car_color',
        'car_plate_no',
        'no_of_seat',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
