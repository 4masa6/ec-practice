<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function category()
    {
        return $this->belongsTo('App\Category'); // todo: Category : Product = 1 : 多
    }

    public function reviews()
    {
        return $this->hasMany('App\Review'); // todo: Product : Review = 1 : 多
    }
}
