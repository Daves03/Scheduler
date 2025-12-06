<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tutor extends Model
{
    use HasFactory, SoftDeletes;

    // ✅ These are the fields allowed to be saved
    protected $fillable = [
        'name', 
        'email', 
        'bio', 
        'subjects',     // Stores the array ["Math", "English"]
        'hourly_rate', 
        'phone',        // Optional (can be null)
        'avatar'        // Optional (can be null)
    ];

    protected $casts = [
        'subjects' => 'array',       // ✅ Critical: Auto-converts between Array <-> JSON
        'hourly_rate' => 'decimal:2' // Keeps money formatted correctly
    ];

    public function sessions()
    {
        return $this->hasMany(TutoringSession::class);
    }
}