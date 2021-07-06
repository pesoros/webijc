<?php

namespace Modules\Setup\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Setting\Model\GeneralSetting;

class SetupController extends Controller
{
    public function index()
    {
        return view('setup::index');
    }

    public function ltr_rtl(Request $request)
    {

        $general_settings = GeneralSetting::find(1);
        $general_settings->ttl_rtl = $request->ltr ?? 1;
        $general_settings->save();

        return true;
    }
}
