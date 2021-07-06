<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Brand extends Model
{
    protected $table = "brands";
    protected $fillable = ["name", "description", "status", "created_by", "updated_by"];

    public function products()
    {
        return $this->hasMany(Product::class)->latest();
    }

    public static function boot()
    {
        parent::boot();
        static::saving(function ($brand) {
            $brand->created_by = Auth::user()->id ?? null;
        });

        static::updating(function ($brand) {
            $brand->updated_by = Auth::user()->id ?? null;
        });
    }
}
