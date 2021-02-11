<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notification;
use Auth;
use App\User;
use App\Notifications\NotifyAdmin;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class NotificationController extends Controller
{
    //
    public function get(){
        // return Notification::all();
        $unreadNotifications = Auth::user()->unreadNotifications;
        $fechaActual = date('Y-m-d');
        foreach($unreadNotifications as $notification){
            if($fechaActual != $notification->created_at->toDateString()){
                $notification->markAsRead();
            }
        }
        return Auth::user()->unreadNotifications;
    }
    public function guardarNotificacion(Request $request){

        $detalles = $request->data;//Array de detalles
    
        $id_producto=0;
        foreach($detalles as $ep=>$det)
        {
            if(isset($det['id_asociado']) && $det['id_asociado']!=''){
                $id_producto = $det['id_asociado'];
            }
            else{
                $id_producto = $det['idarticulo'];
            }          

            $fechaActual = date('Y-m-d');
            $numVentas = DB::table('detalle_facturacion')->whereDate('created_at',$fechaActual)->count();

            $arregloDatos=[               
                'numero' => $numVentas,                
                'cantidad' => $det['cantidad'],
                'id_producto' => $id_producto,                
                'estado' => 'Preparando'
            ];

            $allUsers = User::where('idrol', 2)->orWhere('idrol', 1)->get();

            foreach ($allUsers as $notificar){
                User::findOrFail($notificar->id)->notify(new NotifyAdmin($arregloDatos));
            }
           
        }
    }

    public function eliminarNotificacion(Request $request){
        // Notification::destroy($request->id);
        // return 'eliminado';

        $user = Auth::user()->unreadNotifications;
        foreach ($user as $notification) {
            $notification->markAsRead();
        }

        // foreach ($user->unreadNotifications as $notification) {
        //     $notification->markAsRead();
        // }
    }
}
