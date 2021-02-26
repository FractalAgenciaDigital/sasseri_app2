<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DetalleFacturacion;
use App\Facturacion;
use App\Impresora;
use App\ConfigGenerales;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;

class DetalleFacturacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        
        if ($buscar==''){
            $detalle_facturacion = DetalleFacturacion::join('facturacion', 'detalle_facturacion.id_factura','=','facturacion.id')
            ->join('articulos', 'detalle_facturacion.id_producto','=','articulos.id')
            ->select('detalle_facturacion.id','detalle_facturacion.id_factura','facturacion.num_factura as num_factura','detalle_facturacion.id_producto','articulos.codigo as codigo_articulo','articulos.nombre as nombre_articulo','detalle_facturacion.valor_venta','detalle_facturacion.cantidad','detalle_facturacion.cantidad as cantidad2','detalle_facturacion.valor_iva','detalle_facturacion.valor_descuento','detalle_facturacion.porcentaje_iva','detalle_facturacion.valor_subtotal','detalle_facturacion.valor_final', 'preparado')
            ->orderBy('id', 'desc')
            ->paginate(10);
        }
        else{
            $detalle_facturacion = DetalleFacturacion::join('facturacion', 'detalle_facturacion.id_factura','=','facturacion.id')
            ->join('articulos', 'detalle_facturacion.id_producto','=','articulos.id')
            ->select('detalle_facturacion.id','detalle_facturacion.id_factura','facturacion.num_factura as num_factura','detalle_facturacion.id_producto','articulos.codigo as codigo_articulo','articulos.nombre as nombre_articulo','articulos.precio_venta','detalle_facturacion.valor_venta','detalle_facturacion.cantidad','detalle_facturacion.cantidad as cantidad2','detalle_facturacion.valor_iva','detalle_facturacion.valor_descuento','detalle_facturacion.porcentaje_iva','detalle_facturacion.valor_subtotal','detalle_facturacion.valor_final','preparado')
            ->where($criterio, 'like', '%'. $buscar . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);
        }
        

        return [
            'pagination' => [
                'total'        => $detalle_facturacion->total(),
                'current_page' => $detalle_facturacion->currentPage(),
                'per_page'     => $detalle_facturacion->perPage(),
                'last_page'    => $detalle_facturacion->lastPage(),
                'from'         => $detalle_facturacion->firstItem(),
                'to'           => $detalle_facturacion->lastItem(),
            ],
            'detalle_facturacion' => $detalle_facturacion
        ];
    }

    public function buscarDetalleFacturacion(Request $request){

        // if (!$request->ajax()) return redirect('/');

        $id_factura = $request->id_factura;
        $id_empresa = $request->session()->get('id_empresa');

        $detalle_facturacion = DetalleFacturacion::leftJoin('facturacion', 'detalle_facturacion.id_factura','=','facturacion.id')
        ->leftJoin('articulos', 'detalle_facturacion.id_producto','=','articulos.id')
        ->leftJoin('presentacion','articulos.id_presentacion','=','presentacion.id')
        ->select('detalle_facturacion.id','detalle_facturacion.id_factura','facturacion.num_factura as num_factura','detalle_facturacion.id_producto','detalle_facturacion.padre','articulos.id as idarticulo','articulos.codigo','articulos.codigo as codigo_articulo','articulos.nombre as articulo','articulos.precio_venta as precio','articulos.stock','detalle_facturacion.valor_venta','detalle_facturacion.cantidad','detalle_facturacion.cantidad as cantidad2','detalle_facturacion.valor_iva','detalle_facturacion.valor_descuento','detalle_facturacion.valor_descuento as valor_descuento2','detalle_facturacion.porcentaje_iva as iva','detalle_facturacion.valor_subtotal','detalle_facturacion.valor_final','articulos.id_presentacion','presentacion.nombre as nom_presentacion', 'articulos.tipo_articulo','preparado')
        ->where('detalle_facturacion.id_factura','=', $id_factura)
        ->get();
        
        $arrayDetalles = [];
        foreach($detalle_facturacion as $df)
        {
            if($df->padre!=null && $df->padre!='' && $df->padre!=0)
            {
                $cons = "SELECT productos_asociados.id_presentacion, presentacion.nombre as nom_presentacion FROM productos_asociados,presentacion WHERE productos_asociados.id=".$df->id_producto." AND productos_asociados.id_presentacion=presentacion.id";
                $cons = DB::select($cons);

                $cons2 = "SELECT articulos.id as idarticulo,articulos.codigo,articulos.codigo as codigo_articulo,articulos.nombre as articulo,articulos.precio_venta as precio,articulos.stock FROM articulos WHERE articulos.id=".$df->padre;
                $cons2 = DB::select($cons2);

                $df->id_presentacion = $cons[0]->id_presentacion;
                $df->nom_presentacion = $cons[0]->nom_presentacion;
                $df->id_asociado = $df->id_producto;
                
                $df->idarticulo = $cons2[0]->idarticulo;
                $df->codigo = $cons2[0]->codigo;
                $df->codigo_articulo = $cons2[0]->codigo_articulo;
                $df->articulo = $cons2[0]->articulo;
                $df->precio = $cons2[0]->precio;
                $df->stock = $cons2[0]->stock;
            }
        }

        return ['detalles' => $detalle_facturacion];
    }
    public function productosPreparados(Request $request){

        // if (!$request->ajax()) return redirect('/');
        $id_impresora = Auth::user()->id_impresora;

        $id_factura = $request->id_factura;

        $facturacion = Facturacion::join('personas', 'facturacion.id_tercero','=','personas.id')
            ->join('zona', 'facturacion.lugar','=','zona.id')->join('users', 'facturacion.id_usuario','=','users.id')
            ->select('facturacion.id','facturacion.num_factura','facturacion.id_tercero','personas.nombre as nom_tercero','facturacion.id_usuario','facturacion.fec_crea','facturacion.fec_edita','facturacion.usu_edita','facturacion.subtotal','facturacion.valor_iva','facturacion.total','abono','facturacion.saldo','facturacion.detalle','facturacion.lugar','zona.zona as nom_lugar','facturacion.descuento','facturacion.fec_registra','facturacion.fec_envia','facturacion.fec_anula','facturacion.usu_registra','facturacion.usu_envia','facturacion.usu_anula','facturacion.fecha','facturacion.id_tarifario','facturacion.estado','personas.nombre1','personas.nombre2','personas.apellido1','personas.apellido2','users.idrol', 'facturacion.fec_crea')
            ->where('facturacion.estado','=',1)
            ->get();        

            $factura = array();            
            $df = array();
        
            foreach($facturacion as $fac){
               

                $detalle_facturacion = DetalleFacturacion::leftJoin('facturacion', 'detalle_facturacion.id_factura','=','facturacion.id')
                ->leftJoin('articulos', 'detalle_facturacion.id_producto','=','articulos.id')
                ->leftJoin('presentacion','articulos.id_presentacion','=','presentacion.id')
                ->select('detalle_facturacion.id as id', 'detalle_facturacion.observaciones','detalle_facturacion.id_factura','facturacion.num_factura as num_factura','detalle_facturacion.id_producto','detalle_facturacion.padre','articulos.id as idarticulo','articulos.codigo','articulos.codigo as codigo_articulo','articulos.nombre as articulo','articulos.precio_venta as precio','articulos.stock','detalle_facturacion.valor_venta','detalle_facturacion.cantidad','detalle_facturacion.cantidad as cantidad2','detalle_facturacion.valor_iva','detalle_facturacion.valor_descuento','detalle_facturacion.valor_descuento as valor_descuento2','detalle_facturacion.porcentaje_iva as iva','detalle_facturacion.valor_subtotal','detalle_facturacion.valor_final','articulos.id_presentacion','presentacion.nombre as nom_presentacion', 'articulos.tipo_articulo','preparado')   
                ->where('detalle_facturacion.id_factura', '=', $fac->id)
                ->where('detalle_facturacion.preparado', '<>', 3)
                ->orderBy('facturacion.id', 'asc')
                ->get();
                
                if((count($detalle_facturacion))>0){
                    // $df[$fac['id']]=$fac;
                    $factura[$fac['id']]=array(
                        'nombre_lugar' => $fac['nom_lugar'],
                        'id' => $fac['id'],
                        'estado' => $fac['estado'],
                        'fec_crea' => $fac['fec_crea']
                    );
                    foreach($detalle_facturacion as $det){
                        $factura[$fac['id']]['productos'][$det['id']] = [
                                'id' => $det['id'],
                                'articulo' => $det['articulo'],
                                'cantidad' => $det['cantidad'],
                                'observaciones' => $det['observaciones'],
                                'preparado' => $det['preparado'],                       
                        ];
                    }
                }
            }       

            return [
            // 'detalles' => $detalle_facturacion, 
            'factura' => $factura];
       
    }
   
    public function verTicket(Request $request)
    {
        $id_factura = $request->id;
        $id_empresa = $request->session()->get('id_empresa');
        $id_impresora = Auth::user()->id_impresora;
        
       $imprimir = Impresora::where('id', $id_impresora)->first();

        $detalle_facturacion = DetalleFacturacion::leftJoin('facturacion', 'detalle_facturacion.id_factura','=','facturacion.id')
        ->leftJoin('articulos', 'detalle_facturacion.id_producto','=','articulos.id')
        ->leftJoin('presentacion','articulos.id_presentacion','=','presentacion.id')
        ->select('detalle_facturacion.id',
        'detalle_facturacion.id_factura',
        'facturacion.num_factura as num_factura',
        'detalle_facturacion.id_producto',
        'detalle_facturacion.padre',
        'articulos.id as idarticulo',
        'articulos.codigo',
        'articulos.codigo as codigo_articulo',
        'articulos.nombre as nombre_articulo',
        'articulos.precio_venta as precio',
        'articulos.stock',
        'detalle_facturacion.valor_venta',
        'detalle_facturacion.observaciones',
        'detalle_facturacion.cantidad',
        'detalle_facturacion.cantidad as cantidad2',
        'detalle_facturacion.valor_iva',
        'detalle_facturacion.valor_descuento',
        'detalle_facturacion.valor_descuento as valor_descuento2',
        'detalle_facturacion.porcentaje_iva as iva',
        'detalle_facturacion.valor_subtotal',
        'detalle_facturacion.valor_final',
        'articulos.id_presentacion',
        'presentacion.nombre as nom_presentacion',
         'articulos.tipo_articulo',
        'detalle_facturacion.preparado',
        'preparado')
        ->where('detalle_facturacion.id_factura','=', $id_factura)
        // ->where('articulos.id_impresora','=', $id_impresora)
        ->get();

        $facturacion = Facturacion::leftJoin('personas', 'personas.id', 'facturacion.id_tercero')
        ->leftJoin('personas as p_usuarios', 'p_usuarios.id', 'facturacion.id_usuario')
        ->leftJoin('zona', 'zona.id', 'facturacion.lugar')
        ->select('facturacion.id as id', 'facturacion.fec_crea', 'personas.nombre1', 'personas.nombre2', 'personas.apellido1', 'personas.apellido2', 'p_usuarios.nombre as cajero', 'p_usuarios.id as idusuario', 'personas.id as idpersona', 'zona.zona')->where('facturacion.id','=', $id_factura)
        ->first();
        // ->get();
        $infoEmpresa = ConfigGenerales::select()->where('id','=', $id_empresa)->limit(1)->get();

        foreach($detalle_facturacion as $df)
        {
            if($df->padre!=null && $df->padre!='' && $df->padre!=0)
            {
                $cons = "SELECT productos_asociados.id_presentacion, presentacion.nombre as nom_presentacion FROM productos_asociados,presentacion WHERE productos_asociados.id=".$df->id_producto." AND productos_asociados.id_presentacion=presentacion.id";
                $cons = DB::select($cons);

                $cons2 = "SELECT articulos.id as idarticulo,articulos.codigo,articulos.codigo as codigo_articulo,articulos.nombre as articulo,articulos.precio_venta as precio,articulos.stock FROM articulos WHERE articulos.id=".$df->padre;
                $cons2 = DB::select($cons2);

                $df->id_presentacion = $cons[0]->id_presentacion;
                $df->nom_presentacion = $cons[0]->nom_presentacion;
                $df->id_asociado = $df->id_producto;
                
                $df->idarticulo = $cons2[0]->idarticulo;
                $df->codigo = $cons2[0]->codigo;
                $df->codigo_articulo = $cons2[0]->codigo_articulo;
                $df->articulo = $cons2[0]->articulo;
                $df->precio = $cons2[0]->precio;
                $df->stock = $cons2[0]->stock;
            }
        }

        
        return ['detalles_facturacion' => $detalle_facturacion,
                'facturacion'  => $facturacion
        ];
        
    }
    public function imprimirTicket(Request $request)
    {
        $id_factura = $request->id;
        $id_empresa = $request->session()->get('id_empresa');
        $id_impresora = Auth::user()->id_impresora;
        
       $imprimir = Impresora::where('id', $id_impresora)->first();

       

        // $detalle_facturacion = DetalleFacturacion::findOrFail($id_factura);
        // $nombreImpresora = env("NOMBRE_IMPRESORA");

        $detalle_facturacion = DetalleFacturacion::leftJoin('facturacion', 'detalle_facturacion.id_factura','=','facturacion.id')
        ->leftJoin('articulos', 'detalle_facturacion.id_producto','=','articulos.id')
        ->leftJoin('presentacion','articulos.id_presentacion','=','presentacion.id')
        ->select('detalle_facturacion.id',
        'detalle_facturacion.id_factura',
        'facturacion.num_factura as num_factura',
        'detalle_facturacion.id_producto',
        'detalle_facturacion.padre',
        'articulos.id as idarticulo',
        'articulos.codigo',
        'articulos.codigo as codigo_articulo',
        'articulos.nombre as nombre_articulo',
        'articulos.precio_venta as precio',
        'articulos.stock',
        'detalle_facturacion.valor_venta',
        'detalle_facturacion.cantidad',
        'detalle_facturacion.cantidad as cantidad2',
        'detalle_facturacion.valor_iva',
        'detalle_facturacion.valor_descuento',
        'detalle_facturacion.valor_descuento as valor_descuento2',
        'detalle_facturacion.porcentaje_iva as iva',
        'detalle_facturacion.valor_subtotal',
        'detalle_facturacion.preparado',
        'detalle_facturacion.valor_final',
        'articulos.id_presentacion',
        'presentacion.nombre as nom_presentacion',
        'detalle_facturacion.observaciones',
         'articulos.tipo_articulo',         
        'preparado')
        ->where('detalle_facturacion.id_factura','=', $id_factura)
        // ->where('articulos.id_impresora','=', $id_impresora)
        ->get();

        $facturacion = Facturacion::leftJoin('personas', 'personas.id', 'facturacion.id_tercero')
        ->leftJoin('personas as p_usuarios', 'p_usuarios.id', 'facturacion.id_usuario')
        ->leftJoin('zona', 'zona.id', 'facturacion.lugar')
        ->select('facturacion.id as id', 'facturacion.fec_crea', 'personas.nombre1', 'personas.nombre2', 'personas.apellido1', 'personas.apellido2', 'p_usuarios.nombre as cajero', 'p_usuarios.id as idusuario', 'personas.id as idpersona', 'total', 'zona.zona')->where('facturacion.id','=', $id_factura)
        ->first();
        // ->get();
        $infoEmpresa = ConfigGenerales::select()->where('id','=', $id_empresa)->limit(1)->get();
        

        $connector = new WindowsPrintConnector($imprimir->nombre_impresora);
        $impresora = new Printer($connector);
        // $impresora->lineSpacing(19);
        // $logo = EscposImage::load('logo.jpg', false);
        $impresora->bitImage($logo);
        $impresora->setJustification(Printer::JUSTIFY_CENTER)
        ;
        $impresora->text("\n===============================\n");
        $impresora->setEmphasis(true);
        $impresora->text($infoEmpresa[0]->nombre."\n");
        $impresora->setEmphasis(false);
        // $impresora->text("NIT: ");
        // $impresora->text($infoEmpresa[0]->nit."\n");
        // $impresora->text("Dirección: ");
        // $impresora->text($infoEmpresa[0]->direccion."\n");
        
       
         $impresora->setEmphasis(true);
        $impresora->text("Cajero(a): ");
        $impresora->text($facturacion->cajero."\n");
        $impresora->text("Mesa: ");
        $impresora->text($facturacion->zona."\n");
        $impresora->setEmphasis(false);

        $impresora->text($facturacion->fec_crea."\n"."\n");
        $impresora->setLineSpacing(2);
 
        $impresora->setJustification(Printer::JUSTIFY_LEFT);
        $impresora->text("\n______________________________________"."\n\n");
        $impresora->setLineSpacing(2);
        $impresora->text(sprintf('%-25s %+10.8s %+10.7s','ARTICULO', 'CANT', 'PRECIO'));
        $impresora->text("\n"); 
       
        $impresora->text("______________________________________\n"."\n");

        foreach($detalle_facturacion as $df)
        {
            
            $line = sprintf('%-25s %10.0f %10.2f ','-'. $df->nombre_articulo, $df->cantidad, $df->cantidad * $df->precio);
            $impresora->setEmphasis(true);
          
            $impresora->text($line);
                      
            if(($df->observaciones) != ''){
                $impresora->text("\n"); 
                $impresora->text('Notas:');
                $impresora->text($df->observaciones);
            }
            $impresora->text("\n"); 
            $impresora->text("\n"); 
            
            
        }

        $impresora->setJustification(Printer::JUSTIFY_CENTER);
        $impresora->text("\n_________________________________\n");
        $impresora->setJustification(Printer::JUSTIFY_RIGHT);
        $impresora->setEmphasis(true);
        $impresora->setLineSpacing(2);

        $impresora->text("\nTotal: $" . number_format($facturacion->total, 2) . "\n");
        $impresora->setJustification(Printer::JUSTIFY_CENTER);
       
        $impresora->setTextSize(1, 1);
        $impresora->setLineSpacing(2);
        $impresora->text("\n===============================\n");
        $impresora->setEmphasis(false);
        $impresora->setFont(Printer::FONT_C);
        $impresora->text("Sasseri");
        $impresora->text("\nwww.fractalagenciadigital.com\n");
        $impresora->text("\n===============================\n");
        $impresora->text("Gracias por su compra\n");
        $impresora->text("\n===============================\n");

        
        $impresora->feed(5);
        $impresora->cut();
        $impresora->pulse();
        $impresora->close();
        
        return redirect()->back()->with("mensaje", "Ticket impreso");
        
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
        $categoria = new Facturacion();
        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->descripcion;
        $categoria->condicion = '1';
        $categoria->save();
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
        $categoria = Facturacion::findOrFail($request->id);
        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->descripcion;
        $categoria->condicion = '1';
        $categoria->save();
    }

    public function sinCocinar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $categoria = DetalleFacturacion::findOrFail($request->id);
        $categoria->preparado = '0';
        $categoria->save();
    }

    public function cocinado(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        // $app = App\ModelName::find(1);
        // $app->where("status", 1)
        // ->update(["keyOne" => $valueOne, "keyTwo" => $valueTwo]);

        if($request->tipo==1){

            $factura = DetalleFacturacion::where('detalle_facturacion.id_factura',$request['id'])->get();

            foreach($factura as $cat){
                $categoria = DetalleFacturacion::findOrFail($cat['id']);
                $categoria->preparado = '3';
                $categoria->save();
            }        
        }
        else{
            $categoria = DetalleFacturacion::findOrFail($request->id);
            $categoria->preparado = '1';
            $categoria->save();            
        }

        
    }

    
}
