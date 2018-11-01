<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id'        =>  $this->id,
            'customerId'=>  $this->customerId,
            'customer'  =>  $this->customer->customername,
            'amount'    =>  $this->amount,
            'updated_at' => $this->updated_at.date("")
        ];
    }

/*
    public function with($request)
    {
        return [
            'version'  =>  '1.0.0',
            'author_url'  =>  url('http://leviserg.zzz.com.ua'),
            'target'    => 'WinStarsTech LLC'
        ];
    }
*/
}
