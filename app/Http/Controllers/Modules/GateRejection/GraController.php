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
      $data=  json_decode($request->getContent());



        $penaltyAmount = 0;

        foreach ($data->data->penalties as $penalty)
        {
            $penaltyAmount += $penalty -> amount;
        }

        foreach ($data->data->overTravelCharges as $penalty)
        {
            $penaltyAmount += $penalty -> amount;
        }

        $saleOrderNumber = OrderUtility::genSaleOrderNumber($data->data->tokenType, $data->pax_mobile);

        $pax_details = DB::table('users')
                ->where('pax_mobile','=',$data->pax_mobile)
                ->first();

        DB::table('sale_order')->insert([
            'sale_or_no'        => $saleOrderNumber,
            'txn_date'          => Carbon::now(),
            'pax_id'            => $pax_details->pax_id,
            'ms_qr_no'          => $request->input('masterTxnId'),
            'src_stn_id'        => $request->input('source') ?? 1,
            'des_stn_id'        => $request -> input('station_id'),
            'unit'              => 1,
            'unit_price'        => $penaltyAmount,
            'total_price'       => $penaltyAmount,
            'media_type_id'     => env('MEDIA_TYPE_ID_MOBILE'),
            'product_id'        => $data->data->qrType,
            'op_type_id'        => env('ORDER_GRA'),
            'pass_id'           => $data->data->tokenType,
            'pg_id'             => env('PHONE_PE_PG'),
            'sale_or_status'    => env('ORDER_GRA'),
            'ref_sl_qr'         => $request->input('refTxnId')
        ]);

        return response([
            'status' => true,
            'order_id' => $saleOrderNumber
        ]);

    }

}
