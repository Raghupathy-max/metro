<?php /** @noinspection LaravelFunctionsInspection */

namespace App\Http\Controllers\Modules\StoreValue;

use App\Http\Controllers\Api\MMOPL\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Modules\Utility\OrderUtility;
use App\Models\SaleOrder;
use App\Models\SvSlBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreValueOrderController extends Controller
{

    public function create(Request $request)
    {
        $request->validate([
            'price' => 'required|integer|min:100|max:3000|multiple_of:100',
            'pax_mobile' => 'required'
        ]);

        $saleOrderNumber = OrderUtility::genSaleOrderNumber($request->input('pass_id'), $request ->input('pax_mobile'));
        SaleOrder::storeSv($request, $saleOrderNumber);

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

        if ($response->status == "OK") SvSlBooking::store($order, $response);

        return response([
            'status' => true,
            'message' => 'Trip created successfully'
        ]);
    }

    public function canIssuePass($pax_mobile)
    {

        $api = new ApiController();
        $response = $api -> canIssuePass(
            env('PRODUCT_SV'),
            env('PASS_SV'),
            $pax_mobile
        );

        if ($response -> status == 'OK')
        {
            return response([
                'status' => true,
                'message' => 'Pass can be issued !'
            ]);
        }
        else
        {
            return response([
                'status' => false,
                'error' => $response
            ]);
        }

    }

}
