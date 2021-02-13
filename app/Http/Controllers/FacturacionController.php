<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facturacion;
use App\DetalleFacturacion;
use App\Stock;
use App\Articulo;
use App\User;
use App\Notifications\NotifyAdmin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Exports\FacturacionExport;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Facades\Excel;

use App\Impresora;
use App\ConfigGenerales;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;


class FacturacionController extends Controller
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

    public function buscarFacturacion(Request $request){
        if (!$request->ajax()) return redirect('/');
        $id_empresa = $request->session()->get('id_empresa');

        $filtro = $request->filtro;
        $facturacion = Facturacion::join('personas', 'facturacion.id_tercero','=','personas.id')
        ->select('facturacion.id','facturacion.num_factura','facturacion.id_tercero','personas.nombre as nom_tercero','facturacion.id_usuario','facturacion.fec_crea','facturacion.fec_edita','facturacion.usu_edita','facturacion.subtotal','facturacion.valor_iva','facturacion.total','abono','facturacion.saldo','facturacion.detalle','facturacion.lugar','facturacion.descuento','facturacion.fec_registra','facturacion.fec_envia','facturacion.fec_anula','facturacion.usu_registra','facturacion.usu_envia','facturacion.usu_anula','facturacion.fecha','facturacion.estado','personas.nombre1','personas.nombre2','personas.apellido1','personas.apellido2')
        ->where('id_empresa','=',$id_empresa)
        ->where('facturacion.id','=', $filtro)
        ->get();

        return ['facturacion' => $facturacion];
    }

    public function redirect_log(){
        return redirect('/');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id_empresa = $request->session()->get('id_empresa');

        $id_usuario = Auth::user()->id;
        $facturacion = new Facturacion();
        $facturacion->num_factura = $request->num_factura;
        $facturacion->id_tercero = $request->id_tercero;
        $facturacion->id_usuario = $id_usuario;
        $facturacion->fec_edita = null;
        $facturacion->usu_edita = null;
        $facturacion->subtotal = $request->subtotal;
        $facturacion->valor_iva = $request->valor_iva;
        $facturacion->total = $request->total;
        $facturacion->abono = $request->abono;
        $facturacion->saldo = $request->saldo;
        $facturacion->detalle = $request->detalle;
        $facturacion->lugar = $request->lugar;
        $facturacion->descuento = $request->descuento;
        $facturacion->fec_registra = null;
        $facturacion->fec_envia = null;
        $facturacion->fec_anula = null;
        $facturacion->usu_registra = null;
        $facturacion->usu_envia = null;
        $facturacion->usu_anula = null;
        $facturacion->fecha = $request->fecha;
        $facturacion->id_tarifario = $request->id_tarifario;
        if($request->id_cierre_caja){$facturacion->id_cierre_caja = $request->id_cierre_caja;}
        $facturacion->id_empresa = $id_empresa;
        $facturacion->estado = '1';
        $facturacion->save();

        $detalles = $request->data;//Array de detalles
            //Recorro todos los elementos

        foreach($detalles as $ep=>$det)
        {
            $detalle = new DetalleFacturacion();
            $detalle->id_factura = $facturacion->id;
            if(isset($det['id_asociado']) && $det['id_asociado']!=''){
                $detalle->id_producto = $det['id_asociado'];
            }
            else{
                $detalle->id_producto = $det['idarticulo'];
            }
            $detalle->padre = $det['padre'];
            $detalle->valor_venta = $det['precio'];
            $detalle->cantidad = $det['cantidad'];
            $detalle->valor_iva = $det['valor_iva'];
            $detalle->valor_descuento = $det['valor_descuento'];
            $detalle->porcentaje_iva = $det['iva'];
            $detalle->valor_subtotal = $det['valor_subtotal'];  
            $detalle->valor_final = $det['valor_subtotal']+$det['valor_iva'];
            $detalle->observaciones = $det['observaciones'];
            $detalle->save();

            $fechaActual = date('Y-m-d');
            $numVentas = DB::table('detalle_facturacion')->whereDate('created_at',$fechaActual)->count();

            $stock = new Stock();
            $stock->id_producto = $det['idarticulo'];
            $stock->id_usuario = $id_usuario;
            $stock->id_facturacion = $facturacion->id;
            $stock->cantidad = $det['cantidad'];
            $stock->tipo_movimiento = $request->tipo_movimiento;
            $stock->sumatoria = $request->sumatoria;
            $stock->save();
           
           
        }
        return ['id_facturacion' => $facturacion->id];        
    }
  

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id_usuario = Auth::user()->id;
        
        $facturacion = Facturacion::findOrFail($request->id);
        $facturacion->num_factura = $request->num_factura;
        $facturacion->id_tercero = $request->id_tercero;
        $facturacion->fec_edita = $request->fec_edita;
        $facturacion->usu_edita = $id_usuario;
        $facturacion->subtotal = $request->subtotal;
        $facturacion->valor_iva = $request->valor_iva;
        $facturacion->total = $request->total;
        $facturacion->abono = $request->abono;
        $facturacion->saldo = $request->saldo;
        $facturacion->detalle = $request->detalle;
        $facturacion->lugar = $request->lugar;
        $facturacion->descuento = $request->descuento;
        $facturacion->fec_registra = null;
        $facturacion->fec_envia = null;
        $facturacion->fec_anula = null;
        $facturacion->usu_registra = null;
        $facturacion->usu_envia = null;
        $facturacion->usu_anula = null;
        $facturacion->fecha = $request->fecha;
        $facturacion->id_tarifario = $request->id_tarifario;
        $facturacion->estado = $request->estado;
        $facturacion->save();

        $borrar_detalles = DetalleFacturacion::where('id_factura','=',$request->id)->delete();

        $detalles = $request->data;//Array de detalles

        foreach($detalles as $ep=>$det)
        {
            $detalle = new DetalleFacturacion();
            $detalle->id_factura = $request->id;
            if(isset($det['id_asociado']) && $det['id_asociado']!=''){
                $detalle->id_producto = $det['id_asociado'];
            }
            else{
                $detalle->id_producto = $det['idarticulo'];
            }
            $detalle->padre = $det['padre'];
            $detalle->valor_venta = $det['precio'];
            $detalle->cantidad = $det['cantidad'];
            $detalle->valor_iva = $det['valor_iva'];
            $detalle->valor_descuento = $det['valor_descuento'];
            $detalle->porcentaje_iva = $det['iva'];
            $detalle->valor_subtotal = $det['valor_subtotal'];  
            $detalle->valor_final = $det['valor_subtotal']+$det['valor_iva'];
            if(isset($det['observaciones'])){
                $detalle->observaciones = $det['observaciones'];
            }else{
                $detalle->observaciones = '';
            }
            $detalle->save();
        }


        return ['id_facturacion' => $request->id];   
    }

    public function cambiarEstado(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');

        $facturacion = Facturacion::findOrFail($request->id);
        $facturacion->estado = $request->estado;
        if($request->estado==2 && $request->num_factura)
        {
            $detalles = DetalleFacturacion::where('id_factura','=',$request->id)->get();

            if(!empty($detalles))
            {
                foreach($detalles as $det)
                {
                    $articulo = Articulo::findOrFail($det['id_producto']);

                    if($articulo->stock>=$det['cantidad'])
                    {
                        $stock = new Stock();
                        $stock->id_producto = $det['id_producto'];
                        $stock->id_usuario = $id_usuario;
                        $stock->id_facturacion = $request->id;
                        $stock->cantidad = $det['cantidad'];
                        $stock->tipo_movimiento = 4;
                        $stock->sumatoria = 0;
                        $stock->save();

                        $articulo->stock = $articulo->stock-$det['cantidad'];
                        $articulo->save();
                    }
                }
            }

            $facturacion->num_factura=$request->num_factura;
        }

        if($request->estado==4)
        {
            $detalles = DetalleFacturacion::where('id_factura','=',$request->id)->get();

            if(!empty($detalles))
            {
                foreach($detalles as $det)
                {
                    $articulo = Articulo::findOrFail($det['id_producto']);
                    $stock = Stock::where('id_producto','=',$det['id_producto'])->where('id_facturacion','=',$request->id)->delete();

                    $articulo->stock = $articulo->stock+$det['cantidad'];
                    $articulo->save();
                }
            }

            $facturacion->num_factura=$request->num_factura;
        }

        $facturacion->save();

        if($facturacion->estado==2)
        {
            if($facturacion->saldo>0)
            {
                $cons= "INSERT INTO cuentas_x_cobrar (id_tercero,id_factura,valor_deuda,abono,saldo,fecha_cobro,estado_cobro,id_empresa,usu_crea) VALUES (".$facturacion->id_tercero.",".$facturacion->id.",".$facturacion->total.",".$facturacion->abono.",".$facturacion->saldo.",'".$facturacion->fecha."',1,".$id_empresa.",".$id_usuario.")";

                $cuentaXCobrar = DB::insert($cons);
                return $cons;
            }
        }
    }

    public function buscarNumFacturaSugerida(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        
        $facturacion = Facturacion::select('num_factura')
        ->where('num_factura','!=',null)
        ->orderBy('num_factura', 'desc')
        ->take(1)
        ->get();
        
        return [
            'facturacion' => $facturacion
        ];
    }

    public function obtenerCabecera(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $facturacion = Facturacion::join('personas','facturacion.id_tercero','=','personas.id')
        ->join('users','facturacion.id_usuario','=','users.id')
        ->leftJoin('zona','facturacion.lugar','=','zona.id')
        ->select('facturacion.id','facturacion.num_factura','facturacion.id_tercero','facturacion.fec_crea','facturacion.fec_edita','facturacion.usu_edita','facturacion.subtotal','facturacion.valor_iva','facturacion.total','facturacion.abono','facturacion.abono as abono2','facturacion.saldo','facturacion.detalle','facturacion.lugar','facturacion.descuento','facturacion.fec_registra','facturacion.fec_envia','facturacion.fec_anula','facturacion.usu_registra','facturacion.usu_envia','facturacion.usu_anula','facturacion.fecha','facturacion.estado','personas.nombre as nom_tercero','users.usuario','zona.zona as nom_lugar')
        ->where('facturacion.id','=',$id)
        ->orderBy('facturacion.id', 'desc')->take(1)->get();
        
        return ['facturacion' => $facturacion];
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $categoria = Facturacion::findOrFail($request->id);
        $categoria->condicion = '0';
        $categoria->save();
    }

    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $categoria = Facturacion::findOrFail($request->id);
        $categoria->condicion = '1';
        $categoria->save();
    }

    public function PdfFacturacion(Request $request, $id)
    {
  
        $cons="SELECT id,num_factura,id_tercero,subtotal,valor_iva,total,abono,saldo,detalle,lugar,descuento,fecha,estado FROM facturacion WHERE id=$id";
        $factura = DB::select($cons); 
        $factura = $factura[0];

        $cons="SELECT nombre1,nombre2,apellido1,apellido2,num_documento, tipo_documento FROM personas WHERE id=".$factura->id_tercero;
        $tercero = DB::select($cons); 
        $tercero = $tercero[0];

        // return ['facturacion'=>$factura,'tercero'=>$tercero];
        $pdf = \PDF::loadView('pdf.Factura',['facturacion'=>$factura,'tercero'=>$tercero]);
        return $pdf->download('pdf.factura-'.$factura->num_factura.'.pdf');
        // return view('Factura')->with('facturacion', $factura)->with('tercero', $tercero);
    }

    public function ExcelFacturacion(Request $request, $id)
    {
  
        $cons="SELECT id,num_factura,id_tercero,fec_crea,fec_edita,usu_edita,subtotal,valor_iva,total,abono,abono as abono2,saldo,detalle,lugar,descuento,fec_registra,fec_envia,fec_anula,usu_registra,usu_envia,usu_anula,fecha,estado,id_usuario,id_cierre_caja FROM facturacion WHERE id=$id";
        $factura = DB::select($cons); 
        $factura = $factura[0];

        $cons2 = "SELECT detalle_facturacion.id,detalle_facturacion.id_factura,detalle_facturacion.id_producto,detalle_facturacion.padre,detalle_facturacion.valor_venta,detalle_facturacion.cantidad,detalle_facturacion.valor_iva,detalle_facturacion.valor_descuento,detalle_facturacion.porcentaje_iva,detalle_facturacion.valor_subtotal,detalle_facturacion.valor_final, articulos.nombre,articulos.idcategoria, articulos.idcategoria2,articulos.codigo,articulos.precio_venta,articulos.stock,articulos.descripcion,articulos.cod_invima,articulos.lote,articulos.fec_vence,articulos.minimo,articulos.tipo_articulo,articulos.iva, articulos.talla,articulos.id_und_medida,articulos.id_concentracion,articulos.id_presentacion,articulos.condicion, presentacion.nombre as nom_presentacion FROM detalle_facturacion,articulos, presentacion WHERE id_factura=".$factura->id." AND detalle_facturacion.id_producto=articulos.id AND articulos.id_presentacion=presentacion.id";
        $detalles = DB::select($cons2);

        $cons3="SELECT nombre1,nombre2,apellido1,apellido2,num_documento, tipo_documento FROM personas WHERE id=".$factura->id_tercero;
        $tercero = DB::select($cons3); 
        $tercero = $tercero[0];

        $cons4="SELECT * FROM users WHERE id=".$factura->id_usuario;
        $user = DB::select($cons4); 
        $user = $user[0];

        $cons5="SELECT * FROM cajas_cierres,cajas WHERE cajas_cierres.id=".$factura->id_cierre_caja.' AND cajas_cierres.id_caja=cajas.id';
        $caja = DB::select($cons5); 
        $caja = $caja[0];

        $cons6="SELECT * FROM zona WHERE zona.id=".$factura->lugar;
        $lugar = DB::select($cons6); 
        $lugar = $lugar[0];

        // return view('pdf.FacturaExcel', ['facturacion'=>$factura, 'tercero'=>$tercero,'detalles'=>$detalles,'user'=>$user,'caja'=>$caja,'lugar'=>$lugar,],);
        return Excel::download(new FacturacionExport($id), 'pdf.facturaExcel-'.$factura->num_factura.'.xlsx');
    }

    public function imprimirTicketFacturacion(Request $request)
    {
        $id_factura = $request->id;
        $id_empresa = $request->session()->get('id_empresa');
        $id_impresora = $request->id_impresora;
        
       $imprimir = Impresora::where('id', $id_impresora)->first();

        // $detalle_facturacion = DetalleFacturacion::findOrFail($id_factura);
        // $nombreImpresora = env("NOMBRE_IMPRESORA");

        $detalle_facturacion = DetalleFacturacion::leftJoin('facturacion', 'detalle_facturacion.id_factura','=','facturacion.id')
        ->leftJoin('articulos', 'detalle_facturacion.id_producto','=','articulos.id')
        ->leftJoin('presentacion','articulos.id_presentacion','=','presentacion.id')
        ->select('detalle_facturacion.id','detalle_facturacion.id_factura','facturacion.num_factura as num_factura','detalle_facturacion.id_producto','detalle_facturacion.padre','articulos.id as idarticulo','articulos.codigo','articulos.codigo as codigo_articulo','articulos.nombre as nombre_articulo','articulos.precio_venta as precio','articulos.stock','detalle_facturacion.valor_venta','detalle_facturacion.cantidad','detalle_facturacion.cantidad as cantidad2','detalle_facturacion.valor_iva','detalle_facturacion.valor_descuento','detalle_facturacion.valor_descuento as valor_descuento2','detalle_facturacion.porcentaje_iva as iva','detalle_facturacion.valor_subtotal','detalle_facturacion.observaciones','detalle_facturacion.valor_final','articulos.id_presentacion','presentacion.nombre as nom_presentacion','articulos.tipo_articulo',
        'preparado')
        ->where('detalle_facturacion.id_factura','=', $id_factura)
        ->get();

        $facturacion = Facturacion::leftJoin('personas', 'personas.id', 'facturacion.id_tercero')
        ->leftJoin('personas as p_usuarios', 'p_usuarios.id', 'facturacion.id_usuario')
        ->leftJoin('zona', 'zona.id', 'facturacion.lugar')
        ->select('facturacion.id as id', 'facturacion.fec_crea', 'personas.nombre1', 'personas.nombre2', 'personas.apellido1', 'personas.apellido2', 'p_usuarios.nombre as cajero', 'p_usuarios.id as idusuario', 'personas.id as idpersona', 'total', 'zona.zona', 'facturacion.num_factura')->where('facturacion.id','=', $id_factura)
        ->first();
        // ->get();
        $infoEmpresa = ConfigGenerales::select()->where('id','=', $id_empresa)->first();
        

        $connector = new WindowsPrintConnector($imprimir->nombre_impresora);
        
        $impresora = new Printer($connector);
    
        $impresora->setJustification(Printer::JUSTIFY_CENTER);
        //     try{
            // $logo = EscposImage::load(env('APP_URL').'/Empresas/'.$infoEmpresa->id.'_empresa/ImgLogos/'.$infoEmpresa->logo, false);
        //     //   $logo = EscposImage::load("logo.jpg", true);
            // $impresora->bitImage($logo);
        // }catch(Exception $e){/*No hacemos nada si hay error*/}
    
         
        $impresora->text("\n===============================\n");
        
        $impresora->setTextSize(1, 2);
        $impresora->text($infoEmpresa->nombre."\n");
        $impresora->setTextSize(1, 1);
        
        $impresora->setEmphasis(false);
        $impresora->text("NIT: ");
        $impresora->text($infoEmpresa->nit."\n");
        $impresora->text("Dirección: ");
        $impresora->text($infoEmpresa->direccion."\n");
        

        $impresora->setJustification(Printer::JUSTIFY_LEFT);
        $impresora->setEmphasis(true);
        $impresora->text("Mesa: ");
        $impresora->text($facturacion->zona."\n");
        $impresora->text("Cajero(a): ");
        $impresora->text($facturacion->cajero."\n");
        $impresora->setEmphasis(false);
        $impresora->text("Fecha: ");
        $impresora->text($facturacion->fec_crea."\n");

        if(isset($facturacion->num_factura) || $facturacion->num_factura != NULL){
            $impresora->text("N° Factura: ");
            $impresora->text($facturacion->num_factura."\n");
        // 
        }
        $impresora->text("Cliente: ");
        $impresora->text($facturacion->nombre1." ".$facturacion->nombre2." ".$facturacion->apellido1." ".$facturacion->apellido2."\n");

       


        $impresora->setLineSpacing(2);
 
        $impresora->setJustification(Printer::JUSTIFY_LEFT);
        $impresora->text("\n______________________________________"."\n\n");
        $impresora->setLineSpacing(1);
        $impresora->setEmphasis(true);
        $impresora->text(sprintf('%-25s %+10.8s %+10.7s','ARTICULO', 'CANT', 'PRECIO'));
        $impresora->setEmphasis(false);

        $impresora->text("\n"); 
        foreach($detalle_facturacion as $df)
        {
            // $impresora->setJustification(Printer::JUSTIFY_LEFT);
            // $impresora->text($df->cantidad. "   ");
            

            // $impresora->setJustification(Printer::JUSTIFY_CENTER);
            // $impresora->text(sprintf( $df->nombre_articulo ."  "));
            
            // $impresora->setJustification(Printer::JUSTIFY_RIGHT);
            // $impresora->text('$' . number_format($df->cantidad * $df->precio, 2)."\n");
            // $impresora->setJustification(Printer::JUSTIFY_LEFT);
        //   $impresora->text($df->observaciones. "\n");
        //     $impresora->setJustification(Printer::JUSTIFY_CENTER);
        //     $impresora->text("-------------------------\n\n");

            $line = sprintf('%-25s %10.0f %10.2f ', $df->nombre_articulo, $df->cantidad, $df->cantidad * $df->precio);
            $impresora->text($line);
            $impresora->text("\n"); 
            
        }

        $impresora->setJustification(Printer::JUSTIFY_RIGHT);
        $impresora->setEmphasis(true);
        $impresora->setLineSpacing(2);

        $impresora->text("\nTotal: $" . number_format($facturacion->total, 2) . "\n");
        $impresora->setJustification(Printer::JUSTIFY_CENTER);
       
        
        $impresora->setLineSpacing(2);
        $impresora->text("\n===============================\n");
        $impresora->setEmphasis(false);
        $impresora->setFont(Printer::FONT_C);
        $impresora->text("Sasseri");
        $impresora->text("\nwww.fractalagenciadigital.com\n");
        $impresora->text("\n===============================\n");
        $impresora->text("Gracias por su compra\n");
        $impresora->text("\n===============================\n");

        
        $impresora->feed(5);$impresora->cut();
        $impresora->close();
        
        // return $connector;
        return redirect()->back()->with("mensaje", "Ticket impreso");
        
    }

   
}
