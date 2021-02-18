<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facturacion;
use App\DetalleFacturacion;
use App\Articulo;
use App\Cajas;
use App\CierresXCaja;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InformeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id_empresa = $request->session()->get('id_empresa');

        $numFacturaFiltro = $request->numFacturaFiltro;
        $estadoFiltro = $request->estadoFiltro;
        $idTerceroFiltro = $request->idTerceroFiltro;
        $ordenFiltro = $request->ordenFiltro;
        $desdeFiltro = $request->desdeFiltro;
        $hastaFiltro = $request->hastaFiltro;
        $idVendedorFiltro = $request->idVendedorFiltro;
        
        if(isset($request->filtroInformes))
        {
            
            $n = '';
            $facturacion = Facturacion::join('personas', 'facturacion.id_tercero','=','personas.id')
            ->join('zona', 'facturacion.lugar','=','zona.id')
            ->join('users', 'facturacion.id_usuario','=','users.id');
            if(isset($request->idCajaInformes) && $request->idCajaInformes!='' && $request->idCajaInformes!=0){
            $facturacion = $facturacion->join('cajas_cierres','facturacion.id_cierre_caja','=','cajas_cierres.id');}

            $facturacion = $facturacion->select('facturacion.id','facturacion.num_factura','facturacion.id_tercero','personas.nombre as nom_tercero','facturacion.id_usuario','facturacion.fec_crea','facturacion.fec_edita','facturacion.usu_edita','facturacion.subtotal','facturacion.valor_iva','facturacion.total','abono','facturacion.saldo','facturacion.detalle','facturacion.lugar','zona.zona as nom_lugar','facturacion.descuento','facturacion.fec_registra','facturacion.fec_envia','facturacion.fec_anula','facturacion.usu_registra','facturacion.usu_envia','facturacion.usu_anula','facturacion.fecha','facturacion.id_tarifario','facturacion.estado','personas.nombre1','personas.nombre2','personas.apellido1','personas.apellido2','users.idrol');
            
            if(isset($request->idCajaInformes) && $request->idCajaInformes!='' && $request->idCajaInformes!=0){$facturacion = $facturacion->where('cajas_cierres.id_caja','=',$request->idCajaInformes);}
            if(isset($request->idCajeroInformes) && $request->idCajeroInformes!='' && $request->idCajeroInformes!=0){$facturacion = $facturacion->where('facturacion.id_usuario','=',$request->idCajeroInformes);}
            if(isset($request->saldoInformes) && $request->saldoInformes!=''){
                if($request->saldoInformes == 'Saldo'){
                    $facturacion = $facturacion->where('facturacion.saldo','>',0);
                }else if($request->saldoInformes == 'Sin Saldo'){
                    $facturacion = $facturacion->where('facturacion.saldo','=',0);
                }
            }
            if(isset($request->estadoInformes) && $request->estadoInformes!=''){
                if($request->estadoInformes == '1'){
                    $facturacion = $facturacion->where('facturacion.estado','=',1);
                }else if($request->estadoInformes == '2'){
                    $facturacion = $facturacion->where('facturacion.estado','=',2);
                }else if($request->estadoInformes == '3'){
                    $facturacion = $facturacion->where('facturacion.estado','=',3);
                }else if($request->estadoInformes == '4'){
                    $facturacion = $facturacion->where('facturacion.estado','=',4);
                }
            }

            $facturacion = $facturacion->where('facturacion.id_empresa','=',$id_empresa)
            ->get();

            return ['facturacion'=>$facturacion,];
        }
        else
        {
            
            $facturacion = Facturacion::join('personas', 'facturacion.id_tercero','=','personas.id')
            ->join('zona', 'facturacion.lugar','=','zona.id')->join('users', 'facturacion.id_usuario','=','users.id')
            ->select('facturacion.id','facturacion.num_factura','facturacion.id_tercero','personas.nombre as nom_tercero','facturacion.id_usuario','facturacion.fec_crea','facturacion.fec_edita','facturacion.usu_edita','facturacion.subtotal','facturacion.valor_iva','facturacion.total','abono','facturacion.saldo','facturacion.detalle','facturacion.lugar','zona.zona as nom_lugar','facturacion.descuento','facturacion.fec_registra','facturacion.fec_envia','facturacion.fec_anula','facturacion.usu_registra','facturacion.usu_envia','facturacion.usu_anula','facturacion.fecha','facturacion.id_tarifario','facturacion.estado','personas.nombre1','personas.nombre2','personas.apellido1','personas.apellido2','users.idrol');
            
            if($numFacturaFiltro!='' && $numFacturaFiltro!='0')
            {
                $facturacion = $facturacion->where('facturacion.num_factura','=',$numFacturaFiltro);
            }
            if($estadoFiltro!='' && $estadoFiltro!='0')
            {
                $facturacion = $facturacion->Where('facturacion.estado','=',$estadoFiltro);
            }
            if($idTerceroFiltro!='' && $idTerceroFiltro!='0')
            {
                $facturacion = $facturacion->Where('facturacion.id_tercero','=',$idTerceroFiltro);
            }
            if($idVendedorFiltro!='' && $idVendedorFiltro!='0')
            {
                $facturacion = $facturacion->Where('facturacion.id_usuario','=',$idVendedorFiltro);
            }
            if($desdeFiltro!='' && $hastaFiltro!='')
            {
                $facturacion = $facturacion->whereBetween('facturacion.fecha', [$desdeFiltro, $hastaFiltro]);
            }
            if($ordenFiltro!='')
            {
                $orden='';
                if($ordenFiltro=='num_factura')
                    $orden='desc';
                else
                    $orden='asc';
                $facturacion = $facturacion->orderBy($ordenFiltro,$orden);
            }
            else
            {
                $facturacion = $facturacion->orderBy('id', 'desc');
            }
            if($request->id_cierre_caja && $request->id_cierre_caja!=0){$facturacion = $facturacion->where('id_cierre_caja','=',$request->id_cierre_caja);}

            $facturacion = $facturacion->where('facturacion.id_empresa','=',$id_empresa)
            ->paginate(6);

            return [
                'pagination' => [
                    'total'        => $facturacion->total(),
                    'current_page' => $facturacion->currentPage(),
                    'per_page'     => $facturacion->perPage(),
                    'last_page'    => $facturacion->lastPage(),
                    'from'         => $facturacion->firstItem(),
                    'to'           => $facturacion->lastItem(),
                ],               
                'facturacion' => $facturacion,
                'idrol' => Auth::user()->idrol,
            ];
        }
    }

    public function productos(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id_empresa = $request->session()->get('id_empresa');

        $detalles_facturacion = DetalleFacturacion::select('articulos.nombre as articulo', 'categorias.nombre as categoria', 'articulos.idcategoria2', 'articulos.id as id_prod', 'facturacion.id as id_fac', 'detalle_facturacion.id as id', 'detalle_facturacion.valor_venta', 'detalle_facturacion.cantidad','detalle_facturacion.valor_final', 'facturacion.fec_crea')
        ->join('facturacion', 'detalle_facturacion.id_factura', '=', 'facturacion.id')
        ->join('articulos', 'detalle_facturacion.id_producto', '=', 'articulos.id')
        ->join('categorias', 'articulos.idcategoria2', '=', 'categorias.id')
        ->get();

        foreach($detalles_facturacion as $dt){
            // $dt
        }

        return [
            'detalles_facturacion' => $detalles_facturacion,
        ];

    }

    public function cajas(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id_empresa = $request->session()->get('id_empresa');

        $cajas_cierres = CierresXCaja::select('cajas_cierres.id as id', 'cajas_cierres.id_caja', 'cajas_cierres.vr_inicial', 'cajas_cierres.vr_gastos', 'cajas_cierres.vr_final', 'cajas.nombre as nombre_caja', 'cajas.id as idcaja', 'cajas_cierres.updated_at as fecha_cierre')
        ->join('cajas','cajas_cierres.id_caja','cajas.id')
        ->where('cajas_cierres.id_empresa',1)
        ->get();

        return [
            'cajas_cierres' => $cajas_cierres,
        ];

    }

}
