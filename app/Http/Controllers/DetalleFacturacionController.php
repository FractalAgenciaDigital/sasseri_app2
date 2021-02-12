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
        $detalle_facturacion = DetalleFacturacion::leftJoin('facturacion', 'detalle_facturacion.id_factura','=','facturacion.id')
        ->leftJoin('articulos', 'detalle_facturacion.id_producto','=','articulos.id')
        ->leftJoin('presentacion','articulos.id_presentacion','=','presentacion.id')
        ->select('detalle_facturacion.id', 'detalle_facturacion.observaciones','detalle_facturacion.id_factura','facturacion.num_factura as num_factura','detalle_facturacion.id_producto','detalle_facturacion.padre','articulos.id as idarticulo','articulos.codigo','articulos.codigo as codigo_articulo','articulos.nombre as articulo','articulos.precio_venta as precio','articulos.stock','detalle_facturacion.valor_venta','detalle_facturacion.cantidad','detalle_facturacion.cantidad as cantidad2','detalle_facturacion.valor_iva','detalle_facturacion.valor_descuento','detalle_facturacion.valor_descuento as valor_descuento2','detalle_facturacion.porcentaje_iva as iva','detalle_facturacion.valor_subtotal','detalle_facturacion.valor_final','articulos.id_presentacion','presentacion.nombre as nom_presentacion', 'articulos.tipo_articulo','preparado')
        ->where('detalle_facturacion.id_factura','=', $id_factura)
        // ->where('articulos.id_impresora','=', $id_impresora)
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
        'detalle_facturacion.observaciones',
        'preparado')
        ->where('detalle_facturacion.id_factura','=', $id_factura)
        ->where('articulos.id_impresora','=', $id_impresora)
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
        'detalle_facturacion.observaciones',
        'detalle_facturacion.valor_final',
        'articulos.id_presentacion',
        'presentacion.nombre as nom_presentacion',
         'articulos.tipo_articulo',
         
        'preparado')
        ->where('detalle_facturacion.id_factura','=', $id_factura)
        ->where('articulos.id_impresora','=', $id_impresora)
        ->get();

        $facturacion = Facturacion::leftJoin('personas', 'personas.id', 'facturacion.id_tercero')
        ->leftJoin('personas as p_usuarios', 'p_usuarios.id', 'facturacion.id_usuario')
        ->select('facturacion.id as id', 'facturacion.fec_crea', 'personas.nombre1', 'personas.nombre2', 'personas.apellido1', 'personas.apellido2', 'p_usuarios.nombre as cajero', 'p_usuarios.id as idusuario', 'personas.id as idpersona', 'total')->where('facturacion.id','=', $id_factura)
        ->first();
        // ->get();
        $infoEmpresa = ConfigGenerales::select()->where('id','=', $id_empresa)->limit(1)->get();
        

        $connector = new WindowsPrintConnector($imprimir->nombre_impresora);
        $impresora = new Printer($connector);
        // $impresora->lineSpacing(19);
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
        
        $impresora->text("Cliente: ");
        $impresora->text($facturacion->nombre1." ".$facturacion->nombre2." ".$facturacion->apellido1." ".$facturacion->apellido2."\n");

        $impresora->text("Cajero(a): ");
        $impresora->text($facturacion->cajero."\n");

        $impresora->text($facturacion->fec_crea."\n"."\n");
        $impresora->setLineSpacing(2);
 
        $impresora->setJustification(Printer::JUSTIFY_LEFT);
        $impresora->text("| CANTIDAD  ");
       
        $impresora->setJustification(Printer::JUSTIFY_CENTER);        
        $impresora->text("|  ARTICULO  |  ");

        $impresora->setJustification(Printer::JUSTIFY_RIGHT);
        $impresora->text("  |   PRECIO     |\n");

        $impresora->setJustification(Printer::JUSTIFY_CENTER);
        $impresora->text("______________________________________\n"."\n");

        foreach($detalle_facturacion as $df)
        {
            
            $impresora->setJustification(Printer::JUSTIFY_LEFT);
            $impresora->text($df->cantidad. "\n");
            
            $impresora->setJustification(Printer::JUSTIFY_CENTER);
            $impresora->text(sprintf( $df->nombre_articulo ."\n"));
            
            $impresora->setJustification(Printer::JUSTIFY_RIGHT);
            $impresora->text('$' . number_format($df->cantidad * $df->precio, 2)."\n");
            $impresora->setJustification(Printer::JUSTIFY_LEFT);
            $impresora->text($df->observaciones. "\n");
            $impresora->setJustification(Printer::JUSTIFY_CENTER);
            $impresora->text("-------------------------\n\n");
            
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

        
        $impresora->feed(5);$impresora->cut();
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
        $categoria = DetalleFacturacion::findOrFail($request->id);
        $categoria->preparado = '1';
        $categoria->save();
    }

    
}
