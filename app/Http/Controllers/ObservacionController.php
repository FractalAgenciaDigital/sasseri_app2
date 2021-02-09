<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Observacion;
use App\Articulo;
use Illuminate\Support\Facades\Auth;


class ObservacionController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id_empresa = $request->session()->get('id_empresa');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        
        if ($buscar==''){
            $observacion = Observacion::leftJoin('articulos', 'articulos.id', 'observaciones.id_articulo')->select('observacion', 'observaciones.id as id', 'articulos.nombre as nombre_articulo', 'observaciones.estado as estado')->orderBy('id', 'desc')->paginate(10);
        }
        else{
            $observacion = Observacion::leftJoin('articulos', 'articulos.id', 'observaciones.id_articulo')->select('observacion', 'observaciones.id as id', 'articulos.nombres as nombre_articulo', 'observaciones.estado as estado')->where($criterio, 'like', '%'. $buscar . '%')->orderBy('id', 'desc')->paginate(10);
        }
        

        return [
            'pagination' => [
                'total'        => $observacion->total(),
                'current_page' => $observacion->currentPage(),
                'per_page'     => $observacion->perPage(),
                'last_page'    => $observacion->lastPage(),
                'from'         => $observacion->firstItem(),
                'to'           => $observacion->lastItem(),
            ],
            'observacion' => $observacion
        ];
    }

    public function selectObservacion(Request $request){
        if (!$request->ajax()) return redirect('/');
        $id_empresa = $request->session()->get('id_empresa');

        $observacion = Observacion::select('id','observacion')->where('estado','=','1')->orderBy('created_at', 'asc')->get();
        
        return ['observacion' => $observacion];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');

        $observacion = new Observacion();
        $observacion->observacion = $request->observacion;
        $observacion->id_articulo = $request->id_articulo_obs;
        // $observacion->usu_crea = $id_usuario;
        $observacion->save();
    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $observacion = Observacion::findOrFail($request->id);
        $observacion->observacion = $request->observacion;
        $observacion->save();
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $observacion = Observacion::findOrFail($request->id);
        $observacion->estado = '0';
        $observacion->save();
    }

    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $observacion = Observacion::findOrFail($request->id);
        $observacion->estado = '1';
        $observacion->save();
    }
}
