<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    use HasFactory;

    protected $fillable = ['title'];


    public function userReactions()
    {
        return $this->hasMany(UserReaction::class);
    }
}
