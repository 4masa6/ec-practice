<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFavorite\Traits\Favoriteable;
use Kyslik\ColumnSortable\Sortable;

class Product extends Model
{
    use Favoriteable, Sortable;

    // todo: 14_ソートに使用するデータを指定
    public $sortable = [
        'price',
        'updated_at'
    ];

    public function category()
    {
        return $this->belongsTo('App\Category'); // todo: Category : Product = 1 : 多
    }

    public function reviews()
    {
        return $this->hasMany('App\Review'); // todo: Product : Review = 1 : 多
    }
}
