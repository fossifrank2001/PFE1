<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appreciation extends Model
{
    use HasFactory;
    /**
     * Get the owning appreciable model.
     */
    public function appreciable(){
        return $this->morphTo();
    }
}
