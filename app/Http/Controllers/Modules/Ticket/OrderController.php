<?php

namespace App\Http\Controllers\Modules\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Modules\Utility\OrderUtility;
use App\Models\SaleOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'source_id' => ['required'],
            'destination_id' => ['required'],
            'pass_id' => ['required'],
            'quantity' => ['required'],
            'fare' => ['required'],
            'pax_mobile'=>['required']
        ]);

        $saleOrderNumber = OrderUtility::genSaleOrderNumber(
            $request->input('pass_id'),
            $request->input('pax_mobile')

        );

        $saleOrder = new SaleOrder();
        $saleOrder->store($request, $saleOrderNumber);

        return response([
            'status' => true,
            'order_id' => $saleOrderNumber
        ]);

    }
}
