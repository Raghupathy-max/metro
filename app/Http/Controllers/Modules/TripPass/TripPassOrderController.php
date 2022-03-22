<?php

namespace App\Http\Controllers\Modules\TripPass;

use App\Http\Controllers\Api\MMOPL\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Modules\Utility\OrderUtility;
use App\Models\SaleOrder;
use App\Models\TpSlBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TripPassOrderController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'source_id' => 'required|integer|min:1|max:12',
            'destination_id' => 'required|integer|min:1|max:12',
            'fare' => 'required',
            'pax_mobile' => 'required'
        ]);

        $saleOrderNumber = OrderUtility::genSaleOrderNumber(
            env('PASS_TP'),
            $request->input('pax_mobile')
        );

        $saleOrder = new SaleOrder();
        $saleOrder->storeTp($request, $saleOrderNumber);

        return response([
            'status' => true,
            'order_id' => $saleOrderNumber
        ]);

    }

    public function issueTrip($order_id)
    {
        $order = DB::table('sale_order')
            ->where('sale_or_no', '=', $order_id)
            ->first();

        $api = new ApiController();
        $response = $api->genTrip($order);

        if (!is_null($response)) {

            if ($response->status == "OK") {

                $tpSl = new TpSlBooking();
                $tpSl->store($order, $response);

            }

        }

        return response([
            'status' => true,
            'message' => 'Trips created successfully'
        ]);

    }

    public function canIssuePass($pax_mobile)
    {
        $api = new ApiController();
        $response = $api->canIssuePassTP(
            env('PRODUCT_TP'),
            env('PASS_TP'),
            $pax_mobile
        );

        if ($response->status == 'OK') {
            return response([
                'status' => true,
                'message' => 'Pass can be issued !'
            ]);
        } else {
            return response([
                'status' => false,
                'error' => $response->error
            ]);
        }

    }

}
