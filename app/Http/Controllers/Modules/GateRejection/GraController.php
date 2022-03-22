<?php /** @noinspection LaravelFunctionsInspection */

namespace App\Http\Controllers\Modules\GateRejection;

use App\Http\Controllers\Api\MMOPL\ApiController;
use App\Http\Controllers\Api\PhonePe\PhonePePaymentController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Modules\Utility\OrderUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GraController extends Controller
{
    public function info($slave_id, $station_id)
    {
        $api = new ApiController();
        $response = $api -> graInfo($slave_id, $station_id);

        return $response->status == 'OK' ? response([
            'status' => true,
            'data' => $response->data
        ]) : response([
            'status' => false,
            'error' => $response->error
        ]);
    }

    public function apply(Request $request)
    {
        $graInfo = json_decode(
            json_encode(
                $request -> input('penaltyInfo')
            )
        );

        $penaltyAmount = 0;

        foreach ($graInfo -> penalties as $penalty)
        {
            $penaltyAmount += $penalty -> amount;
        }

        foreach ($graInfo -> overTravelCharges as $penalty)
        {
            $penaltyAmount += $penalty -> amount;
        }

        $saleOrderNumber = OrderUtility::genSaleOrderNumber($graInfo -> tokenType);

        DB::table('sale_order')->insert([
            'sale_or_no'        => $saleOrderNumber,
            'txn_date'          => Carbon::now(),
            'pax_id'            => Auth::id(),
            'ms_qr_no'          => $graInfo -> masterTxnId,
            'src_stn_id'        => $graInfo -> source ?? 1,
            'des_stn_id'        => $request -> input('station_id'),
            'unit'              => 1,
            'unit_price'        => $penaltyAmount,
            'total_price'       => $penaltyAmount,
            'media_type_id'     => env('MEDIA_TYPE_ID_MOBILE'),
            'product_id'        => $graInfo -> qrType,
            'op_type_id'        => env('ORDER_GRA'),
            'pass_id'           => $graInfo -> tokenType,
            'pg_id'             => env('PHONE_PE_PG'),
            'sale_or_status'    => env('ORDER_GRA'),
            'ref_sl_qr'         => $graInfo -> refTxnId
        ]);

        $order = DB::table('sale_order as so')
            ->join('stations as s', 's.stn_id', '=', 'so.src_stn_id')
            ->join('stations as d', 'd.stn_id', '=', 'so.des_stn_id')
            ->where('sale_or_no', '=', $saleOrderNumber)
            ->select(['so.*', 's.stn_name as source_name', 'd.stn_name as destination_name'])
            ->first();

        $api = new PhonePePaymentController();
        $response = $api->pay($order);

        return $response->success
            ? response([
                'status' => true,
                'redirectUrl' => $response->data->redirectUrl,
                'order_id' => $saleOrderNumber
            ])
            : response([
                'status' => false,
                'error' => $response,
                'order_id' => $saleOrderNumber
            ]);

    }

}
