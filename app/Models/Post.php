<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','title','images'];

    protected $casts = [
        'images' => 'array'
    ];


    public function user() {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');

    }

    public function reactions()
    {
        return $this->morphMany(UserReaction::class, 'reactable');
    }
}
