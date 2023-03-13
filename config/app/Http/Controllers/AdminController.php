<?php

namespace App\Http\Controllers;

use App\ClientTable;
use App\Depo;
use App\Orderciment;
use App\Sellesdata;
use App\User;
use App\Cell;
use App\Province;
use App\District;
use App\payment;
use App\payment_method;
use App\Sector;
use App\Team;
use App\ubudehe;
use App\Village;
use Carbon\Carbon;
use \PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    public function dashboard(){
        $user = Auth::user()->user_type;
        if ($user =='Superadmin'){
            return view('admin.dashboard');
        }
        elseif($user =='admin'){
            return view('admin.dashboard');
        } elseif($user =='karani'){
            return view('admin.dashboard');
        }else{
            return redirect('home');
        }
    }
    public function Orderciment(){
        if (Auth::user()->user_type=="admin"||Auth::user()->user_type=="Superadmin") {
            $stocker=Depo::all();
            return view('admin.Orderciment',compact('stocker'));
        }else {
            return redirect('/dashboard');
        }

            }
    public function Edituser(){
                $userinthesystem=User::all();
                $user = Auth::user()->user_type;
                if ($user =='Superadmin'){
                    return view('admin.Edituser',compact('userinthesystem'));
                }
                elseif($user =='admin'){
                    return view('admin.Edituser',compact('userinthesystem'));
                }else{
                    return redirect('/dashboard');
                }

            }
    public function Edituserbyid(Request $request,$id){
        $user = Auth::user()->user_type;
        $userid=Auth::user()->id;
        $stocker=Auth::user()->stocker;
        if ($userid !=$id) {

            if ($user =='Superadmin'){
                $users = User::where('id',$id )->get();


                $stocker = Depo::all();
                return view('admin.Edituserbyid',compact('users'));
            }
            elseif($user =='admin'){
                $users = User::where('id',$id )->get();

                return view('admin.Edituserbyid',compact('users'));
            }else{
                Session::flash('message', "User Updated Successfull ");
                return redirect('/dashboard');
            }
        }else{
            Session::flash('message', "Please you can't edit you self");
            return redirect('/Edituser');
        }


    }
    public function ChangeStutas(Request $request){
        $id=$request->get('id');
        $privilage=$request->get('privilage');

        $updat=DB::table('users')->where('id', $id) ->update(['user_type' =>$privilage]);
        Session::flash('message', "User Updated Successfull ");
        return redirect('/Edituser');

    }

public function newclient()
{
    $team=Team::all();
    $ubudehe=DB::table('ubudehe')->get();

    $province=Province::all();
    return view('admin.newform.newclient',compact('province','team','ubudehe'));
}

public function storenewclientpost(Request $request)
{
$newclient = new ClientTable();
$nameofuser=Auth::user()->name;
$newclient->fname= $request->cfname;
$newclient->lname= $request->clname;
$newclient->phone_number= $request->phone;
$newclient->client_card= $request->client_card;
$newclient->village_id= $request->village;
$newclient->house_number= $request->hsnumer;
$newclient->ubudehe_id= $request->ubudehe;
$newclient->team_id= $request->team_id;
$newclient->save();
Session::flash('message', 'succes full');
return redirect()->back();

}


    public function storenewclient(Request $request){
         $orderciment =new Orderciment();
                $nameofuser=Auth::user()->name;//this will help use just to be displayed on the schreen
                $orderciment->Depo_id=$stocker=$request->get('Depoid');
                        $stockerf = Depo::where('id',$stocker )->get();
                        foreach ($stockerf as $key => $value) {
                            $stockername=$value->name;
                        }
                $orderciment->sacker= $userid=Auth::user()->id;//uwabyohereje kuri depo bivuye kurangurwa
                $sacker=$request->get('sacker');//imifuka yohereje
                $orderciment->sacker= $sacker;
                $Untprice=$request->get('Untprice');//ikiguzi cy umufuka umwe
                $orderciment->pricepersack=$Untprice;
                $orderciment->accepted="0";//kubera bitarakirwa ni zero
                $total=$sacker*$Untprice;
                $date=date("Y/m/d");
                $orderciment->totalprice=$total;
                $orderciment->save();

                $data = ['title' => "Invoive",'date'=>$date,'nameofuser'=>$nameofuser,'Untprice'=>$Untprice,'sacker'=>"$sacker",'total'=>"$total",'stockername'=>$stockername];
                $pdf = PDF::loadView('myPDF', $data);
                Session::flash('message', 'succes full');
        return $pdf->stream("report$date.pdf");

    }
    public function reportnewclient(){
        $Allclient=ClientTable::all();

        return view('admin.Allclient',compact('Allclient'));
    }



    public function acceptypayment($id){

        $Client=ClientTable::find($id);

        $ubudehe=DB::table('ubudehe')->where('id',$Client->ubudehe_id)->orderBy('id', 'DESC')->first();

        $paymentmetode=payment_method::all();
        return view('admin.newform.requestclienttopay',compact('Client','paymentmetode','ubudehe'));
    }

    // post payment of the customer
    public function paymentclient(Request $request)
    {
        $postpaynment= new payment();

        $postpaynment->Payment_method_id=$request->paymentmethod;
        $postpaynment->Amaunt=$request->hsnumer;
        $postpaynment->User_id=Auth::user()->id;
        $postpaynment->client_id=$request->id;
        $postpaynment->Message="payment";
        $postpaynment->save();
        Session::flash('message', "Payment Method  ");
        return redirect()->back();
    }
    public function Checknewstocker(){
        if (Auth::user()->user_type =="admin" ||Auth::user()->user_type =="admin") {
            $ordercemtent=Orderciment::all();
            return view('admin.stockerorderd',compact('userinthesystem'));
        }
        else {
            $ordercemtent=Orderciment::all();
            return view('admin.stockerorderd',compact('userinthesystem'));
        }

    }
    public function ACCEPTCIMENT(){
        $orderofcimnet=Orderciment::all();
      return view('admin.Acceptciment',compact('orderofcimnet'));
    }



    public function paymentkarani(){
        $karanpaymentreport = DB::table('payments')
->select('users.name AS usename','client_tables.fname AS clfiname','client_tables.lname AS clliname','payment_methods.Name AS Pmth','payments.Amaunt AS Amt')

->join('users', 'payments.User_id', '=', 'users.id')
->join('client_tables', 'payments.client_id', '=', 'client_tables.id')
->join('payment_methods', 'payments.Payment_method_id', '=', 'payment_methods.id')

->get();


    return view('admin.Allpayedclient' ,compact('karanpaymentreport'));
    }
    public function sellcimenttocustomer(Request $request){
        // $carbon = new Carbon();
        // $yesterday = Carbon::yesterday();
        // $startDate = Carbon::now()->endOfMonth();
        // $lastSunday = $startDate()->endOfWeek();
        // dd($yesterday);
        $selluser = new Sellesdata();

        $buynumberofzucker=$request->get('numberofzucker');
        $remainingstocker = DB::table('sellesdatas')->where('depo_id',Auth::user()->stocker)->orderBy('id', 'DESC')->first();
        if ($remainingstocker->Totalnumberofsuck < $buynumberofzucker) {
            Session::flash('message', "Hello you  you don't have anough stocker ");
            return redirect()->back();
        }else{
            $selluser->Company=$request->get('name');
            $selluser->Whobuyphone=$request->get('Phone');
            $selluser->Numberofsuckbuy=$request->get('numberofzucker');
            $selluser->Totalnumberofsuck=$remainingstocker->Totalnumberofsuck-$buynumberofzucker;
            $selluser->depo_id=Auth::user()->stocker;
            $selluser->solled_by=Auth::user()->id;
            $selluser->save();
            Session::flash('message', "hello ciment have been sold successfull");
            return redirect()->back();
        }

    }
    public function gateallcustomer(){
        $remainingstocker = DB::table('client_tables')->orderBy('id', 'DESC')->get();


        return view('admin.Allclient',compact('remainingstocker'));

    }
}
