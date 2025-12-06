<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutoringSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutor_id', 'session_date', 'start_time', 'end_time', 'subject', 'status'
    ];

    protected $casts = [
        'session_date' => 'date',
    ];

    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }

    public function booking()
    {
        return $this->hasOne(Booking::class, 'session_id');
    }
}