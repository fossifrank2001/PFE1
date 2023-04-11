<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\Like;
use App\Models\Menu;
use App\Models\Image;
use App\Models\Comment;
use App\Models\Appreciation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Meal extends Model 

{
    use HasFactory;
    protected $fillable = ['name', 'price', 'description', 'status', 'checkpub'];

    public function menus()
    {
        return $this->belongsToMany(Menu::class);
    }
    public function comments(){
        return $this->morphMany(Comment::class, 'commentable');
    }
    public function appreciates(){
        return $this->morphMany(Appreciation::class, 'appreciable');
    }
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    public function likes()
    {
        return $this->morphMany(Like::class, 'likable');
    }
}
