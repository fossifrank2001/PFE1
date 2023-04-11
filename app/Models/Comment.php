<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = ['content', 'commentable_type', 'commentable_id', 'user_id'];

    /**
     * Get the owning commentable model.
     */
    public function commentable(){
        return $this->morphTo();
    }
    /**
     * Get the user that authored the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
