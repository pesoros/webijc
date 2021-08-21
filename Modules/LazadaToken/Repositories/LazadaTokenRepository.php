<?php

namespace Modules\LazadaToken\Repositories;

use App\Repositories\UserRepository;
use App\Traits\ImageStore;
use Carbon\Carbon;
use App\Traits\Accounts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Account\Repositories\JournalRepository;
use App\User;
use Modules\LazadaToken\Entities\Lztoken;

class LazadaTokenRepository implements LazadaTokenRepositoryInterface
{
    public function all()
    {
        return Lztoken::all();
    }
}
