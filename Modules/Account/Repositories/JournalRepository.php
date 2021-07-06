<?php

namespace Modules\Account\Repositories;

use Illuminate\Support\Arr;
use Modules\Account\Entities\Voucher;
use Modules\Account\Entities\ChartAccount;

class JournalRepository implements JournalRepositoryInterface
{
    public function all()
    {
        return ChartAccount::with("chart_accounts")
            ->where('status', 1)
            ->get();
    }

    public function transactionalAccounts()
    {
        return ChartAccount::with("chart_accounts")
            ->where('is_group', 0)->where('status', 1)
            ->get();
    }

    public function expenseAccounts()
    {
        return ChartAccount::with("chart_accounts")
            ->where('type', 4)->where('status', 1)
            ->get();
    }

    public function journal_all()
    {
        return Voucher::where('payment_type', 'journal_voucher')->latest()->get();
    }

    public function create($data)
    {
        $Voucher = '';
        $transactions = $this->trranactionEntry($data);
        $Voucher = Voucher::create([
            'amount'=> $data['amount'],
            'date'=> $data['date'],
            'narration' =>  $data['narration'],
            'voucher_type' => $data['voucher_type'],
            'payment_type' => $data['payment_type'],
            'is_approve' => $data['is_approve']
        ]);

        $Voucher->transactions()->createMany($transactions);

        foreach($Voucher->transactions as $transaction) {
            if($transaction->type == 'Cr'){
                $transaction->fromAccounts()->attach($data['sub_account_id']);
            }else{
                $transaction->fromAccounts()->attach($data['account_id']);
            }
        }
        $Voucher->update(['tx_id' => $data['voucher_type'].'-'.$Voucher->id]);

        if (!empty($data['sale_id']) && !empty($data['sale_class'])) {
            $Voucher->update(['tx_id' => $data['voucher_type'].'-'.$Voucher->id , 'referable_id' => $data['sale_id'], 'referable_type' => $data['sale_class']]);
        }else {
            $Voucher->update(['tx_id' => $data['voucher_type'].'-'.$Voucher->id]);
        }

        return $Voucher->load('transactions');
    }

    protected function trranactionEntry($data)
    {
        if ($data['account_type'] == 'debit') {
            if(is_array($data['sub_account_id'])){
                $conver_date = [];
                for ($i=0; $i < count($data['sub_account_id']); $i++) {
                    array_unshift($conver_date,[
                        'account_id' => $data['sub_account_id'][$i],
                        'type' => 'Cr',
                        'amount' => $data['sub_amount'][$i],
                        'narration' => $data['sub_narration'][$i],
                    ]);
                }
                array_unshift($conver_date,[
                    'account_id' => $data['account_id'],
                    'type' => 'Dr',
                    'amount' => $data['main_amount'],
                ]);
                return $conver_date;
            }
        }else {
            if(is_array($data['sub_account_id'])){
                $conver_date = [];
                for ($i=0; $i < count($data['sub_account_id']); $i++) {
                    array_unshift($conver_date,[
                        'account_id' => $data['sub_account_id'][$i],
                        'type' => 'Dr',
                        'amount' => $data['sub_amount'][$i],
                        'narration' => $data['sub_narration'][$i],
                    ]);
                }
                array_unshift($conver_date,[
                    'account_id' => $data['account_id'],
                    'type' => 'Cr',
                    'amount' => $data['main_amount'],
                ]);
                return $conver_date;
            }
        }
    }

    public function find($id)
    {
        return Voucher::findOrFail($id);
    }

    public function update(array $data, $id)
    {
        $Voucher = '';
        $transactions = $this->trranactionEntry($data);
        $Voucher = Voucher::findOrFail($id);
        $Voucher->update([
            'amount'=> $data['amount'],
            'date'=> $data['date'],
            'narration' =>  $data['narration'],
            'voucher_type' => $data['voucher_type'],
            'payment_type' => $data['payment_type'],
            'is_approve' => $data['is_approve']
        ]);

        foreach ($Voucher->transactions as $key => $transaction) {
            $transaction->delete($transaction->id);
        }

        $Voucher->transactions()->createMany($transactions);
        foreach($Voucher->transactions as $transaction) {
            if($transaction->type == 'Cr'){
                $transaction->fromAccounts()->attach($data['sub_account_id']);
            }else{
                $transaction->fromAccounts()->attach($data['account_id']);
            }
        }


        $Voucher->update(['tx_id' => $data['voucher_type'].'-'.$Voucher->id]);

        return $Voucher->load('transactions');
    }
}
