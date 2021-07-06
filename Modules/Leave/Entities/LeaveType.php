<?php

namespace Modules\Leave\Entities;

use Illuminate\Database\Eloquent\Model;
use Auth;

class LeaveType extends Model
{
    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
