<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    public $table = "images";
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hash', 'source', 'presentation_id'
    ];
    
    public function presentation()
    {
        $this->belongsTo('App\Presentation');
    }
}
