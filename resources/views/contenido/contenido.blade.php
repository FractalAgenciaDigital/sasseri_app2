@extends('principal')
    @section('contenido')
<?php
    $menu_usuario2 = Session::get('menu_usu');
    $rol_usu = Auth::user()->idrol;
    
?>
    @if(Auth::check())
        <template v-if="menu==0">
            <body>
                <section id="w3hubs">
                    <div class="container-fluid">
                        <div class="card-deck">
                            <div class="card bg-light b1">
                                <div class="card-body text-center">
                                    <img src="img/Recurso 11@0.75x.png" style="max-width: 299px;"><br><br><br>
                                    <h3 class="text-primary">Bienvenido al Sistema</h3><br><br>
                                    <p class="card-text">www.fractalagenciadigital.com</p>
                                    <div class="socialicon">
                                        <a href="#"><i class="fa fa-facebook-square"></i></a>
                                        <a href="#"><i class="fa fa-twitter-square"></i></a>
                                        <a href="#"><i class="fa fa-google-plus-square"></i></a>
                                        <a href="#"><i class="fa fa-linkedin-square"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </body>  
        </template>
        @if($rol_usu==2)                
            <template v-if="menu==40">
                <punto_venta :ruta="ruta"></punto_venta>
            </template>
        @endif
       
            

      

                   
            
			<!-- @foreach ($menu_usuario2 as $menu_usu)
				@if(count($menu_usu['hijos'])>0)
					@foreach ($menu_usu['hijos'] as $menu_hijo)
						<template v-if="menu=={{$menu_hijo['menu']}}">
							<{{$menu_hijo['template_menu']}} :ruta="ruta" :permisos-user="permisosUser['{{$menu_hijo['template_menu']}}']"></{{$menu_hijo['template_menu']}}>
						</template>
					@endforeach
				@endif
			@endforeach -->
			
        @if($rol_usu == 1)   
            <template v-if="menu==999991">
                <user :ruta="ruta"></user>
            </template>
            <template v-if="menu==10">
                <terceros :ruta="ruta"></terceros>
            </template>
            <!-- Comfiguraciones -->
                <template v-if="menu==6">
                    <configgenerales :ruta="ruta"></configgenerales>
                </template>
                <template v-if="menu==12">
                    <zona :ruta="ruta"></zona>
                </template>
                <template v-if="menu==27">
                    <iva :ruta="ruta"></iva>
                </template>
                <template v-if="menu==29">
                    <cajas :ruta="ruta"></cajas>
                </template>
                
           
            <template v-if="menu==14">
                <facturacion :ruta="ruta"></facturacion>
            </template>
            <template v-if="menu==47">
                <facturacion_mobile :ruta="ruta"></facturacion_mobile>
            </template>
            <!-- almacen -->
                <template v-if="menu==15">
                    <articulo :ruta="ruta"></articulo>
                </template>
                <template v-if="menu==17">
                    <ingreso :ruta="ruta"></ingreso>
                </template>
                <template v-if="menu==18">
                    <egreso :ruta="ruta"></egreso>
                </template>
                <template v-if="menu==19">
                    <stock :ruta="ruta"></stock>
                </template>
                <template v-if="menu==22">
                    <presentacion :ruta="ruta"></presentacion>
                </template>
                <template v-if="menu==23">
                    <und_medida :ruta="ruta"></und_medida>
                </template>
                <template v-if="menu==24">
                    <concentracion :ruta="ruta"></concentracion>
                </template>
                <template v-if="menu==25">
                    <categoria :ruta="ruta"></categoria>
                </template>
            <!-- Cajas -->
                <template v-if="menu==35">
                    <cajas_admin :ruta="ruta"></cajas_admin>
                </template>
                <template v-if="menu==30">
                    <cierrescaja :ruta="ruta"></cierrescaja>
                </template>
            <!-- Informes -->
                <template v-if="menu==31">
                    <informes :ruta="ruta"></informes>
                </template>
                <template v-if="menu==44">
                    <informe_arqueo :ruta="ruta"></informe_arqueo>
                </template>
                <template v-if="menu==45">
                    <informe_producto :ruta="ruta"></informe_producto>
                </template>
                <template v-if="menu==48">
                    <informe_categoria :ruta="ruta"></informe_categoria>
                </template>
                <template v-if="menu==46">
                    <historial :ruta="ruta"></historial>
                </template>
            <!-- Cartera -->
                <template v-if="menu==33">
                    <cuentasxcobrar :ruta="ruta"></cuentasxcobrar>
                </template>
                <template v-if="menu==34">
                    <cuentasxpagar :ruta="ruta"></cuentasxpagar>
                </template>
                <template v-if="menu==41">
                    <impresora :ruta="ruta"></impresora>
                </template>
                <template v-if="menu==43">
                    <observacion :ruta="ruta"></observacion>
                </template>
                <template v-if="menu==49">
                    <gastos :ruta="ruta"></gastos>
                </template>

            <template v-if="menu==999992">
                <rol :ruta="ruta"></rol>
            </template>

            <template v-if="menu==999993">
                <modulo :ruta="ruta"></modulo>
            </template>
            
            
            <!-- <template v-if="menu==11">
                <h1>Ayuda</h1>
            </template> -->
        @endif
            <template v-if="menu==42">
                <cocina :ruta="ruta"></cocina>
            </template>
            <template v-if="menu==999994">
                <h1>Acerca de</h1>
            </template>   
            


    @endif
       
        
    @endsection