<?php

namespace Modules\LazadaToken\Http\Controllers;

use App\Traits\Notification;
use App\Traits\PdfGenerate;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Lazada_set;
use Session;
use PDF;
use Mail;
use Modules\LazadaToken\Entities\Lztoken;
use GuzzleHttp\Client;
use Lazada\LazopClient;
use Lazada\LazopRequest;
use Carbon\Carbon;

class LazadaTokenController extends Controller
{
    private $accessToken;
    private $apiGatewayGlobal;
    private $apiGateway;
    private $appKey;
    private $appSecret;

    protected $LazadaTokenRepository;

    public function __construct(
        Lazada_set $lazadaSet
    )
    {
        $this->middleware(['auth', 'verified']);
        $this->lazadaSet = $lazadaSet;
        $datatoken = [
            [
                "akun" => "juraganq89@gmail.com",
                "token" => "50000200d119prpwoUBgZfX1b34985bflvfvui8Ear3ksuFgiNGwjryGEvjxAO3r",
            ],
        ];
          
        $this->accessToken = $datatoken;
        $this->apiGatewayGlobal = env('LZ_API_GATEWAY_GLOBAL');
        $this->apiGateway = env('LZ_API_GATEWAY');
        $this->apiKey = env('LZ_API_KEY');
        $this->apiSecret = env('LZ_API_SECRET');
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        try {
            $tokendata = Lztoken::all();
            // return view('sale::sale.index', compact('sales'));
            return view('lazadatoken::lazadatoken.index', compact('tokendata'));
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('lazadatoken::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('lazadatoken::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('lazadatoken::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    public function generate_token(Request $request)
    {
        $querystring = $request->all();
        if (!isset($querystring['oauth'])) {
            return 'oauth is required';
        } else {
            $oauth = $querystring['oauth'];
        }

        $res = $this->__generate_token($oauth);

        if (isset($querystring['save'])) {
            if ($querystring['save'] == true && $res['code'] == '0') {
                $Lztoken = new Lztoken;
                $Lztoken->akun_name = $res['account'];
                $Lztoken->token = $res['access_token'];
                $Lztoken->refresh_token = $res['refresh_token'];
                $Lztoken->token_expires_in = $res['expires_in'];
                $Lztoken->refresh_token_expires_in = $res['refresh_expires_in'];
                $Lztoken->api_key = $this->apiKey;
                $Lztoken->api_secret = $this->apiSecret;
                $Lztoken->status = 1;
                $Lztoken->save();
                $res['save'] = true;
            }
        }
        
        return $res;
    }

    public function refresh_token_all()
    {
        $result = [];
        $tokendata = Lztoken::all();
        $res = $this->__generate_refresh_token($tokendata[0]['refresh_token']);
        return $res;
        foreach ($tokendata as $key => $value) {
            $res = $this->__generate_refresh_token($value['refresh_token']);
            $Lztoken = Lztoken::where('refresh_token', $value['refresh_token'])->first();
            $Lztoken->akun_name = $res['account'];
            $Lztoken->token = $res['access_token'];
            $Lztoken->refresh_token = $res['refresh_token'];
            $Lztoken->token_expires_in = $res['expires_in'];
            $Lztoken->refresh_token_expires_in = $res['refresh_expires_in'];
            $Lztoken->api_key = $this->apiKey;
            $Lztoken->api_secret = $this->apiSecret;
            $Lztoken->status = 1;
            $Lztoken->save();
            $result[$key] = $res;
            $result[$key]['save'] = true;
        }

        return $result;
    }

    public function refresh_token(Request $request)
    {
        $querystring = $request->all();
        if (!isset($querystring['refresh_token'])) {
            return 'refresh_token is required';
        } else {
            $refresh_token = $querystring['refresh_token'];
        }

        $res = $this->__generate_refresh_token($refresh_token);

        if (isset($querystring['save'])) {
            if ($querystring['save'] == true && $res['code'] == '0') {
                $Lztoken = Lztoken::where('refresh_token', $refresh_token)->first();
                $Lztoken->akun_name = $res['account'];
                $Lztoken->token = $res['access_token'];
                $Lztoken->refresh_token = $res['refresh_token'];
                $Lztoken->token_expires_in = $res['expires_in'];
                $Lztoken->refresh_token_expires_in = $res['refresh_expires_in'];
                $Lztoken->api_key = $this->apiKey;
                $Lztoken->api_secret = $this->apiSecret;
                $Lztoken->status = 1;
                $Lztoken->save();
                $res['save'] = true;
            }
        }
        
        return $res;
    }

    private function __generate_token($oauth)
    {
        $method = 'GET';
        $apiName = '/auth/token/create';

        $c = new LazopClient($this->apiGatewayGlobal, $this->apiKey, $this->apiSecret);
        $request = new LazopRequest($apiName,$method);
        $request->addApiParam('code',$oauth);
        // $request->addApiParam('uuid','38284839234');
        $executelazop = json_decode($c->execute($request), true);

        return $executelazop;
    }

    private function __generate_refresh_token($refresh_token)
    {
        $method = 'GET';
        $apiName = '/auth/token/refresh';

        $c = new LazopClient($this->apiGatewayGlobal, $this->apiKey, $this->apiSecret);
        $request = new LazopRequest($apiName,$method);
        $request->addApiParam('refresh_token',$refresh_token);
        $executelazop = json_decode($c->execute($request), true);

        return $executelazop;
    }
}
