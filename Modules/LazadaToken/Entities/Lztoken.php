<?php

namespace Modules\LazadaToken\Entities;

use Illuminate\Database\Eloquent\Model;

class lztoken extends Model
{
    protected $fillable = ['akun_name','token','refresh_token','token_expires_in','refresh_token_expires_in','api_key','api_secret'];
}
