<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    public function product()
    {
        return $this->belongsTo('App\Product'); // todo: Product : Review = 1 : 多
    }

    public function user()
    {
        return $this->belongsTo('App\User'); // todo: User : Review = 1 : 多
    }
}
