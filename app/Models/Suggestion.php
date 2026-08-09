<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
    use HasFactory;
    public const TYPES = ['suggestion', 'complaint', 'question'];

    protected $fillable = ['user_id', 'type', 'content'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
