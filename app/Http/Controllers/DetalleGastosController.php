<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\CierresXCaja;
use App\Facturacion;
use App\DetalleGasto;
use Illuminate\Support\Facades\Auth;

class DetalleGastosController extends Controller
{
    public function index(Request $request){
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $id_empresa = $request->session()->get('id_empresa');
        
        $detalle_gastos = DetalleGasto::select('detalles_gastos.id as id','detalles_gastos.id_caja_cierre', 'valor_gasto', 'detalle_gasto', 'cajas.nombre as nombre_caja')->where('detalle_gasto', 'like', '%'. $buscar . '%')
        ->join('cajas_cierres', 'cajas_cierres.id', 'detalles_gastos.id_caja_cierre')
        ->join('cajas', 'cajas.id', 'cajas_cierres.id_caja')
        ->where('cajas_cierres.estado', '1');
        if($buscar!=''){$detalle_gastos = $detalle_gastos->where('cajas_cierres.estado', '1');}
        $detalle_gastos = $detalle_gastos->orderBy('id', 'desc')->paginate(10);        

        return [
            'pagination' => [
                'total'        => $detalle_gastos->total(),
                'current_page' => $detalle_gastos->currentPage(),
                'per_page'     => $detalle_gastos->perPage(),
                'last_page'    => $detalle_gastos->lastPage(),
                'from'         => $detalle_gastos->firstItem(),
                'to'           => $detalle_gastos->lastItem(),
            ],
            'detalle_gastos' => $detalle_gastos
        ];

    }
    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $detalle_gasto = new DetalleGasto();
        $detalle_gasto->detalle_gasto = $request->detalle_gasto;
        $detalle_gasto->valor_gasto = $request->valor_gasto;
        $detalle_gasto->id_caja_cierre = $request->id_caja_cierre;
        $detalle_gasto->save();

        $cierrexcaja =CierresXCaja::where('cajas_cierres.id', '=',$request->id_caja_cierre)->first();
        $cierrexcaja->vr_gastos = $cierrexcaja->vr_gastos +  $request->valor_gasto;
        $cierrexcaja->save();
    }
    
    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $valor_gasto= DetalleGasto::where('detalles_gastos.id', '=',$request->id )->first();
        $cierrexcaja =CierresXCaja::where('cajas_cierres.id', '=', $request->id_caja_cierre)->first();
        $cierrexcaja->vr_gastos = $cierrexcaja->vr_gastos-$valor_gasto->valor_gastos;
        $cierrexcaja->save();
        

        $detalle_gasto = DetalleGasto::findOrFail($request->id);
        $detalle_gasto->detalle_gasto = $request->detalle_gasto;
        $detalle_gasto->valor_gasto = $request->valor_gasto;
        $detalle_gasto->id_caja_cierre = $request->id_caja_cierre;
        $detalle_gasto->save();


        $cierre_caja =CierresXCaja::where( 'cajas_cierres.id','=',$request->id_caja_cierre)->first();
        $cierre_caja->vr_gastos = $cierre_caja->vr_gastos+$request->valor_gasto;
        $cierre_caja->save();
    }

    public function seleccionarCajaAbierta(Request $request){
        if (!$request->ajax()) return redirect('/');
        $id_empresa = $request->session()->get('id_empresa');
        $cierres_caja = CierresXCaja::select('cajas_cierres.id as id_cierre_caja', 'cajas.nombre', 'cajas.id as idCaja',)->where('cajas_cierres.estado','=','1')->orderBy('cajas_cierres.id','desc')
        ->join('cajas', 'cajas.id', 'id_caja')
        ->get();

        return ['cierres_caja' => $cierres_caja];
    }
}
