<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Impresora;

class ImpresoraController extends Controller
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
        $id_empresa = $request->session()->get('id_empresa');
        
        $impresoras = Impresora::where($criterio, 'like', '%'. $buscar . '%');
        if($buscar!=''){
            $impresoras = Impresora::where('id_empresa','=',$id_empresa);
        }
        $impresoras = Impresora::orderBy('id', 'desc')->paginate(10);

        return [
            'pagination' => [
                'total'        => $impresoras->total(),
                'current_page' => $impresoras->currentPage(),
                'per_page'     => $impresoras->perPage(),
                'last_page'    => $impresoras->lastPage(),
                'from'         => $impresoras->firstItem(),
                'to'           => $impresoras->lastItem(),
            ],
            'impresoras' => $impresoras
        ];
    }

    public function selectImpresora(Request $request){
        if (!$request->ajax()) return redirect('/');
        $id_empresa = $request->session()->get('id_empresa');
        
        $impresoras = Impresora::where('condicion','=','1')
        ->select('id','nombre_impresora','codigo')
        ->where('id_empresa','=',$id_empresa)
        ->orderBy('nombre_impresora', 'asc')
        ->get();
        
        return ['impresoras' => $impresoras];
    }
    
    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id_empresa = $request->session()->get('id_empresa');

        $impresora = new Impresora();
        $impresora->nombre_impresora = $request->nombre_impresora;
        $impresora->codigo = $request->codigo;
        $impresora->condicion = '1';
        $impresora->id_empresa = $id_empresa;
        $impresora->save();
    }
  
    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $impresora = Impresora::findOrFail($request->id);
        $impresora->nombre_impresora = $request->nombre_impresora;
        $impresora->codigo = $request->codigo;
        $impresora->condicion = '1';
        $impresora->save();
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $impresora = Impresora::findOrFail($request->id);
        $impresora->condicion = '0';
        $impresora->save();
    }

    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $impresora = Impresora::findOrFail($request->id);
        $impresora->condicion = '1';
        $impresora->save();
    }

    
}
