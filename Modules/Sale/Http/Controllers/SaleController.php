<?php

namespace Modules\Sale\Http\Controllers;

use App\Traits\Notification;
use App\Traits\PdfGenerate;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Contact\Repositories\ContactRepositoriesInterface;
use Modules\Inventory\Entities\ShowRoom;
use Modules\Inventory\Repositories\ShowRoomRepositoryInterface;
use Modules\Inventory\Repositories\WareHouseRepositoryInterface;
use Modules\Packing\Entities\PackingItemDetail;
use Modules\Product\Repositories\ProductRepositoryInterface;
use Modules\Sale\Http\Requests\SaleQuotationRequest;
use Modules\Sale\Http\Requests\SaleRequest;
use Modules\Sale\Repositories\SaleRepositoryInterface;
use Modules\Setting\Repositories\GeneralSettingRepositoryInterface;
use Modules\Setup\Repositories\IntroPrefixRepositoryInterface;
use Modules\Setup\Repositories\TaxRepositoryInterface;
use Modules\Product\Repositories\VariantRepositoryInterface;
use Modules\Setting\Model\EmailTemplate;
use Modules\LazadaToken\Entities\Lztoken;
use App\Lazada_set;
use Session;
use PDF;
use Mail;
use App\Traits\SaleProductSelect;
use GuzzleHttp\Client;
use Lazada\LazopClient;
use Lazada\LazopRequest;
use Carbon\Carbon;

class SaleController extends Controller
{
    use PdfGenerate, Notification, SaleProductSelect;

    private $accessToken;
    private $apiGatewayGlobal;
    private $apiGateway;
    private $appKey;
    private $appSecret;

    protected $saleRepository, $productRepository, $contactRepositories, $wareHouseRepository, $settingRepository, $showRoomRepository, $taxRepository, $introPrefixRepository, $variationRepository, $lazadaSet;

    public function __construct(
        SaleRepositoryInterface $saleRepository,
        ContactRepositoriesInterface $contactRepositories,
        WareHouseRepositoryInterface $wareHouseRepository,
        ProductRepositoryInterface $productRepository,
        GeneralSettingRepositoryInterface $settingRepository,
        ShowRoomRepositoryInterface $showRoomRepository,
        TaxRepositoryInterface $taxRepository,
        IntroPrefixRepositoryInterface $introPrefixRepository,
        VariantRepositoryInterface $variationRepository,
        Lazada_set $lazadaSet
    )
    {
        $this->middleware(['auth', 'verified']);
        $this->saleRepository = $saleRepository;
        $this->productRepository = $productRepository;
        $this->wareHouseRepository = $wareHouseRepository;
        $this->contactRepositories = $contactRepositories;
        $this->settingRepository = $settingRepository;
        $this->showRoomRepository = $showRoomRepository;
        $this->taxRepository = $taxRepository;
        $this->introPrefixRepository = $introPrefixRepository;
        $this->variationRepository = $variationRepository;
        $this->lazadaSet = $lazadaSet;
        $this->accessToken = Lztoken::get();
        $this->apiGatewayGlobal = env('LZ_API_GATEWAY_GLOBAL');
        $this->apiGateway = env('LZ_API_GATEWAY');
        $this->apiKey = env('LZ_API_KEY');
        $this->apiSecret = env('LZ_API_SECRET');
    }

    public function index()
    {
        $showroom_id = session()->get('showroom_id');
        $isLazada = $this->lazadaSet->where('branch_id', $showroom_id)->where('variant', 'lazada')->first();
        if (!$isLazada) {
            try {
                $sales = $this->saleRepository->all()->where('type', 1);
                return view('sale::sale.index', compact('sales'));
            } catch (\Exception $e) {
                \LogActivity::errorLog($e->getMessage());
                Toastr::error(__('common.Something Went Wrong'));
                return back();
            }
        } else {
            // $lazadaOrders = $this->get_orders('pending');
            // return $lazadaOrders;
            // $dataOrders = $lazadaOrders['data'];

            return view('sale::sale.index_lazada');
        }        
    }

    public function make_return_list()
    {
        try {
            $sales = $this->saleRepository->all()->where('type', 1);
            return view('sale::sale.make_return_list', compact('sales'));
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function conditionalSale()
    {
        try {
            $sales = $this->saleRepository->all()->where('type', 0);
            return view('sale::conditional_sale.index', compact('sales'));
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function create()
    {
        session()->forget('sku');
        $showroom_id = session()->get('showroom_id');
        $isPos = $this->lazadaSet->where('branch_id', $showroom_id)->where('variant', 'pos')->first();

        try {
            $productList = $this->productRepository->productList(session()->get('showroom_id'), ShowRoom::class);
            $serviceList = $this->productRepository->serviceList(session()->get('showroom_id'), ShowRoom::class);

            $data = [
                'allProducts' => $productList,
                'allServices' => $serviceList,
                'customers' => $this->contactRepositories->witoutWalkInCustomer(),
                'warehouses' => $this->wareHouseRepository->all(),
                'showrooms' => $this->showRoomRepository->all(),
                'taxes' => $this->taxRepository->activeTax(),
                'invoice' => $this->introPrefixRepository->find(3),
            ];
            if (!$isPos) {
                return view('sale::sale.create')->with($data);
            } else {
                return view('sale::sale.create_pos')->with($data);
            }

        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function store(SaleRequest $request)
    {

        if ($request->combo_product_id == null && $request->product_id == null && !$request->has('clone')) {
            Toastr::error("Please Add product first.", 'error!');
            return back();
        }
        DB::beginTransaction();
        try {
            $sale = $this->saleRepository->create($request->except("_token"));

            if (!is_numeric($sale)) {
                $created_by = Auth::user()->name;
                $content = 'A Sale has been created by ' . $created_by . ' which Invoice No. is <a href="' . route("sale.show", $sale->id) . '">' . $sale->invoice_no . '</a> for this you have to pay total of ' . $sale->payable_amount . '';
                $message = 'A Sale has been created by ' . $created_by . ', Invoice No: ' . $sale->invoice_no . ', Amount: ' . single_price($sale->payable_amount) . '';
                if ($sale->customer_id != null) {
                    $number = $sale->customer->mobile;
                    $this->sendNotification($sale, $sale->customer->email, 'Sale Create Reminder', $content, $number, $message,null,null,route('sale.show',$sale->id));
                } else {
                    $number = $sale->agentuser->phone;
                    $this->sendNotification($sale, $sale->agentuser->email, 'Sale Create Reminder', $content, $number, $message,null,null,route('sale.show',$sale->id));
                }
            }


            DB::commit();

            if (is_numeric($sale)) {
                Toastr::error(trans('sale.Your stock is out'), 'error!');
                return back();
            } else {
                if ($request->payment_method[0] != "due-00" && !empty($request->amount[0])) {
                    $this->savePaymentFirst($request, $sale->id);
                }
                if ($request->send_mail == 1)
                    $this->send_mail_quotation($sale->id);
                if ($request->preview_status == 1) {
                    Session::put('previewSale', 1);
                    return redirect()->route('sale.edit', $sale->id);
                } else {
                    if (app('business_settings')->where('type', 'sale_approval')->first()->status == 1) {
                        $this->statusChange($sale->id);
                    }else {
                        Toastr::success(__('sale.Sale Added Successfully'));
                    }
                    \LogActivity::successLog('Sale Added Successfully.');
                    if ($request->sale_url == url('conditional-sale/create'))
                        return redirect()->route('conditional-sale.show', $sale->id);
                    else
                        return redirect()->route('sale.show', $sale->id);
                }
            }
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            DB::rollBack();
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }

        // return $request->all();

    }

    public function quotation_to_store(SaleQuotationRequest $request)
    {

        DB::beginTransaction();
        try {
            $sale = $this->saleRepository->create($request->except("_token"));

            if (!is_numeric($sale)) {
                $created_by = Auth::user()->name;
                $content = 'A Sale has been created by ' . $created_by . ' which Invoice No. is <a href="' . route("sale.show", $sale->id) . '">' . $sale->invoice_no . '</a> for this you have to pay total of ' . $sale->payable_amount . '';
                $message = 'A Sale has been created by ' . $created_by . ', Invoice No: ' . $sale->invoice_no . ', Amount: ' . single_price($sale->payable_amount) . '';
                if ($sale->customer_id != null) {
                    $number = $sale->customer->mobile;
                    $this->sendNotification($sale, $sale->customer->email, 'Sale Create Reminder', $content, $number, $message,null,null,route('sale.show',$sale->id));
                } else {
                    $number = $sale->agentuser->phone;
                    $this->sendNotification($sale, $sale->agentuser->email, 'Sale Create Reminder', $content, $number, $message,null,null,route('sale.show',$sale->id));
                }
            }


            DB::commit();

            if (is_numeric($sale)) {
                Toastr::error(trans('sale.Your stock is out'), 'error!');
                return back();
            } else {
                if ($request->payment_method[0] != "due-00" && !empty($request->amount[0])) {
                    $this->savePaymentFirst($request, $sale->id);
                }
                if ($request->send_mail == 1)
                    $this->send_mail_quotation($sale->id);
                if ($request->preview_status == 1) {
                    Session::put('previewSale', 1);
                    return redirect()->route('sale.edit', $sale->id);
                } else {
                    if (app('business_settings')->where('type', 'sale_approval')->first()->status == 1) {
                        $this->statusChange($sale->id);
                    }else {
                        Toastr::success(__('sale.Sale Added Successfully'));
                    }
                    \LogActivity::successLog('Sale Added Successfully.');
                    if ($request->sale_url == url('conditional-sale/create'))
                        return redirect()->route('conditional-sale.show', $sale->id);
                    else
                        return redirect()->route('sale.show', $sale->id);
                }
            }
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            DB::rollBack();
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }

    }

    public function payments($id)
    {
        try {

            $sale = $this->saleRepository->find($id);
            $bank_accounts = \Modules\Account\Entities\ChartAccount::where('configuration_group_id', 2)->get();
            $data = [
                'sale' => $sale,
                'bank_accounts' => $bank_accounts,
            ];

            return view('sale::sale.payment')->with($data);
        } catch (\Exception $e) {
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }

    }

    public function payments_details_sale(Request $request)
    {
        try {

            $sale = $this->saleRepository->find($request->id);
            $data = [
                'sale' => $sale
            ];

            return view('sale::sale.sale_payment_modal')->with($data);
        } catch (\Exception $e) {
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }

    }

    public function savePayment(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required'
        ], [
                'payment_method.required' => 'Please Select method'
            ]
        );
        DB::beginTransaction();

        try {
             $bank_payments = $cash_payments = [];

            if ($request->payment_method) {
                for ($i = 0; $i < count($request->payment_method); $i++) {
                    $req_payment_method = explode('-', $request->payment_method[$i]);
                    if ($req_payment_method[0] == 'bank') {
                        $bank_methods[$i] = [
                            'payment_method' => $req_payment_method[0],
                            'account_id' => $req_payment_method[1],
                            'amount' => $request->amount[$i],
                        ];
                        $banks[] = $i;
                    } else {
                        $cash_payments[$i] = [
                            'payment_method' => $req_payment_method[0],
                            'amount' => $request->amount[$i],
                        ];
                    }
                }
            }
            if (isset($banks))
                foreach ($banks as $key => $bank) {
                    $bank_infos[$key] = [
                        'bank_name' => array_key_exists($key, $request->bank_name) ? $request->bank_name[$key] : '',
                        'branch' => array_key_exists($key, $request->branch) ? $request->branch[$key] : '',
                        'account_no' => array_key_exists($key, $request->account_no) ? $request->account_no[$key] : '',
                        'account_owner' => array_key_exists($key, $request->account_owner) ? $request->account_owner[$key] : '',
                    ];
                    $bank_payments[] = array_merge($bank_methods[$bank], $bank_infos[$key]);
                }

            $payments = array_merge($cash_payments, $bank_payments);

            $this->saleRepository->payments($payments, $id);
            DB::commit();

            Toastr::success('Payment Successful', 'Success!');
            return redirect()->route('sale.show', $id);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            DB::rollBack();
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }


    public function savePaymentFirst(Request $request, $id)
    {
        DB::beginTransaction();
        if ($request->quick_amounts == null && count($request->payment_method) < 1) {
            Toastr::error(trans('sale.Please Select a Payment Method'), '!Error');
            return back();
        }
        try {
            $quick_payments = $bank_payments = $cash_payments = [];
            if ($request->quick_amounts) {
                $quick_payments['quick_cash'] = [
                    'payment_method' => 'quick cash',
                    'amount' => array_sum(explode(',', $request->quick_amounts)),
                ];
            }
            if ($request->payment_method) {
                for ($i = 0; $i < count($request->payment_method); $i++) {
                    $req_payment_method = explode('-', $request->payment_method[$i]);
                    if ($req_payment_method[0] == 'bank') {
                        $bank_methods[$i] = [
                            'payment_method' => $req_payment_method[0],
                            'account_id' => $req_payment_method[1],
                            'amount' => $request->amount[$i],
                        ];
                        $banks[] = $i;
                    } else {
                        $cash_payments[$i] = [
                            'payment_method' => $req_payment_method[0],
                            'amount' => $request->amount[$i],
                        ];
                    }
                }
            }
            if (isset($banks))
                foreach ($banks as $key => $bank) {
                    $bank_infos[$key] = [
                        'bank_name' => array_key_exists($key, $request->bank_name) ? $request->bank_name[$key] : '',
                        'branch' => array_key_exists($key, $request->branch) ? $request->branch[$key] : '',
                        'account_no' => array_key_exists($key, $request->account_no) ? $request->account_no[$key] : '',
                        'account_owner' => array_key_exists($key, $request->account_owner) ? $request->account_owner[$key] : '',
                    ];
                    $bank_payments[] = array_merge($bank_methods[$bank], $bank_infos[$key]);
                }

            $payments = array_merge($cash_payments, $bank_payments, $quick_payments);

            $this->saleRepository->payments($payments, $id, 1);
            DB::commit();
            return 1;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function show($id)
    {
        try {
            $due = 0;
            $sale = $this->saleRepository->find($id);
            if ($sale->customer_id) {
                $due = $sale->customer->accounts['due'];
            }else {
                $due = $sale->user->accounts['due'];
            }

            $data = [
                'sale' => $sale,
                'due' => $due
            ];
            return view('sale::sale.show')->with($data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function send_mail_quotation($id)
    {
        try {
            $data = $this->saleRepository->find($id);
            if ($data->customer->email != null) {
                $datas["email"] = app('general_setting')->email;
                $datas["title"] = EmailTemplate::where('type', 'sale_template')->first()->subject;
                $datas["body"] = EmailTemplate::where('type', 'sale_template')->first()->value;
                $datas["body"] = str_replace("{USER_FIRST_NAME}",$data->customer->name,$datas["body"]);
                $datas["body"] = str_replace("{EMAIL_SIGNATURE}",app('general_setting')->mail_signature,$datas["body"]);
                $datas["body"] = str_replace("{EMAIL_FOOTER}",app('general_setting')->mail_footer,$datas["body"]);
                $pdf = PDF::loadView('sale::sale.pdf', compact('data'))->setPaper('a4', 'portrait');
                Mail::send('sale::emails.quote_mail', $datas, function ($message) use ($data, $datas, $pdf) {
                    $message->to($data->customer->email, $data->customer->email)
                        ->subject($datas["title"])
                        ->attachData($pdf->output(), $data->invoice_no . '.pdf');
                });
                $data->mail_status = 1;
                $data->save();
                Toastr::success('Mail Send tO ' . $data->customer->email . ' Successfully!');
            } else {
                Toastr::error("Customer email Doesn't exist !");
            }
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error('Operation Failed', 'Error!');
            return back();
        }
    }

    public function getPreview(Request $request)
    {
        try {
            $order = $this->saleRepository->find($request->id);
            return view('sale::sale.preview_modal', compact('order'));
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error('Operation Failed', 'Error!');
            return back();
        }
    }

    public function edit($id)
    {
        session()->forget('sku');

        $sale = $this->saleRepository->find($id);

        try {
            $productList = $this->productRepository->productList($sale->saleable_id, $sale->saleable_type);

            $data = [
                'allProducts' => $productList,
                'sale' => $sale,
                'customers' => $this->contactRepositories->customer(),
                'products' => $this->productRepository->allStockProduct(),
                'warehouses' => $this->wareHouseRepository->all(),
                'showrooms' => $this->showRoomRepository->all(),
                'setting' => $this->settingRepository->all(),
                'taxes' => $this->taxRepository->activeTax(),
                'invoice' => $this->introPrefixRepository->find(3),
            ];
            return view('sale::sale.edit')->with($data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function print_view($id)
    {
        try {
            $data['data'] = $this->saleRepository->find($id);
            return view('sale::sale.print_view',$data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error('Operation Failed', 'Error!');
            return back();
        }
    }

    public function print_view_pos($id)
    {
        try {
            $due = 0;
            $sale = $this->saleRepository->find($id);
            if ($sale->customer_id) {
                $due = $sale->customer->accounts['due'];
            }else {
                $due = $sale->user->accounts['due'];
            }

            $data = [
                'sale' => $sale,
                'due' => $due
            ];
            return view('sale::sale.print_view_pos',$data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error('Operation Failed', 'Error!');
            return back();
        }
    }

    public function challan_print_view($id)
    {
        try {
            $data['data'] = $this->saleRepository->find($id);
            return view('sale::sale.challan_print_view',$data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error('Operation Failed', 'Error!');
            return back();
        }
    }

    public function cloneSale($id)
    {
        session()->forget('sku');

        $sale = $this->saleRepository->find($id);

        try {
            $productList = $this->productRepository->productList($sale->saleable_id, $sale->saleable_type);

            $data = [
                'allProducts' => $productList,
                'sale' => $sale,
                'customers' => $this->contactRepositories->customer(),
                'products' => $this->productRepository->allStockProduct(),
                'warehouses' => $this->wareHouseRepository->all(),
                'showrooms' => $this->showRoomRepository->all(),
                'setting' => $this->settingRepository->all(),
                'taxes' => $this->taxRepository->activeTax(),
                'invoice' => $this->introPrefixRepository->find(3),
            ];
            return view('sale::sale.clone_to_sale')->with($data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function update(SaleRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $sale = $this->saleRepository->update($request->except("_token"), $id);

            $created_by = Auth::user()->name;
            $content = 'A Sale has been updated by ' . $created_by . ' which Invoice No. is <a href="' . route("sale.show", $sale->id) . '">' . $sale->invoice_no . '</a> for this you have to pay total of ' . $sale->payable_amount . '';
            $message = 'A Purchase has been approved by ' . $created_by . ', Invoice No: ' . $sale->invoice_no . ', Amount: ' . single_price($sale->payable_amount) . '';

            if ($sale->customer_id != null) {
                $number = $sale->customer->mobile;
                $this->sendNotification($sale, $sale->customer->email, 'Sale Update Reminder', $content, $number, $message);
            } else {
                $number = $sale->agentuser->phone;
                $this->sendNotification($sale, $sale->agentuser->email, 'Sale Update Reminder', $content, $number, $message);
            }
            DB::commit();
            if (is_numeric($sale)) {
                Toastr::error(trans('sale.Your stock is out'), 'error!');
                return back();
            } else {

                \LogActivity::successLog('Sale Updated Successfully without Payment');
                Toastr::success(__('sale.Sale Updated Successfully'));
                return redirect()->route("sale.show", $sale->id);

            }
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            DB::rollBack();
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $sale = $this->saleRepository->find($id);

            $created_by = Auth::user()->name;
            $content = 'A Sale has been destroyed by ' . $created_by . ' which Invoice No. is <a href="' . route("sale.show", $sale->id) . '">' . $sale->invoice_no . '</a> for this you have to pay total of ' . $sale->payable_amount . '';
            $message = 'A Sale has been destroyed by ' . $created_by . ', Invoice No: ' . $sale->invoice_no . ', Amount: ' . single_price($sale->payable_amount) . '';

            if ($sale->customer_id != null) {
                $number = $sale->customer->mobile;
                $this->sendNotification($sale, $sale->customer->email, 'Sale Delete Reminder', $content, $number, $message);
            } else {
                $number = $sale->agentuser->phone;
                $this->sendNotification($sale, $sale->agentuser->email, 'Sale Delete Reminder', $content, $number, $message);
            }

            $sale = $this->saleRepository->delete($id);
            DB::commit();
            Toastr::success(__('sale.Sale Deleted Successfully'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            DB::rollBack();
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function itemDestroy(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->type) {
                PackingItemDetail::destroy($request->id);
            } else
                $this->saleRepository->itemDelete($request->id);

            DB::commit();
            return response()->json(trans('common.Deleted'));

        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            DB::rollBack();
            return trans('common.Something Went Wrong');
        }
    }

    public function returnList()
    {
        $showroom_id = session()->get('showroom_id');
        $isLazada = $this->lazadaSet->where('branch_id', $showroom_id)->first();
        if (!$isLazada) {
            try {
                $items = $this->saleRepository->itemList();
                return view('sale::sale.return_item_list', compact('items'));
            } catch (\Exception $e) {
                \LogActivity::errorLog($e->getMessage());
                Toastr::error(__('common.Something Went Wrong'));
                return back();
            }
        } else {
            $lazadaOrders = $this->get_orders('canceled');
            $dataOrders = $lazadaOrders['data'];
            // return $lazadaOrders;

            return view('sale::sale.index_lazada_return', ['dataOrders'=>$dataOrders]);
        }
    }

    public function saleReturn($id)
    {
        try {
            $data = [
                'sale' => $this->saleRepository->find($id),
                'customers' => $this->contactRepositories->customer(),
                'products' => $this->productRepository->all(),
                'warehouses' => $this->wareHouseRepository->all(),
                'showrooms' => $this->showRoomRepository->all(),
            ];
            return view('sale::sale.return_sale')->with($data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function return_details($id)
    {
        try {
            $data = [
                'sale' => $this->saleRepository->find($id),
                'customers' => $this->contactRepositories->customer(),
                'products' => $this->productRepository->all(),
                'warehouses' => $this->wareHouseRepository->all(),
                'showrooms' => $this->showRoomRepository->all(),
            ];
            return view('sale::sale.return_sale_details')->with($data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function returnItem(Request $request, $id)
    {
        // return $request->all();
        DB::beginTransaction();
        try {
            $item = [];
            if ($request->items) {
                for ($i = 0; $i < count($request->items); $i++) {
                    $item[$i] = [
                        'item_id' => $request->items[$i],
                        'quantity' => $request->quantity[$i],
                    ];
                }
            }
            if (array_sum($request->quantity) > 0) {
                $this->saleRepository->itemUpdate($request->except("_token"), $item, $id);
                \LogActivity::successLog('Item Returned Successfully');
                Toastr::success(__('sale.Item Returned Successfully'));
                DB::commit();
                return back();
            } else {
                DB::rollBack();
                Toastr::error(__('sale.Please Select Quantity to Return'));
                return back();
            }

        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            DB::rollBack();
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function statusChange($id)
    {
        DB::beginTransaction();
        try {
            $sale = $this->saleRepository->statusChange($id);

            $created_by = Auth::user()->name;
            $content = 'A Sale has been approved which was created by ' . $created_by . ' which Invoice No. is <a href="' . route("sale.show", $sale->id) . '">' . $sale->invoice_no . '</a> for this you have to pay total of ' . $sale->payable_amount . '';
            $message = 'A Sale has been approved by ' . $created_by . ', Invoice No: ' . $sale->invoice_no . ', Amount: ' . single_price($sale->payable_amount) . '';

            if ($sale->customer_id != null) {
                $number = $sale->customer->mobile;
                $this->sendNotification($sale, $sale->customer->email, 'Sale Create Reminder', $content, $number, $message);
            } else {
                $number = $sale->agentuser->phone;
                $this->sendNotification($sale, $sale->agentuser->email, 'Sale Create Reminder', $content, $number, $message);
            }
            DB::commit();
            Toastr::success(__('sale.Sale has Been Approved'), 'success!');
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function returnApprove($id)
    {

        DB::beginTransaction();
        try {
            $this->saleRepository->returnApprove($id);
            DB::commit();
            Toastr::success('Return has Been Approved', 'success!');
            return back();
        } catch (\Exception $e) {

            \LogActivity::errorLog($e->getMessage());
            DB::rollBack();
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function saleOrder(Request $request)
    {
        try {
            $this->saleRepository->acceptOrder($request->except('_token'));
            Toastr::success(trans('sale.Sale Info has Been Updated'), 'success!');
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    //Ajax Reuqest for getting Shipping Info
    public function shippingInfo(Request $request)
    {
        try {
            $shipping = $this->saleRepository->find($request->id)->shipping;
            $shipping['date'] =  $shipping['date'] ? date(app('general_setting')->dateFormat->format, strtotime($shipping['date'])) : '' ;
            $shipping['received_date'] =  $shipping['received_date'] ? date(app('general_setting')->dateFormat->format, strtotime($shipping['received_date'])) : '';
            return response()->json($shipping);
        } catch (\Exception $e) {
            return response()->json(['error' => __('common.Something Went Wrong')]);
        }
    }

    //Ajax Product Modal Render for Products
    public function product_modal_for_select(Request $request)
    {
        try {
            $type = explode('-', $request->id);
            if ($type[1] == "Single" or $type[1] == "Service") {

                $data['product_id'] = $this->storeSkuSale($type[0], $request->customer);
                $data['product_type'] = $type[1];
                return $data;

            } elseif ($type[1] == "Combo") {
                $data['product_id'] = $this->storeCombo($type[0], $request->customer);
                $data['product_type'] = $type[1];
            } else {
                $data['product_id'] = $type[0];
                $data['product_type'] = $type[1];

                $data['html'] = (string)view('sale::sale.product_details', [
                    "product" => $this->productRepository->find($type[0])
                ]);
            }

            return response()->json($data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(['error' => __('common.Something Went Wrong')]);
        }
    }

    public function product_modal_for_select_pos(Request $request)
    {
        try {
            $type = explode('-', $request->id);
            if ($type[1] == "Single" or $type[1] == "Service") {

                $data['product_id'] = $this->storeSkuSalePos($type[0]);
                $data['product_type'] = $type[1];
                return $data;

            } elseif ($type[1] == "Combo") {
                $data['product_id'] = $this->storeComboPos($type[0]);
                $data['product_type'] = $type[1];
            } else {
                $data['product_id'] = $type[0];
                $data['product_type'] = $type[1];

                $data['html'] = (string)view('sale::sale.product_details', [
                    "product" => $this->productRepository->find($type[0])
                ]);
            }

            return response()->json($data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(['error' => __('common.Something Went Wrong')]);
        }
    }

    //Ajax Request for getting Customer Last Invoice No
    public function customerDetails(Request $request)
    {
        $due = $url = $invoice = '';
        try {
            if ($request->pos) {
                $customer_info = explode('-', $request->customer_id);
                $id = $customer_info[1];
                session()->put('customer', $request->customer_id);

                $customer = $this->contactRepositories->find($id);

                $sale = $customer->lastInvoice;
            } else {
                $type = explode('-', $request->customer_id);
                session()->put('customer', $request->customer_id);
                $id = $type[1];

                $customer = $this->contactRepositories->find($id);

                $sale = $customer->lastInvoice;
            }


            $due = $customer->accounts ? single_price($customer->accounts['due']) : '';


            if ($sale) {
                $url = route('sale.show', $sale->id);
                $invoice = $sale->invoice_no;
            }

            return response()->json([
                'due' => $due,
                'url' => $url,
                'invoice' => $invoice,
                'credit_limit' => $customer->credit_limit,
            ]);
        } catch (\Exception $e) {
            return response()->json(trans('common.Something Went Wrong'));
        }
    }

    public function invoiceDetails(Request $request)
    {

        try {
            $type = explode('-', $request->customer_id);

            $customer = $this->contactRepositories->find($type[1]);

            $data = [
                'customer' => $customer,
                'sale' => $customer->lastInvoice,
            ];
            return view('sale::sale.invoice_details')->with($data);
        } catch (\Exception $e) {
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }

    public function orderDetails(Request $request)
    {
        try {
            $due = 0;
            $pos = 0;
            if ($request->pos) {
                $customer_info = explode('-', $request->customer_id);
                $id = $customer_info[1];
                session()->put('customer', $request->customer_id);
                $customer = $this->contactRepositories->find($id);
                $sale = $customer->lastPosInvoice;

            } elseif (!$request->pos && $request->customer_id) {
                $type = explode('-', $request->customer_id);
                $customer = $this->contactRepositories->find($type[1]);

                $sale = $customer->lastInvoice;

            } else
                $sale = $this->saleRepository->find($request->id);


            if ($sale) {
                $payments = $this->saleRepository->customerDues($sale->customer_id, $request->id);

                if ($payments['payable_price'] > $payments['paid_price']) {
                    $due = $payments['payable_price'] - $payments['paid_price'];
                }
            }
            if ($request->type)
                $pos = 1;

            $data = [
                'sale' => $sale,
                'due' => $due,
                'pos' => $pos,
            ];
            return view('sale::sale.get_details')->with($data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function storeShipping(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required',
            'shipping_ref' => 'required',
            'shipping_date' => 'required',
            'booking_slip' => 'nullable|mimes:jpeg,jpg,png,pdf',
            'prove_of_delivery' => 'nullable|mimes:jpeg,jpg,png,pdf'
        ]);

        $this->saleRepository->storeShipping($request->except('_token'));
        return response()->json('ok');
    }

    public function getPdf($id)
    {
        try {
            $data = $this->saleRepository->find($id);
            return $this->getInvoice('sale::sale.pdf', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error('Operation Failed', 'Error!');
            return back();
        }
    }

    public function getChallanPdf($id)
    {
        try {
            $data = $this->saleRepository->find($id);
            return $this->getChallan('sale::sale.challan_pdf', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error('Operation Failed', 'Error!');
            return back();
        }
    }

    //Ajax Request for Storing Products in Sale,Purchase,Pos
    public function storeProduct(Request $request)
    {
        try {
            $last_price = '';
            $tax = 0;

            $productSku = $this->productRepository->findSku($request->id);
            $type = $productSku->id . ",'sku'";
            $last_price .= '<td style="text-align: right" class="last_price_td">';
            if ($request->sale_customer) {
                $customer_type = explode('-', $request->sale_customer);
                $customer = $this->contactRepositories->find($customer_type[1]);
                if ($customer != null) {
                    $sale = $customer->lastInvoice;
                    if ($sale) {

                        $product_item = $sale->items->where('productable_id', $request->id)->where('productable_type', 'Modules\Product\Entities\ProductSku')->first();
                        if ($product_item) {
                            $last_price .= '<input name="last_price" class="primary_input_field product_price product_price_sku' . $productSku->id . '" type="number"
                            value="' . $product_item->price . '" readonly>';
                        }
                    }
                }
            }
            $last_price .= '</td>';

            $skus = session()->get('sku');

            $sku[$productSku->sku] = $productSku->sku;

            if (!empty($skus) && count($skus) > 0) {
                if (array_key_exists($productSku->sku, $skus)) {
                    return 1;
                }
                session()->put('sku', $sku + $skus);
            } else {
                session()->put('sku', $sku);
            }

            $sub_total = ($productSku->selling_price);

            $variantName = $this->variationRepository->variantName($productSku);
            $option = '';
            foreach ($productSku->part_numbers->where('is_sold', 0) as $key => $part_number) {
                $option .= '<option value="'.$part_number->id.'">'.$part_number->seiral_no.'</option>';
            }
            if (app('general_setting')->origin == 1) {
                $row = '<td class="product_sku' . $productSku->id . '">' . $productSku->product->origin . '</td>';
            }else {
                $row = '<td class="product_sku' . $productSku->id . '">' . $productSku->sku . '</td>';
            }
            $output = '';
            $output .= '<tr>
                        <td><input type="hidden" name="product_id[]" value="' . $productSku->id . '" class="primary_input_field sku_id' . $productSku->id . '">' . $productSku->product->product_name . '</br>' . $variantName . '</td>

                        '.$row.'
                        <td>'.@$productSku->product->model->name.'</td>
                        <td>'.@$productSku->product->brand->name.'</td>
                        <td class="d-none">
                        <select class="primary_select mb-15 sale_type" id="serial_no" name="serial_no[]" multiple>
                            '.$option.'
                        </select>
                        </td>

                        <td><input name="product_price[]" min="' . $productSku->cost_of_goods . '" onkeyup="priceCalc(' . $type . ')" class="primary_input_field product_price product_price_sku' . $productSku->id . '" type="number"
                        value="' . $productSku->selling_price . '"></td>

                            ' . $last_price . '


                        <td>
                            <input type="number" name="product_quantity[]" value="1" onkeyup="addQuantity(' . $type . ')" class="primary_input_field quantity quantity_sku' . $productSku->id . '">
                        </td>

                        <td>
                            <input type="number" name="product_tax[]"  value="' . $productSku->tax . '" onkeyup="addTax(' . $type . ')" class="primary_input_field tax tax_sku' . $productSku->id . '">
                        </td>

                        <td>
                            <input type="number" name="product_discount[]" value="0" onkeyup="addDiscount(' . $type . ')" class="primary_input_field discount discount_sku' . $productSku->id . '">
                        </td>
                        <td style="text-align:center" class="product_subtotal product_subtotal_sku' . $productSku->id . '">' . $sub_total . '</td>
                        <td><a data-id="' . $productSku->id . '" data-product="' . $productSku->id . '-Normal" class="primary-btn primary-circle fix-gr-bg delete_product" href="javascript:void(0)"><i class="ti-trash"></i></a></td>
                        </tr>

                        ';

            return response()->json($output);

        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(['error' => trans('common.Something Went Wrong')]);
        }
    }

    public function convertToQuotation($id)
    {
        session()->forget('sku');
        $quotation = $this->saleRepository->find($id);

        try {
            if ($quotation->quotationable_id != null) {
                $productList = $this->productRepository->productList($quotation->quotationable_id, $quotation->quotationable_type);
            } else {
                $productList = $this->productRepository->productList(1, 'Modules\Inventory\Entities\ShowRoom');
            }

            $data = [
                'allProducts' => $productList,
                'quotation' => $quotation,
                'customers' => $this->contactRepositories->witoutWalkInCustomer(),
                'products' => $this->productRepository->allStockProduct(),
                'warehouses' => $this->wareHouseRepository->all(),
                'showrooms' => $this->showRoomRepository->all(),
                'taxes' => $this->taxRepository->activeTax(),
                'invoice' => $this->introPrefixRepository->find(4),
            ];
            return view('sale::sale.clone_quotation')->with($data);
        } catch (\Exception $e) {
            Toastr::success('Oops, Something Went Wrong', 'Error!');
            return back();
        }
    }

    public function dueList()
    {
        try {
            $dues = $this->saleRepository->dueList('all');
            return view('sale::sale.due_list', compact('dues'));
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function itemSessionDelete(Request $request)
    {
        $productSku = $this->productRepository->findSku($request->id);
        $skus = session()->get('sku');
        $carts = session()->get('carts');

        unset($skus[$productSku->sku]);
        unset($carts['sku-' . $productSku->id]);
        session()->put('sku', $skus);
        session()->put('carts', $carts);

        return response()->json(['success' => trans('common.Deleted')]);
    }

    public function invoiceList()
    {
        try {
            $sales = $this->saleRepository->dueInvoiceList();
            return view('sale::sale.index', compact('sales'));
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function saleConfiguration()
    {
        return view('sale::sale.configurations');
    }

    public function orderDetailsLazada(Request $request)
    {
        try {
            $data = [
                'sale' => $this->get_orderItem($request->ordernumber, $request->token),
                'token' => $request->token,
            ];
            // echo json_encode($data);
            // return;
            return view('sale::sale.get_details_lazada')->with($data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function get_reverse_orders($status = '') {
        
        $tokenwehave = $this->accessToken;
        $arr = [];
        $method = 'GET';
        $apiName = '/reverse/getreverseordersforseller';
        
        foreach ($tokenwehave as $key => $value) {
            $c = new LazopClient($this->apiGateway, $this->apiKey, $this->apiSecret);
            $request = new LazopRequest($apiName,$method);
            // $request->addApiParam('ofc_status_list','[\"RETURN\"]');
            // $request->addApiParam('reverse_order_id','0');
            // $request->addApiParam('trade_order_id','0');
            $request->addApiParam('page_size','10');
            // $request->addApiParam('reverse_status_list','[\"REQUEST_INITIATE\"]');
            $request->addApiParam('page_no','1');
            // $request->addApiParam('return_to_type','RTM');
            // $request->addApiParam('dispute_in_progress','true');
            if ($status != '') {
                $request->addApiParam('status', $status);
            }
            $executelazop = json_decode($c->execute($request, $value['token']), true);

            if (isset($executelazop['data']['orders'])) {

                $data = $executelazop['data']['orders'];
            
                for ($i=0; $i < count($data); $i++) { 
                    $data[$i]['nama_akun'] = $value['akun_name'];
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

        return $res;
    }

    public function get_orders($status = '', $saleDate = '')
    {
        // if ($saleDate != '') {            
            $theDate = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
            // if ($status == 'pending' || $status == 'canceled' || $status == 'failed' || $status == 'lost_by_3pl') {
                $daysToMin = 2;
                $datestart = Carbon::now()->subMonth()->format('Y-m-d').'T00:00:00+08:00';
                $daysToAdd = 1;
                $dateend = Carbon::now()->addDays($daysToAdd)->format('Y-m-d').'T01:00:00+08:00';
        //     } else {
        //         $datestart = $theDate->format('Y-m-d').'T00:00:00+08:00';
        //         $daysToAdd = 1;
        //         $dateend = $theDate->addDays($daysToAdd)->format('Y-m-d').'T01:00:00+08:00';
        //     }
        // } else {
        //     $theDate = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
        //     $daysToAdd = 1;
        //     $datestart = $theDate->format('Y-m-d').'T00:00:00+08:00';
        //     $dateend = $theDate->addDays($daysToAdd)->format('Y-m-d').'T01:00:00+08:00';
        // }        
        
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
            $request->addApiParam('limit','100');
            // $request->addApiParam('update_after','2017-02-10T09:00:00+08:00');
            $request->addApiParam('sort_by','created_at');
            $request->addApiParam('created_before', $dateend);
            $request->addApiParam('created_after', $datestart);
            if ($status != '') {
                $request->addApiParam('status', $status);
            }
            $executelazop = json_decode($c->execute($request, $value['token']), true);

            if (isset($executelazop['data']['orders'])) {

                $data = $executelazop['data']['orders'];
            
                for ($i=0; $i < count($data); $i++) { 
                    $data[$i]['nama_akun'] = $value['akun_name'];
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

        foreach ($executelazop['data'] as $key => $value) {

            $comboProducts = $this->productRepository->oneComboProduct($value['shop_sku']);
            if (count($comboProducts) > 0) {
                $products = $this->productRepository->findCombo($comboProducts[0]['id']);
                // $products = $products->combo_products;

                $executelazop['data'][$key]['det_prod'] = $products;
            }            
        }

        if ($url == false) {
            return $executelazop['data'];
        } else {
            return $executelazop;
        }
    }

    public function lazadaList(Request $request)
    {
        $status = $request->status;
        $saleDate = '';
        $arrStatus = [];
        if ($status == 'returned') {
            $arrStatus[0] = $status;
        } else if ($status == 'failed') {
            $arrStatus[0] = $status;
            $arrStatus[1] = 'shipped_back';
            $arrStatus[2] = 'shipped_back_success';
            $arrStatus[3] = 'shiped_back_failed';
            $arrStatus[4] = 'package_scrapped';
        } else if ($status == 'lost_by_3pl') {
            $arrStatus[0] = $status;
            $arrStatus[1] = 'damaged_by_3pl';
        } else {
            $arrStatus[0] = $status;
        }

        $lazadaOrders = $this->get_orders($status,$saleDate);
        $dataOrders = $lazadaOrders['data'];

        $htmlBody = view('sale::sale.lazadaSaleList', ['dataOrders'=>$dataOrders])->render();
        $countspan = count($dataOrders);
        $res['body'] = $htmlBody;
        $res['countspan'] = $countspan;
        return $res;
    }

    public function lazadaListReverse(Request $request)
    {
        $status = $request->status;
        $saleDate = $request->saleDate;
        $arrStatus = [];

        $lazadaOrders = $this->get_reverse_orders($status,$saleDate);
        // $dataOrders = $lazadaOrders['data'];

        // $htmlBody = view('sale::sale.lazadaSaleList', ['dataOrders'=>$dataOrders])->render();

        return $lazadaOrders;
    }

    public function getCombo(Request $request)
    {
        $querystring = $request->all();
        if (isset($querystring['skulazada'])) {
            $skulazada = $querystring['skulazada'];
        } else {
            echo 'failed sku lazadas';
            return;
        }
        $comboProducts = $this->productRepository->oneComboProduct($skulazada);

        $products = $this->productRepository->findCombo($comboProducts[0]['id']);
        $products = $products->combo_products;
        foreach ($products as $key => $value) {
            $products[$key]['qty'] = $value->product_qty;
        }
        echo $products;
        return;
    }

    public function setToPacked(SaleRequest $request) 
    {
        // $saveInStore = $this->store($request->except("_token")); 

        if ($request->combo_product_id == null && $request->product_id == null && !$request->has('clone')) {
            Toastr::error("Please Add product first.", 'error!');
            return back();
        }
        DB::beginTransaction();
        try {
            $sale = $this->saleRepository->create($request->except("_token","token","shippingType","orderItemId"));
            // return $sale;

            if (!is_numeric($sale)) {
                $created_by = Auth::user()->name;
                $content = 'A Sale has been created by ' . $created_by . ' which Invoice No. is <a href="' . route("sale.show", $sale->id) . '">' . $sale->invoice_no . '</a> for this you have to pay total of ' . $sale->payable_amount . '';
                $message = 'A Sale has been created by ' . $created_by . ', Invoice No: ' . $sale->invoice_no . ', Amount: ' . single_price($sale->payable_amount) . '';
                if ($sale->customer_id != null) {
                    $number = $sale->customer->mobile;
                    $this->sendNotification($sale, $sale->customer->email, 'Sale Create Reminder', $content, $number, $message,null,null,route('sale.show',$sale->id));
                } else {
                    $number = $sale->agentuser->phone;
                    $this->sendNotification($sale, $sale->agentuser->email, 'Sale Create Reminder', $content, $number, $message,null,null,route('sale.show',$sale->id));
                }
            }


            DB::commit();

            if (is_numeric($sale)) {
                Toastr::error(trans('sale.Your stock is out'), 'error!');
                return 'stock_out';
            } else {
                if ($request->payment_method[0] != "due-00" && !empty($request->amount[0])) {
                    $this->savePaymentFirst($request, $sale->id);
                }
                if ($request->send_mail == 1)
                    $this->send_mail_quotation($sale->id);
                if ($request->preview_status == 1) {
                    Session::put('previewSale', 1);
                    return 'success';
                } else {
                    if (app('business_settings')->where('type', 'sale_approval')->first()->status == 1) {
                        $this->statusChange($sale->id);
                    }else {
                        Toastr::success(__('sale.Sale Added Successfully'));
                    }
                    \LogActivity::successLog('Sale Added Successfully.');

                    $querystring = $request->all();
                    if ( isset($querystring['orderItemId']) == false || isset($querystring['token']) == false || isset($querystring['shippingType']) == false ) {
                        echo 'query string required';
                        return;
                    } 

                    $shippingType = strtolower($querystring['shippingType']);
                    $arr = [];
                    $method = 'POST';
                    $apiName = '/order/pack';

                    $c = new LazopClient($this->apiGateway, $this->apiKey, $this->apiSecret);
                    $request = new LazopRequest($apiName,$method);
                    // $request->addApiParam('shipping_provider','Aramax');
                    $request->addApiParam('delivery_type', 'dropship');
                    $request->addApiParam('order_item_ids', '['.$querystring['orderItemId'].']');
                    // $request->addApiParam('order_item_ids', '[123123]');
                    $executelazop = json_decode($c->execute($request, $querystring['token']), true);

                    return $executelazop;

                    return 'success';
                }
            }
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            DB::rollBack();
            Toastr::error(__('common.Something Went Wrong'));
            return $e->getMessage();
        }

        // return $saveInStore;
    }

    public function setToRts(Request $request)
    {
        $querystring = $request->all();
        if ( isset($querystring['orderItemId']) == false || isset($querystring['token']) == false ) {
            echo 'query string required';
            return;
        } 
        
        $arr = [];
        $method = 'POST';
        $apiName = '/order/rts';

        $c = new LazopClient($this->apiGateway, $this->apiKey, $this->apiSecret);
        $request = new LazopRequest($apiName,$method);
        $request->addApiParam('delivery_type', 'dropship');
        $request->addApiParam('order_item_ids', '['.$querystring['orderItemId'].']');
        // $request->addApiParam('shipment_provider','Aramax');
        // $request->addApiParam('tracking_number','12345678');
        $executelazop = json_decode($c->execute($request, $querystring['token']), true);

        return $executelazop;
    }

    public function setToCancel(Request $request) 
    {
        // return $request;
        $orderNumberId = $this->saleRepository->findRef($request->orderId);
        if (!$orderNumberId) {
            \LogActivity::successLog('order not found id:'.$request->orderId);
            Toastr::error('order not found id:'.$request->orderId);
            DB::commit();
            return 'order not found id:'.$request->orderId;
        }
        // return $request->all();
        DB::beginTransaction();
        try {
            $item = [];
            if ($request->items) {
                for ($i = 0; $i < count($request->items); $i++) {
                    $item[$i] = [
                        'item_id' => $request->items[$i],
                        'quantity' => $request->quantity[$i],
                    ];
                }
            }
            if (array_sum($request->quantity) > 0) {
                $this->saleRepository->itemUpdate($request->except("_token"), $item, $orderNumberId['id']);
                \LogActivity::successLog('Item Returned Successfully');
                Toastr::success(__('sale.Item Returned Successfully'));
                DB::commit();
                return 'success';
            } else {
                DB::rollBack();
                Toastr::error(__('sale.Please Select Quantity to Return'));
                return 'Please Select Quantity to Return';
            }

        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            DB::rollBack();
            Toastr::error(__('common.Something Went Wrong'));
            return $e->getMessage();
        }
    }

    public function getDocument(Request $request)
    {
        $querystring = $request->all();
        if ( isset($querystring['orderItemId']) == false || isset($querystring['token']) == false || isset($querystring['doctype']) == false) {
            echo 'query string required!';
            return;
        } 
        
        $arr = [];
        $method = 'GET';
        $apiName = '/order/document/get';

        $c = new LazopClient($this->apiGateway, $this->apiKey, $this->apiSecret);
        $request = new LazopRequest($apiName,$method);
        $request->addApiParam('order_item_ids', '['.$querystring['orderItemId'].']');
        $request->addApiParam('doc_type', $querystring['doctype']);
        $executelazop = json_decode($c->execute($request, $querystring['token']), true);

        
        $fileBase = base64_decode($executelazop['data']['document']['file']);

        if ($executelazop['data']['document']['document_type'] == 'shippingLabel') {
            return view('sale::sale.print_shipping', ['fileBase'=>$fileBase]);
        } else if ($executelazop['data']['document']['document_type'] == 'invoice') {
            return view('sale::sale.print_invoice', ['fileBase'=>$fileBase]);
        }
    }

    public function rtsMultiple(Request $request)
    {
        $querystring = $request->all();
        if ( isset($querystring['orderItemId']) == false) {
            echo 'query string required!';
            return;
        }  

        $collect = [];
        $orderitemar = explode(",",$querystring['orderItemId']);
        foreach ($orderitemar as $key => $value) {
            $expl = explode("-",$value);
            $token = $expl[0];
            $orderid = $expl[1];
            $orderitem = $this->lazopSetToAction($orderid, $token, 'rts');
            $collect[$key] = $orderitem;
        }

        return $collect;
    }

    public function packedMultiple(Request $request)
    {
        $querystring = $request->all();
        if ( isset($querystring['orderItemId']) == false) {
            echo 'query string required!';
            return;
        }  

        $collect = [];
        $orderitemar = explode(",",$querystring['orderItemId']);
        foreach ($orderitemar as $key => $value) {
            $expl = explode("-",$value);
            $token = $expl[0];
            $orderid = $expl[1];
            $orderitem = $this->lazopSetToAction($orderid, $token, 'pack');
            $collect[$key] = $orderitem;
        }

        return $collect;
    }

    public function lazopSetToAction($orderItemId, $token, $actionto)
    {
        $result = [];
        $itemId = [];
        $getiditem = $this->get_orderItem($orderItemId, $token);
        // $itemId[$key]['iditem'] = $orderitem;
        // $itemId[$key]['token'] = $token;

        foreach ($getiditem as $key => $value) {
            $arr = [];
            $method = 'POST';
            $apiName = '/order/'.$actionto;

            $c = new LazopClient($this->apiGateway, $this->apiKey, $this->apiSecret);
            $request = new LazopRequest($apiName,$method);
            $request->addApiParam('delivery_type', 'dropship');
            $request->addApiParam('order_item_ids', '['.$value['order_item_id'].']');
            // $request->addApiParam('shipment_provider','Aramax');
            // $request->addApiParam('tracking_number','12345678');
            $executelazop = json_decode($c->execute($request, $token), true);
            $result[$key] = $executelazop;
        }

        return $result;
    }

    public function getMultipleOrderItem(Request $request)
    {
        $querystring = $request->all();
        if ( isset($querystring['orderItemId']) == false || isset($querystring['doctype']) == false) {
            echo 'query string required!';
            return;
        } 

        $collect = [];
        $orderitemar = explode(",",$querystring['orderItemId']);
        foreach ($orderitemar as $key => $value) {
            $expl = explode("-",$value);
            $token = $expl[0];
            $orderid = $expl[1];
            $orderitem = $this->get_orderItem($orderid, $token);
            $collect[$key] = $orderitem;
        }

        $ress = [];
        foreach ($collect as $key => $value) {
            $ress[$key]['sku'] = $value[0]['sku'];
            $ress[$key]['product_main_image'] = $value[0]['product_main_image'];
            $ress[$key]['name'] = $value[0]['name'];
            $ress[$key]['order_id'] = $value[0]['order_id'];
            $ress[$key]['tracking_code'] = $value[0]['tracking_code'];
        }

        $result = [];
        foreach ($ress as $key => $value) {
            if (isset($result[$value['sku']]) == false) {
                $result[$value['sku']]['sku'] = $value['sku'];
                $result[$value['sku']]['product_main_image'] = $value['product_main_image'];
                $result[$value['sku']]['name'] = $value['name'];
                $result[$value['sku']]['order_id'] = $value['order_id'];
                $result[$value['sku']]['tracking_code'] = $value['tracking_code'];
                $result[$value['sku']]['qty'] = 1;
            } else {
                $result[$value['sku']]['order_id'] .= ', '.$value['order_id'];
                $result[$value['sku']]['tracking_code'] .= ', '.$value['tracking_code'];
                $result[$value['sku']]['qty'] = $result[$value['sku']]['qty'] + 1;
            }
        }
        
        return view('sale::sale.pick_list', ['result'=>$result]);
        // return $result;
    }

    public function getDocumentMultiple(Request $request)
    {
        $querystring = $request->all();
        if ( isset($querystring['orderItemId']) == false || isset($querystring['doctype']) == false) {
            echo 'query string required!';
            return;
        } 

        $collect = [];
        $orderitemar = explode(",",$querystring['orderItemId']);
        foreach ($orderitemar as $key => $value) {
            $expl = explode("-",$value);
            $token = $expl[0];
            $orderid = $expl[1];
            $orderitem = $this->get_orderItem($orderid, $token);
            $orderitem = $orderitem[0]['order_item_id'];
            if (isset($collect[$token]) == false) {
                $collect[$token] = $orderitem;
            } else {
                $collect[$token] = $collect[$token].','.$orderitem;
            }
        }

        $fileBase = [];

        foreach ($collect as $key => $value) {
            $arr = [];
            $method = 'GET';
            $apiName = '/order/document/awb/pdf/get';

            $c = new LazopClient($this->apiGateway, $this->apiKey, $this->apiSecret);
            $request = new LazopRequest('/order/document/awb/pdf/get',$method);
            $request->addApiParam('order_item_ids', '['.$value.']');
            $request->addApiParam('doc_type', $querystring['doctype']);
            $executelazop = json_decode($c->execute($request, $key), true);

            
            $fileBase[] = base64_decode($executelazop['data']['document']['file']);
        }     
        
        if ($executelazop['data']['document']['document_type'] == 'shippingLabel') {
            return view('sale::sale.print_shipping', ['fileBase'=>$fileBase]);
        } else if ($executelazop['data']['document']['document_type'] == 'invoice') {
            return view('sale::sale.print_invoice', ['fileBase'=>$fileBase]);
        }
    }



    public function get_transaction(Request $request)
    {
        $querystring = $request->all();
        if (isset($querystring['date'])) {
            $date = $querystring['date'];
        } else {
            $date = "2021-08-14";
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

    public function getReverseReason()
    {
        $tokenwehave = $this->accessToken;
        $arr = [];
        $method = 'GET';
        $apiName = '/order/reverse/reason/list';

        $c = new LazopClient($this->apiGateway, $this->apiKey, $this->apiSecret);
        $request = new LazopRequest($apiName,$method);
        $request->addApiParam('reverse_order_line_id','0');
        $executelazop = json_decode($c->execute($request, $tokenwehave[0]['token']), true);

        return $executelazop;
    }

    public function formrevrese($id)
    {
        // return $lazadaOrders = $this->getReverseReason();
        return view('sale::sale.formreverse');
    }

}
