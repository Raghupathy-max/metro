<?php /** @noinspection LaravelFunctionsInspection */

namespace App\Http\Controllers\Modules\StoreValue;

use App\Http\Controllers\Api\MMOPL\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Modules\Utility\OrderUtility;
use App\Models\SaleOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreValueReloadController extends Controller
{
    public function status($order_id)
    {
        $order = DB::table('sale_order as so')
            ->where('so.sale_or_status', '=', env('ORDER_TICKET_GENERATED'))
            ->where('so.sale_or_no', '=', $order_id)
            ->first();

        $api = new ApiController();
        $response = $api->reloadStoreValueStatus($order);

        if ($response->status == 'OK') {

            return response([
                'status' => true,
                'message' => 'Pass can be reloaded.'
            ]);

        }

        return response([
            'status' => false,
            'error' => $response->error
        ]);

    }

    public function reload(Request $request)
    {

        $old_order = DB::table('sale_order')
            ->join('users','users.pax_id','=','sale_order.pax_id')
            ->where('sale_or_no', '=', $request->input('order_id'))
            ->first();

        $SaleOrderNumber = OrderUtility::genSaleOrderNumber($old_order->pass_id, $old_order->pax_mobile);

        SaleOrder::reload(
            $old_order,
            $request->input('reloadAmount'),
            $SaleOrderNumber
        );

        return response([
            'status' => true,
            'order_id' => $SaleOrderNumber
        ]);

    }

}
