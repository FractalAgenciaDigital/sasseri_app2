<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facturacion;
use App\DetalleFacturacion;
use App\Articulo;
use App\Cajas;
use App\User;
use App\CierresXCaja;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Impresora;
use App\ConfigGenerales;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

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
        $desdeFiltro = $request->desdeFiltro;
        $hastaFiltro = $request->hastaFiltro;
        $noProductoFiltro = $request->noProductoFiltro;
        $noCategoriaFiltro = $request->noCategoriaFiltro;
        

        $detalles_facturacion = DetalleFacturacion::select('articulos.nombre as articulo', 'categorias.nombre as categoria', 'articulos.idcategoria2', 'articulos.id as id_prod', 'facturacion.id as id_fac', 'detalle_facturacion.id as id','articulos.precio_venta as valor_venta', 'facturacion.fec_crea','facturacion.fecha', DB::raw('SUM(detalle_facturacion.cantidad) as cantidad'), DB::raw('SUM(detalle_facturacion.valor_final) as valor_final') )
        ->groupBy('articulos.id')
        ->groupBy('facturacion.fecha')
        ->join('facturacion', 'detalle_facturacion.id_factura', '=', 'facturacion.id')
        ->join('articulos', 'detalle_facturacion.id_producto', '=', 'articulos.id')
        ->join('categorias', 'articulos.idcategoria2', '=', 'categorias.id');
        

        if(isset($request)){
            

            if($noProductoFiltro!=''){
                $detalles_facturacion = $detalles_facturacion
                ->where('articulos.nombre', 'like', '%'. $noProductoFiltro . '%');
            }
           
            if($noCategoriaFiltro!=''){
                $detalles_facturacion = $detalles_facturacion
                ->where('categorias.nombre', 'like', '%'. $noCategoriaFiltro . '%');
            }
            if($desdeFiltro!='' && $desdeFiltro!=0)
            {
                $detalles_facturacion = $detalles_facturacion->whereDate('detalle_facturacion.updated_at', '>=' , $desdeFiltro);
            }
            if($hastaFiltro!='' && $hastaFiltro!=0)
            {
                $detalles_facturacion = $detalles_facturacion->whereDate('detalle_facturacion.updated_at','<=' , $hastaFiltro);
            }           

        };
        
        
        $detalles_facturacion = $detalles_facturacion->orderBy('detalle_facturacion.id', 'desc')->paginate(20);

    

        return [
            'pagination' => [
                'total'        => $detalles_facturacion->total(),
                'current_page' => $detalles_facturacion->currentPage(),
                'per_page'     => $detalles_facturacion->perPage(),
                'last_page'    => $detalles_facturacion->lastPage(),
                'from'         => $detalles_facturacion->firstItem(),
                'to'           => $detalles_facturacion->lastItem(),
            ], 
            'detalles_facturacion' => $detalles_facturacion,
        ];

    }

    public function cajas(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id_empresa = $request->session()->get('id_empresa');
        $idFiltro = $request->idFiltro;
        $noCajaFiltro = $request->noCajaFiltro;
        $desdeFiltro = $request->desdeFiltro;
        $hastaFiltro = $request->hastaFiltro;

        $cajas_cierres = CierresXCaja::select('cajas_cierres.id as id', 'cajas_cierres.id_caja', 'cajas_cierres.vr_inicial', 'cajas_cierres.vr_gastos', 'cajas_cierres.vr_final', 'cajas.nombre as nombre_caja', 'cajas.id as idcaja', 'cajas_cierres.updated_at as fecha_cierre')
        ->join('cajas','cajas_cierres.id_caja','cajas.id')
        ->where('cajas_cierres.id_empresa','=',$id_empresa)
        ->orderBy('id','desc');       
        

        if(isset($request)){
            if($idFiltro!='' && $idFiltro!='0'){
                $cajas_cierres = $cajas_cierres
                ->where('cajas_cierres.id','=',$idFiltro);                
            }

            if($noCajaFiltro!='' && $noCajaFiltro!='0')
                {
                    $cajas_cierres = $cajas_cierres->where('cajas_cierres.id','=',$noCajaFiltro);
                }

            if($desdeFiltro!='' && $desdeFiltro!=0)
            {
                $cajas_cierres = $cajas_cierres->whereDate('cajas_cierres.updated_at', '>=' , $desdeFiltro);
            }
            if($hastaFiltro!='' && $hastaFiltro!=0)
            {
                $cajas_cierres = $cajas_cierres->whereDate('cajas_cierres.updated_at','<=' , $hastaFiltro);
            }
        }
        $cajas_cierres = $cajas_cierres->where('cajas_cierres.id_empresa',$id_empresa)->paginate(20);

        foreach($cajas_cierres as $cc ){

            $facturacion = Facturacion::select()
            ->where('facturacion.id_cierre_caja','=',$cc->id)
            ->where('facturacion.estado','=',2)
            ->get();

            $total_ventas=0;
            $cont=0;

            foreach($facturacion as $fac){
                $total_ventas += $fac->total;
                $cont++;
            }

            
            $cc->total_ventas = $total_ventas;
            $cc->total_caja =$cc->total_ventas + $cc->vr_inicial;
            $cc->diferencia = $cc->vr_final-$cc->total_caja;
            $cc->no_facturas = $cont;

            if($cc->diferencia == 0){
                $cc->estado = 1; /*Correcto*/
            }
            else 
            {
                if($cc->diferencia < 0){
                    $cc->estado = 2;//falta
                } 
                elseif($cc->diferencia > 0){
                    $cc->estado = 3;//sobra
                }

            }

        }
        return [
            'pagination' => [
                'total'        => $cajas_cierres->total(),
                'current_page' => $cajas_cierres->currentPage(),
                'per_page'     => $cajas_cierres->perPage(),
                'last_page'    => $cajas_cierres->lastPage(),
                'from'         => $cajas_cierres->firstItem(),
                'to'           => $cajas_cierres->lastItem(),
            ], 
            'cajas_cierres' => $cajas_cierres,
        ];

    }
    public function imprimirTicketInformeCajas(Request $request)
    {

        // Project::chunk(200, function ($projects) {
        //     foreach ($projects as $project) {
        //         //Aquí escribimos lo que haremos con los datos (operar, modificar, etc)
        //     }
        // });
        if (!$request->ajax()) return redirect('/');

        // Datos para el ticket
        $id_empresa = $request->session()->get('id_empresa');
        $id_impresora = $request->id_impresora;
        $imprimir = Impresora::where('id', $id_impresora)->first();
        $infoEmpresa = ConfigGenerales::select()->where('id','=', $id_empresa)->first();

        // filtros
        $idFiltro = $request->idFiltro;
        $noCajaFiltro = $request->noCajaFiltro;
        $desdeFiltro = $request->desdeFiltro;
        $hastaFiltro = $request->hastaFiltro;
        

        $cajas_cierres = CierresXCaja::select('cajas_cierres.id as id', 'cajas_cierres.id_caja', 'cajas_cierres.vr_inicial', 'cajas_cierres.vr_gastos', 'cajas_cierres.vr_final', 'cajas.nombre as nombre_caja', 'cajas.id as idcaja', 'cajas_cierres.updated_at as fecha_cierre')
        ->join('cajas','cajas_cierres.id_caja','cajas.id');  

        if(isset($request)){
            if($idFiltro!='' && $idFiltro!='0'){
                $cajas_cierres = $cajas_cierres
                ->where('cajas_cierres.id','=',$idFiltro);                
            }
            if($noCajaFiltro!='' && $noCajaFiltro!='0'){
                $cajas_cierres = $cajas_cierres->where('cajas_cierres.id','=',$noCajaFiltro);
            }
            if($desdeFiltro!='' && $desdeFiltro!=0){
                $cajas_cierres = $cajas_cierres->whereDate('cajas_cierres.updated_at', '>=' , $desdeFiltro);
            }
            if($hastaFiltro!='' && $hastaFiltro!=0){
                $cajas_cierres = $cajas_cierres->whereDate('cajas_cierres.updated_at','<=' , $hastaFiltro);
            }
        }
        $cajas_cierres = $cajas_cierres->where('cajas_cierres.id_empresa',$id_empresa)->get();

        // Inicio de impresion
        $infoEmpresa = ConfigGenerales::select()->where('id','=', $id_empresa)->first();       
        $connector = new WindowsPrintConnector($imprimir->nombre_impresora);        
        $impresora = new Printer($connector);    
        $impresora->setJustification(Printer::JUSTIFY_CENTER); 
        try {
            $logo = EscposImage::load('logo.jpg', false);
            $impresora->bitImage($logo);
        } catch (Exception $e) {
            /* Images not supported on your PHP, or image file not found */
            $impresora -> text($e -> getMessage() . "\n");
        }             
        
        $impresora->setTextSize(1, 2);
        $impresora->setEmphasis(true);
        $impresora->text($infoEmpresa->nombre."\n");
        $impresora->setTextSize(1, 1);        
        $impresora->setEmphasis(false);
        $impresora->text("NIT: ");
        $impresora->text($infoEmpresa->nit."\n");
        $impresora->text("Dirección: ");
        $impresora->text($infoEmpresa->direccion."\n");
        $impresora->text("Fecha: ");
        $impresora->text($desdeFiltro." - ". $hastaFiltro."\n");
        $impresora->text("\n=====================================\n");
        $impresora->setEmphasis(true);
        $impresora->text(sprintf('%+10s %+10s %+10s %+10s', '$Inicial', '$Ventas','$Report','$Diferen'));
        $impresora->setEmphasis(false);
        $impresora->text("\n=====================================\n");
        

        foreach($cajas_cierres as $cc ){
            $facturacion = Facturacion::select()
            ->where('facturacion.id_cierre_caja','=',$cc->id)
            ->where('facturacion.estado','=',2)
            ->get();

            $total_ventas=0;
            $cont=0;

            foreach($facturacion as $fac){
                $total_ventas += $fac->total;
                $cont++;
            }            
            $cc->total_ventas = $total_ventas;
            $cc->total_caja =$cc->total_ventas + $cc->vr_inicial;
            $cc->diferencia = $cc->total_caja-$cc->vr_final;
            $cc->no_facturas = $cont;

            $impresora->text(sprintf('%+10s %+10s %+10s %+10s', $cc->vr_inicial,$cc->total_ventas,$cc->vr_final,$cc->diferencia));
            $impresora->text("\n");    

            // Coincidencia de valor reportado y valor vendido
            if($cc->diferencia == 0){
                $cc->estado = 1; /*Correcto*/
            }
            else 
            {
                if($cc->diferencia < 0){
                    $cc->estado = 2;//falta
                } 
                elseif($cc->diferencia > 0){
                    $cc->estado = 3;//sobra
                }

            }

        }
        $impresora->text("\n******************************************\n");        
        $impresora->setFont(Printer::MODE_FONT_B);
        $impresora->text("Sasseri");
        $impresora->text("\nwww.fractalagenciadigital.com\n");
        
        $impresora->feed(5);
        $impresora->cut();
        $impresora->close();        
        
        return redirect()->back()->with("mensaje", "Ticket impreso");
        // return [
        //     'cajas_cierres' => $cajas_cierres,
        // ];

    }

}
