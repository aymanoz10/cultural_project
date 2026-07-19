<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Volunteering extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'whatsapp_number',
        'birthday_date',
        'address',
        'education_level',
        'has_volunteered_before',
        'previous_experiences',
        'why_volunteer',
        'volunteering_interest',
        'tools',
        'center',
        'available_times',
        'notes',
        'status',
    ];

    protected $casts = [
        'has_volunteered_before' => 'boolean',
    ];
}
