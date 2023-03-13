<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cell;
use App\District;

use App\Hall;

use App\Sector;

use App\Village;

class JqueryController extends Controller

{
    public function Districtjquery(Request $request){
        $value=$request->get('provinces');
        $district = District::where('province',$value )->get();
            return response()->json($district);
    }

    public function paruwasi(Request $request){
        $value=$request->get('provinces');
        $district = Hall::where('parishe',$value )->get();
            return response()->json($district);
    }

    public function Sectorjquery(Request $request){
        $value=$request->get('value');
        $sector = Sector::where('District',$value )->get();
            return response()->json($sector);
    }

    public function Celljquery(Request $request){
        $value=$request->get('provinces');
        $Cell = Cell::where('Sector',$value )->get();
            return response()->json($Cell);
    }
     public function villagejquery(Request $request){
        $value=$request->get('value');
        $village = Village::where('Cell',$value )->get();
            return response()->json($village);
    }

    public function qrcode(){
        return view('qrcode');
    }
}
