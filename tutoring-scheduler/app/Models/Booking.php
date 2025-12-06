<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id', 'student_name', 'student_email', 'student_phone', 'notes', 'status'
    ];

    public function session()
    {
        return $this->belongsTo(TutoringSession::class);
    }
}