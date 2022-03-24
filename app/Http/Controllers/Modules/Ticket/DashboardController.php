<?php /** @noinspection LaravelFunctionsInspection */

namespace App\Http\Controllers\Modules\Ticket;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index($id)
    {
        $upcomingOrders = $this->getUpcomingOrders($id);
        $recentOrders = $this->getRecentOrders($id);

       return response([
            'status' => true,
            'user' => Auth::user(),
            'upcomingOrders' => $upcomingOrders,
            'recentOrders' => $recentOrders
        ]);
    }

    private function getUpcomingOrders($id)
    {
        return DB::table('sale_order as so')
            ->join('stations as s', 's.stn_id', 'so.src_stn_id')
            ->join('stations as d', 'd.stn_id', 'so.des_stn_id')
            ->where('so.pax_id', '=', $id)
            ->where('so.sale_or_status', '=', env('ORDER_TICKET_GENERATED'))
            ->where(function($query) {
                $query->where('product_id', '=', env('PRODUCT_SJT'))
                    ->orWhere('product_id', '=', env('PRODUCT_RJT'));
            })
            ->select(['so.*', 's.stn_name as source', 'd.stn_name as destination'])
            ->get();

    }

    private function getRecentOrders($id)
    {
        return DB::table('sale_order as so')
            ->join('stations as s', 's.stn_id', 'so.src_stn_id')
            ->join('stations as d', 'd.stn_id', 'so.des_stn_id')
            ->where('so.pax_id', '=', $id)
            ->where('so.sale_or_status', '=', env('ORDER_COMPLETED'))
            ->where(function($query) {
                $query->where('product_id', '=', env('PRODUCT_SJT'))
                      ->orWhere('product_id', '=', env('PRODUCT_RJT'));
            })
            ->select(['so.*', 's.stn_name as source', 'd.stn_name as destination'])
            ->limit(1)
            ->get();
    }
}
