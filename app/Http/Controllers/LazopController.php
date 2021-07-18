<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Lazada\LazopClient;
use Lazada\LazopRequest;
use Carbon\Carbon;

class LazopController extends Controller
{
    private $accessToken;
    private $apiGateway;
    private $appKey;
    private $appSecret;

    public function __construct()
    {
        $datatoken = [
            [
                "akun" => "indodjainem.group@gmail.com",
                "token" => "50000900532VMEcqMhzBqGsavqZCdhydlUhsOeBDjtfN1c095e14zxGmlE6KxZYC",
            ],[
                "akun" => "uptodate5758@gmail.com",
                "token" => "50000801725iSHjqbTqHWGiQdrzxdnFuLVERs1a037177Hh2BpPhxvVsPKy09N4",
            ],[
                "akun" => "termurahabis@gmail.com",
                "token" => "50000700a12NCV8iBozuqC9c158bdb0ffUlqwiyPcAFcu6lvVckhJIwDQbj4pbWr",
            ],[
                "akun" => "onegood08@gmail.com",
                "token" => "50000701217pOEazhudlQe0ol7dCt190852ee6OSwiGHRG1EwTjFXlptdeYPA6F",
            ],[
                "akun" => "nomoruno1@gmail.com",
                "token" => "50000800f21CMaoqbCYnUlpsg0RkBbFwh140160aepVygmmOLvHTUBibCFPcyXEs",
            ],[
                "akun" => "nobuanugrahmakmur@gmail.com",
                "token" => "50000200a06NCV8iBN11ca934e3utedzmxEMpgROGecfp6OyVjJFQk2mvcjgFtK",
            ],[
                "akun" => "newuptodate01@gmail.com",
                "token" => "50000001718iSHjqbTxG8GCv6lTTFm17681055mwjvFrulE19wPEQuxytpsvDiJJ",
            ],[
                "akun" => "mahadana8899@gmail.com",
                "token" => "50000301335s8D0gvhhrCVsleFEydqr0GfiwlVlzujexixO1ef59202dvkd7vgdy",
            ],[
                "akun" => "lapakberkah999@gmail.com",
                "token" => "50000100e12kiSxnrdY6I1DI18e176f9qEzsGdfeSCpWvCJdLn4gztBjyysylDWy",
            ],[
                "akun" => "jegeteofficial@gmail.com",
                "token" => "50000800520VMEcqMHT9qDOxowd82h2c107d3c37KsZswh9CFv4sxVkjeuzwpkaC",
            ],[
                "akun" => "jayaningratmobile@gmail.com",
                "token" => "50000000d069Gopwoy1fcc80c5BYdnWgotduocBchRBsUwHIDQK1DrvIGenOzU1r",
            ],[
                "akun" => "jayaningrat5758@gmail.com",
                "token" => "50000500326bU7pacqPmykJFQzpubBdg0gIwgR15aff584TjYdAvBORwCR5BspLt",
            ],[
                "akun" => "indonesiasezt@gmail.com",
                "token" => "50000301414c7dhueHqbRrlWDf1a65cfc7yeOwTemHNjalvWBhx8qvAtPydUzpd7",
            ],[
                "akun" => "gudanginternet7@gmail.com",
                "token" => "5000090003428aawUqbhxKDxCMHpwmwiA2iXdlti0tc8ch14283b1dyCMQwDtX1j",
            ],[
                "akun" => "giladiskon@gmail.com",
                "token" => "50000200e40kiSxnrAD3k1fJPEsoHaDcR9LxuHlEtpYiuzkEYrYp14b3e4b2pDWy",
            ],[
                "akun" => "ellasartika1@gmail.com",
                "token" => "50000601835wBMratTebcdu8PzTgfgMj3gTujg0kQQDLvxy13034106MmReN4Vyd",
            ],[
                "akun" => "devisales118@gmail.com",
                "token" => "50000601505bXKvdj1f93e62bRgUpcWcju6r00fkFwJYHuX9F2GrSdPQvVmxCpmr",
            ],[
                "akun" => "afnstore99@gmail.com",
                "token" => "50000800606q8FrLfQ1552c042AqFPaRUifcoYcKUbRrd6hitCOT2iFGLA8f8mrf",
            ],[
                "akun" => "Jegeteindonesia@gmail.com",
                "token" => "50000600217y7aUobatSlrlnBt4sQ1875a58dcA5k2cJviUthbEEw9Mst5EWbadg",
            ],[
                "akun" => "indovapor99@gmail.com",
                "token" => "50000701240pOEazhTdnUEWtcXiBThKsxlggyI3Gv1BHZkRRgJp71a1d9f38CfUy",
            ],
        ];
          
        $this->accessToken = $datatoken;
        $this->apiGateway = env('LZ_API_GATEWAY');
        $this->apiKey = env('LZ_API_KEY');
        $this->apiSecret = env('LZ_API_SECRET');
    }

    public function get_seller()
    {
        $method = 'GET';
        $apiName = '/seller/get';

        $c = new LazopClient($this->apiGateway, $this->apiKey, $this->apiSecret);
        $request = new LazopRequest($apiName,$method);
        $executelazop = json_decode($c->execute($request, $this->accessToken[0]['token']), true);
        
        return $executelazop;
    }

    public function get_product($setOffset = '50')
    {
        $tokenwehave = $this->accessToken;
        $arr = [];
        $method = 'GET';
        $apiName = '/products/get';

        foreach ($tokenwehave as $key => $value) {
            $c = new LazopClient($this->apiGateway, $this->apiKey, $this->apiSecret);
            $request = new LazopRequest($apiName,$method);
            $request->addApiParam('filter','live');
            // $request->addApiParam('update_before','2018-01-01T00:00:00+0800');
            // $request->addApiParam('create_before','2018-01-01T00:00:00+0800');
            $request->addApiParam('offset',$setOffset);
            $request->addApiParam('create_after','2010-01-01T00:00:00+0800');
            // $request->addApiParam('update_after','2010-01-01T00:00:00+0800');
            $request->addApiParam('limit','50');
            $request->addApiParam('options','1');
            // $request->addApiParam('sku_seller_list',' [\"39817:01:01\", \"Apple 6S Black\"]');
            $executelazop = json_decode($c->execute($request, $value['token']), true);

            if (isset($executelazop['data']['products'])) {

                $data = $executelazop['data']['products'];
            
                for ($i=0; $i < count($data); $i++) { 
                    // if ($data[$i]['fee_name'] != "Payment Fee") {
                        $setdata[$i]['akun'] = $value['akun'];
                        $setdata[$i]['nama'] = $data[$i]['attributes']['name'];
                        $setdata[$i]['sku_lazada'] = $data[$i]['skus'][0]['ShopSku'];
                        array_push($arr,$setdata[$i]);
                    // }
                }
            }
            $res['data'] = $arr;

        }
        
        return $res;
    }

    public function get_transaction(Request $request)
    {
        $querystring = $request->all();
        if (isset($querystring['date'])) {
            $date = $querystring['date'];
        } else {
            $date = "2021-01-01";
        }

        $tokenwehave = $this->accessToken;
        $arr = [];
        $method = 'GET';
        $apiName = '/finance/transaction/detail/get';

        foreach ($tokenwehave as $key => $value) {
            $c = new LazopClient($this->apiGateway, $this->apiKey, $this->apiSecret);
            $request = new LazopRequest($apiName,$method);
            $request->addApiParam('trans_type','-1');
            $request->addApiParam('start_time',$date);
            $request->addApiParam('end_time',$date);
            $request->addApiParam('limit','500');
            $request->addApiParam('offset','0');
            $executelazop = json_decode($c->execute($request, $value['token']), true);

            if (isset($executelazop['data'])) {

                $data = $executelazop['data'];
            
                for ($i=0; $i < count($data); $i++) { 
                    // if ($data[$i]['fee_name'] != "Payment Fee") {
                        $data[$i]['nama_akun'] = $value['akun'];
                        array_push($arr,$data[$i]);
                    // }
                }
    
                $res['jumlah_data'] = count($arr);
                $res['data'] = $arr;
            } else {
                $res = $executelazop;
            }
        }
        
        return $res;
    }
    
    public function get_orders(Request $request)
    {
        $querystring = $request->all();
        if (isset($querystring['date'])) {
            $datestart = Carbon::createFromFormat('Y-m-d', $querystring['date']);
            $daysToAdd = 1;
            $dateend = $datestart->addDays($daysToAdd)->format('Y-m-d').'T01:00:00+08:00';
            $datestart = $querystring['date'].'T00:00:00+08:00';
        } else {
            $datestart = "2021-01-01T01:00:00+08:00";
            $dateend = "2021-01-02T00:00:00+08:00";
        }
        
        $tokenwehave = $this->accessToken;
        $arr = [];
        $method = 'GET';
        $apiName = '/orders/get';
        
        foreach ($tokenwehave as $key => $value) {
            $c = new LazopClient($this->apiGateway, $this->apiKey, $this->apiSecret);
            $request = new LazopRequest($apiName,$method);
            // $request->addApiParam('update_before','2018-02-10T16:00:00+08:00');
            $request->addApiParam('sort_direction','DESC');
            $request->addApiParam('offset','0');
            $request->addApiParam('limit','10');
            // $request->addApiParam('update_after','2017-02-10T09:00:00+08:00');
            $request->addApiParam('sort_by','created_at');
            $request->addApiParam('created_before', $dateend);
            $request->addApiParam('created_after', $datestart);
            // $request->addApiParam('status','shipped');
            $executelazop = json_decode($c->execute($request, $value['token']), true);

            if (isset($executelazop['data']['orders'])) {

                $data = $executelazop['data']['orders'];
            
                for ($i=0; $i < count($data); $i++) { 
                    $data[$i]['nama_akun'] = $value['akun'];
                    $data[$i]['token'] = $value['token'];
                    array_push($arr,$data[$i]);
                }
    
                $res['date_start'] = $datestart;
                $res['date_end'] = $dateend;
                $res['jumlah_data'] = count($arr);
                $res['data'] = $arr;
            } else {
                $res = $executelazop;
                $res['akun'] = $value['akun'];
            }
        }

        return json_encode($res);
    }

    public function importProducts($setOffset = '50')
    {
        $data = $this->get_product($setOffset);
        $data = $data['data'];
        // for ($i=0; $i < count($data); $i++) { 
            Lazada::insert($data);
        // }
        return $data;
    }

    public function get_product_locally()
    {
        $products = IjcProducts::all();
        $res['data'] = $products;
        return $res;
    }

    public function crawl_orders(Request $request)
    {
        $querystring = $request->all();
        if (isset($querystring['date'])) {
            $datestart = Carbon::createFromFormat('Y-m-d', $querystring['date']);
            $daysToAdd = 1;
            $dateend = $datestart->addDays($daysToAdd)->format('Y-m-d').'T01:00:00+08:00';
            $datestart = $querystring['date'].'T00:00:00+08:00';
        } else {
            $datestart = "2021-01-01T01:00:00+08:00";
            $dateend = "2021-01-02T00:00:00+08:00";
        }
        
        $tokenwehave = $this->accessToken;
        $arr = [];
        $method = 'GET';
        $apiName = '/orders/get';
        
        foreach ($tokenwehave as $key => $value) {
            $c = new LazopClient($this->apiGateway, $this->apiKey, $this->apiSecret);
            $request = new LazopRequest($apiName,$method);
            // $request->addApiParam('update_before','2018-02-10T16:00:00+08:00');
            $request->addApiParam('sort_direction','DESC');
            $request->addApiParam('offset','0');
            $request->addApiParam('limit','10');
            // $request->addApiParam('update_after','2017-02-10T09:00:00+08:00');
            $request->addApiParam('sort_by','created_at');
            $request->addApiParam('created_before', $dateend);
            $request->addApiParam('created_after', $datestart);
            // $request->addApiParam('status','shipped');
            $executelazop = json_decode($c->execute($request, $value['token']), true);

            if (isset($executelazop['data']['orders'])) {

                $data = $executelazop['data']['orders'];
            
                for ($i=0; $i < count($data); $i++) { 
                    $data[$i]['nama_akun'] = $value['akun'];
                    $data[$i]['token'] = $value['token'];
                    if ($data[$i]['statuses'][0] == 'delivered') {
                        $orders = lazadaorders::where('order_number', $data[$i]['order_number'])->first();
                        if ($orders == null) {
                            $ordersitem = $this->get_orderItem($data[$i]['order_number'],$value['token']);
                            foreach ($ordersitem as $key2 => $value2) {
                                $saveit = false;
                                $data[$i]['skulist'][$key2] = $value2['shop_sku'];
                                $Lazada = Lazada::where('sku_lazada', $value2['shop_sku'])->first();
                                if ($Lazada != null) {
                                    $LazadaDetail = LazadaDetail::where('id_lazada', $Lazada['id'])->get();
                                    $data[$i]['LazadaDetail'][$key2] = $LazadaDetail;

                                    foreach ($LazadaDetail as $key3 => $value3) {
                                        $product = Product::where('id', $value3['id_product'] )->first();
                                        $product->qty -= 1;
                                        $product->update();
                                        $saveit = true;
                                    }
                                }
                                if ($saveit == true) {
                                    $input['order_number'] = $data[$i]['order_number'];
                                    $input['status'] = '-';
                                    lazadaorders::create($input);
                                }
                            }
                        }
                    }
                    $data[$i]['items'] = $this->get_orderItem($data[$i]['order_number'],$value['token']);
                    array_push($arr,$data[$i]);
                }
    
                $res['date_start'] = $datestart;
                $res['date_end'] = $dateend;
                $res['jumlah_data'] = count($arr);
                $res['data'] = $arr;
            } else {
                $res = $executelazop;
                $res['akun'] = $value['akun'];
            }
        }

        return $res;
    }

    public function get_orderItem($ordernumber, $token, $url=false)
    {
        $arr = [];
        $method = 'GET';
        $apiName = '/order/items/get';

        $c = new LazopClient($this->apiGateway, $this->apiKey, $this->apiSecret);
        $request = new LazopRequest($apiName,$method);
        $request->addApiParam("order_id", $ordernumber);
        $executelazop = json_decode($c->execute($request, $token), true);

        if ($url == false) {
            return $executelazop['data'];
        } else {
            return $executelazop;
        }
    }
}
