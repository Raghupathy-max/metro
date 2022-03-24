<?php

namespace App\Http\Controllers\Modules\Station;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StationController extends Controller
{
    public function  getStation(){
        $station = DB::table('stations')
            ->get();
        return response([
           'stations' => $station
        ]);
    }
}
