<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFavorite\Traits\Favoriteable;

class Product extends Model
{
    use Favoriteable;  // todo: laravel-favoriteを使用するための記述

    public function category()
    {
        return $this->belongsTo('App\Category'); // todo: Category : Product = 1 : 多
    }

    public function reviews()
    {
        return $this->hasMany('App\Review'); // todo: Product : Review = 1 : 多
    }
}
