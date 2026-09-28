<?php

namespace App\Models\Traits;

trait CommonScopes{
    public function scopeOrder($query, $field, $sort='asc'){
        return $query->orderBy($field, $sort);
    }

    public static function scopeActive($query,$field)
    {
        return $query->where($field,'1');
    }

    public function scopeSearchLike($query, $field, $value){
        $query->where($field,'like','%'.$value.'%');
    }

    public function scopeWithEager($query, $eager){
        $query->with($eager);
    }

    public static function scopeApproved($query,$field)
    {
        return $query->where($field,'1');
    }
}