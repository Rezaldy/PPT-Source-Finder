<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Presentation extends Model
{
    public function images()
    {
        return $this->hasMany('App\Image');
    }
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hash',
    ];
}
