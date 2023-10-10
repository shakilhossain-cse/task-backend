<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    public function reactable()
    {
        return $this->morphTo();
    }

    public function reaction()
    {
        return $this->belongsTo(Reaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
