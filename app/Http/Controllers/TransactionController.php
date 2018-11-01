<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Transaction;
use App\Customer;
use DB;
use Validator;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\SearchResource;

class TransactionController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {
        //$lim = 5;
        /*
        $vars = [
            "customers" => CustomerResource::collection(Customer::all()),
            "transactions" => TransactionResource::collection(Transaction::paginate(5)),
            "search" => ""
        ];
        return $vars;
        */
            $transactions = Transaction::orderBy('updated_at', 'desc')->paginate(5);
            $t = TransactionResource::collection($transactions);
        return $t;  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $transaction = $request->isMethod('put') ? Transaction::findOrFail($request->transaction_id) : new Transaction;

        $transaction->customerId = $request->input('customer_id');
        $transaction->amount = $request->input('amount');
        $transaction->created = today();
        $transaction->created_at = now();
        $transaction->updated_at = now();

        if($transaction->save()){
            return new TransactionResource($transaction);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaction = Transaction::findOrFail($id);
        return new TransactionResource($transaction);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        $transaction->amount = $request->input('amount');
        $transaction->updated_at = now();

        if($transaction->save()){
            return new TransactionResource($transaction);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        if($transaction->delete()){
            return new TransactionResource($transaction);
        }
    }

    /**
     * 
     * @param  $val
     * @return \Illuminate\Http\Response
     */
    public function search($val)
    {
        if(is_numeric($val)){
            $filtered = Transaction::where('amount', '>=', $val)
            ->paginate(5);
           return TransactionResource::collection($filtered);
        }
        else{
            $filtered = DB::table('transactions')
            ->join('customers', 'transactions.customerId', '=', 'customers.id')
            ->select('transactions.id', 'customerId','customers.customername as customer', 'amount','transactions.updated_at')
            ->where('customers.customername', 'like', '%'.$val.'%')
            ->paginate(5);
            return SearchResource::collection($filtered);
        }
        
    }

}
