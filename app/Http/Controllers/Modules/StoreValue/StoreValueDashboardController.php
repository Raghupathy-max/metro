<?php /** @noinspection ALL */

namespace App\Http\Controllers\Modules\StoreValue;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StoreValueDashboardController extends Controller
{
    public function index($id)
    {
        $pass = DB::table('sale_order')
            ->where('pax_id', '=', $id)
            ->where('sale_or_status', '=', env('ORDER_TICKET_GENERATED'))
            ->where('product_id', '=', env('PRODUCT_SV'))
            ->orderBy('txn_date', 'desc')
            ->first();

        if (is_null($pass)) return redirect()->route('sv.order');

        $trip = DB::table('sv_sl_booking')
            ->where('sale_or_id', '=', $pass->sale_or_id)
            ->where('qr_status', '!=', env('COMPLETED'))
            ->where('qr_status', '!=', env('EXPIRED'))
            ->first();
        $pax_details = DB::table('users')
            ->where('pax_id','=',$id)
            ->first();

        return response([
            'user' => $pax_details,
            'pass' => $pass,
            'trip' => $trip,
            'stations' => DB::table('stations')->get(['stn_id', 'stn_name'])
        ]);
    }
}
