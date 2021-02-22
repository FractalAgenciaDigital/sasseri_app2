<template>
    <main class="main">

        <div class="cotainer-fluid">
            <div class="row">
                <div class="col-12 text-left">
                    <h5>Facturacion</h5>
                    <hr>

                </div>
               
                <div class="col-md-5">
                    <div class="card" v-show="listado==0" id="newFactura">
                        <div class="card-header">                            
                            <h6>
                                <i class="fa fa-align-justify"></i> Nueva Factura
                            </h6>
                        </div> 
                        <div class="card-body">
                        <div class="form-row border">
                            <div class="col-md-3 form-group" style="display:none;">
                                
                                <label>Fecha</label>
                                <input type="date" class="form-control" v-model="fecha">
                                
                            </div>
                            <!-- <div class="col-md-2" >
                                <div class="form-group">
                                    <label>N° factura</label>
                                    <input type="number" disabled class="form-control" v-model="num_factura">
                                </div>
                            </div> -->
                        
                            <div class="form-group col-md-7">
                                <label for="">Tercero(*)</label>
                                <div class="input-group">
                                    <input type="text" readonly class="form-control" name="cuenta_fin" v-model="tercero" placeholder="Cliente generico">
                                    <div class="input-group-prepend">
                                        <button class="input-group-text btn btn-primary text-white" @click="abrirModalT('terceros')" >...</button>
                                        <button class="input-group-text btn btn-danger" @click="quitar(3)"><i class="icon-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- <div class="col-md-2">
                                <div class="form-group">
                                    <label>Tarifario</label>
                                    <select v-model="id_tarifario" class="form-control" @change="cargarPreciosTarifarios(id_tarifario)">
                                        <option value="" disabled>--Seleccionar--</option>
                                        <option v-for="tarifario in arrayTarifario" :key="tarifario.id" :value="tarifario.id" v-text="tarifario.nombre"></option>
                                    </select>
                                </div>
                            </div> -->
                            <div class="col-md-5 form-group">
                                
                                <label>Mesa</label>
                                <select v-model="lugar" class="form-control">
                                    <option value="" disabled>--Seleccionar--</option>
                                    <option v-for="zonas in arrayZonas" :key="zonas.id" :value="zonas.id" v-text="zonas.zona"></option>
                                </select>
                                
                            </div>
                            <div class="col-md-12">
                                <div class="form-group" style="display:none;">
                                    <label>Detalle</label>
                                    <input type="text" class="form-control" v-model="detalle">
                                </div>
                            </div>
                            <!-- <div class="col-md-3" v-if="estado" style="display:none;">
                                <div class="form-group">
                                    <label>Estado</label>
                                    <select v-if="estado!=3" v-model="estado" class="form-control" 
                                   @change="if(estado==2) sugerirNumFactura();
                                     >
                                     
                                        <option value="1" v-if="estado==1" disabled selected>Creada</option>
                                        <option value="2">Registrada</option>
                                        <option value="3">Enviada</option>
                                        <option value="4">Anulada</option>
                                    </select>
                                     <input type="text" disabled v-else value="Enviada" class="form-control">
                                </div>
                            </div> -->
                        </div>
                        <div class="form-group row border">
                            <div class="col-md-6 col-sm-9">
                                <div class="form-group">
                                    <label>Artículo <span style="color:red;" v-show="idarticulo==0">(*Seleccione)</span></label>
                                    <div class="form-inline">
                                        <input v-if="id_tarifario!=0" type="text" class="form-control col-md-5 col-sm-5" v-model="codigo" @keyup.enter="buscarArticulo()" placeholder="Ingrese artículo">
                                        <input v-else type="text" class="form-control col-md-5 col-sm-5" disabled placeholder="Ingrese artículo">

                                        <button v-if="id_tarifario!=0" @click="abrirModal()" class="btn btn-primary">...</button>
                                        <button v-else class="btn btn-secondary">...</button>

                                        <input type="text" readonly class="form-control col-md-5 col-sm-5" v-model="articulo">
                                    </div>                                    
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-3">
                                <div class="form-group">
                                    <label>Precio</label>
                                    <input type="number" :min="1" :max="precio" disabled step="any" class="form-control" v-model="precio">
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-3">
                                <div class="form-group">
                                    <label>Cantidad <span style="color:red;" v-show="cantidad==0 && idarticulo!=0">(*Ingrese)</span></label>
                              
                                    <input type="number" v-if="stock!=0 && idarticulo!=0" :min="1" :max="stock" class="form-control" v-model="cantidad" @blur="function(){if  (cantidad>stock) {cantidad=stock;}else if(cantidad<=0){cantidad=1;}}">
                                    <input type="number" v-else disabled class="form-control" v-model="cantidad">
                                    <span  v-if="stock!=0" v-text="'Cantidad en stock: '+stock"></span>
                                </div>
                            </div>
                            <!-- <div class="col-md-3 col-sm-3">
                                <div class="form-group">
                                    <label>Descuento</label>
                                    <input type="number" v-if="idarticulo!=0" :min="0" :max="Math.round((precio*cantidad)/((iva/100)+1))" @blur="function(){
                                        if(descuento>Math.round((precio*cantidad)/((iva/100)+1))) descuento=Math.round((precio*cantidad)/((iva/100)+1))
                                    }" class="form-control" v-model="descuento" >
                                    <input type="number" v-else disabled class="form-control" v-model="descuento">
                                    <span v-if="idarticulo!=0" v-text="'Maximo descuento '+Math.round((precio*cantidad)/((iva/100)+1))"></span>
                                </div>
                            </div> -->
                            <div class="col-md-3 col-sm-3">
                                <div class="form-group">
                                    <button v-if="idarticulo!=0 && cantidad!=0" @click="agregarDetalle()" class="btn btn-success form-control btnagregar"><i class="icon-plus"></i></button>
                                    <button v-else class="btn btn-secondary form-control btnagregar"><i class="icon-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row border">
                            <div class="table-responsive col-md-12">
                                <table class="table table-bordered table-striped table-sm  table-responsive">
                                    <thead>
                                        <tr>
                                            <th>Opciones</th>
                                            <th>Artículo</th>
                                            <th>Precio</th>
                                            <th>Cantidad</th>
                                            <th>Descuento</th>
                                            <th>Iva</th>
                                            <th>Vr sin iva</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="arrayDetalle.length">
                                        <tr v-for="(detalle,index) in arrayDetalle" :key="detalle.id">
                                            <td>
                                          
                                                 <button v-if="rolusuario==1" @click="eliminarDetalle(index)" type="button" class="btn btn-danger btn-sm">
                                                    <i class="icon-close"></i>
                                                </button>
                                                <h3 class="text-secondary" title="Deshabilitado" v-else>
                                                    <i class="fa fa-times-circle"></i>
                                                </h3>
                                               
                                            </td>
                                            <td v-if="detalle.padre==null || detalle.padre==''" v-text="detalle.codigo+' - '+detalle.articulo+' - '+detalle.nom_presentacion">
                                            </td>
                                            <td v-else v-text="detalle.codigo+' - '+detalle.articulo+' - '+detalle.nom_presentacion+' (Presentación asociada)'">
                                            </td>
                                            <td style="text-align: right;">
                                                $ {{detalle.precio}}
                                            </td>
                                    
                                    
                                            <td style="text-align: right;">
                                                {{detalle.cantidad}}
                                            </td>
                                            <td>
                                                <input v-model="detalle.valor_descuento" v-if="tipoAccion2==1" type="number" value="0" class="form-control" :min="0" :max="((detalle.precio*detalle.cantidad)-detalle.valor_iva)" @blur="function(){
                                                        if(detalle.valor_descuento>((detalle.precio*detalle.cantidad)-detalle.valor_iva) || detalle.valor_descuento>0)
                                                        {detalle.valor_descuento=((detalle.precio*detalle.cantidad)-detalle.valor_iva);}
                                                        else{ detalle.valor_descuento=0;}
                                                    }
                                                ">

                                                <input v-model="detalle.valor_descuento" v-else-if="tipoAccion2==2" type="number" value="0" class="form-control" :min="0" :max="((detalle.precio*detalle.cantidad)-detalle.valor_iva)" @blur="function(){
                                                    if(detalle.valor_descuento>((detalle.precio*detalle.cantidad)-detalle.valor_iva) || detalle.valor_descuento>0) 
                                                    {detalle.valor_descuento=((detalle.precio*detalle.cantidad)-detalle.valor_iva);}
                                                    else{detalle.valor_descuento=0;}
                                                }">
                                            </td>
                                            <td style="text-align: right;">
                                                $ {{detalle.valor_iva=Math.round(parseFloat(detalle.precio*detalle.cantidad)-parseFloat((detalle.precio*detalle.cantidad)/((detalle.iva/100)+1)))}}
                                            </td>
                                            <td style="text-align: right;">
                                                $ {{detalle.valor_subtotal=Math.round(parseFloat((detalle.precio*detalle.cantidad)-detalle.valor_iva-detalle.valor_descuento))}}
                                            </td>
                                        </tr>
                                        <tr style="background-color: #CEECF5; text-align: right;">
                                            <td colspan="6" align="right"><strong>Total iva:</strong></td>
                                            <td>$ {{valor_iva=calcularTotalIva}}</td>
                                        </tr>
                                        <tr style="background-color: #CEECF5; text-align: right;">
                                            <td colspan="6" align="right"><strong>Total sin iva:</strong></td>
                                            <td>$ {{subtotal=calcularSubtotal}}</td>
                                        </tr>
                                        <tr style="background-color: #CEECF5; text-align: right;">
                                            <td colspan="6" align="right"><strong>Total Neto:</strong></td>
                                            <td>$ {{valor_final=calcularTotal}}</td>
                                        </tr>
                                        <tr style="background-color: #CEECF5; text-align: right;">
                                            <td colspan="6" align="right"><strong>Abono:</strong></td>
                                            <td v-if="tipoAccion2==1"><input v-model="abono" :min="0" :max="calcularTotal" type="number" class="form-control" @blur="
                                            if(abono>calcularTotal) {abono=calcularTotal;}"></td>

                                            <td v-else-if="tipoAccion2==2"><input v-model="abono" :min="abono2" :max="calcularTotal" type="number" class="form-control" @blur="
                                            if(abono>calcularTotal){ abono=calcularTotal;}else if(abono<abono2){ abono = abono2;}"></td>
                                        </tr>
                                        <tr style="background-color: #CEECF5; text-align: right;">
                                            <td colspan="6" align="right"><strong>Saldo:</strong></td>
                                            <td>$ {{saldo=calcularSaldo}}</td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr class="container-fluid">
                                            <td colspan="7">
                                                NO hay artículos agregados
                                            </td>
                                        </tr>
                                    </tbody>                                    
                                </table>
                            </div>
                        </div>
                        <div v-show="errorFacturacion" class="form-group row div-error">
                            <div class="text-center text-error">
                                <div v-for="error in errorMostrarMsjFacturacion" :key="error" v-text="error">

                                </div>
                            </div>
                        </div>
                        <div class="form-group row" v-if="tipoAccion2==1">
                            <div class="col-md-12">
                                <!-- <button type="button" @click="ocultarDetalle()" class="btn btn-secondary">Cerrar</button> -->
                                <button type="button" class="btn btn-primary" v-if="tipoAccion2==1" @click="registrarFacturacion()">Crear</button>
                            </div>
                        </div>
                        <div class="form-group row" v-if="tipoAccion2==2">
                            <div class="col-md-12">
                                <button type="button" @click="ocultarDetalle()" class="btn btn-secondary">Cerrar</button>
                                <button type="button" v-if="estado==1" class="btn btn-primary" @click="actualizarFacturacion()">Actualizar</button>
                                <button type="button" v-if="estado==1" class="btn btn-primary" @click="cambiarEstadoFacturacion(facturacion_id,'registrar')">Registrar</button>
                            </div>
                        </div>
                    </div>
                    </div>

                    <div class="card" v-show="listado==1">
                        <div class="card-header">
                           
                            <h6> <i class="fa fa-align-justify"></i> Facturación</h6>
                        </div>                        
                        <div class="card-body">
                            <!-- Mostrar listado de tablas -->
                            <div>
                                <table class="table table-bordered table-striped table-sm  table-responsive table-earning ">
                                    <thead>
                                        <tr>
                                            <!-- <th>#</th> -->
                                            <th>N° factura</th>
                                            <th>Ver / Imprimir</th>
                                            <th>Tercero</th>
                                            <th>Fecha</th>
                                            <th>Subtotal</th>
                                            <th>Descuento</th>
                                            <th>Lugar</th>
                                            <th>Iva</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="permisosUser.leer && arrayFacturacion.length">
                                        <tr v-for="facturacion in arrayFacturacion" :key="facturacion.id" style="text-align: right;">
                                            <td v-text="facturacion.id"></td>
                                            <td v-if="facturacion.num_factura" v-text="facturacion.num_factura"></td>
                                            <td v-else >
                                                <!-- <i class="icon-eye"></i> -->
                                                <!-- <button type="button" @click="verFacturacion(facturacion.id)" class="btn btn-success btn-sm" title="Ver factura">
                                                    <i class="icon-eye"></i>
                                                </button> -->
                                                <!-- <button type="button" @click="imprimirTicketFacturacion(facturacion.id);" class=" btn btn-primary btn-sm" title="imprimir">
                                                <i class="icon-printer"></i> 
                                                </button> -->
                                                <button type="button" @click="abrirModalImpresion(facturacion.id);" class=" btn btn-primary btn-sm" title="imprimir">
                                                <i class="icon-printer"></i> 
                                                </button>
                                            </td>
                                            <td v-text="facturacion.nom_tercero"></td>
                                            <td v-text="facturacion.fecha"></td>
                                            <td v-text="facturacion.subtotal"></td>
                                            <td v-text="facturacion.descuento"></td>
                                            <td v-text="facturacion.nom_lugar"></td>
                                            <td v-text="facturacion.valor_iva"></td>
                                            <td v-text="facturacion.total"></td>
                                            <td v-if="facturacion.estado==1"><span>Activa</span></td>
                                            <td v-else-if="facturacion.estado==2"><span>Registrada</span></td>
                                            <td v-else-if="facturacion.estado==3"><span>Enviada</span></td>
                                            <td v-else-if="facturacion.estado==4"><span>Anulada</span></td>
                                            <td>
                                                <template>
                                                    <button href="#" @click="mostrarDetalle('facturacion','actualizar',facturacion)" class="btn btn-success btn-sm" v-if="permisosUser.actualizar  && facturacion.estado==1" title="Actualizar">
                                                        <i class="icon-pencil"></i>
                                                    </button>
                                                    <button href="#" class="btn btn-secondary btn-sm" v-else title="Actualizar (Deshabilitado)">
                                                        <i class="icon-pencil"></i>
                                                    </button>
                                                </template>

                                                <!-- Botones de registrar -->
                                                <template> 
                                                    <button type="button" v-if="permisosUser.actualizar && facturacion.estado==1" class="btn btn-primary text-white" @click="cambiarEstadoFacturacion(facturacion.id,'registrar')" title="Registrar">
                                                        <i class="fa fa-registered"></i> 
                                                        <!-- Registrar -->
                                                    </button>

                                                    <button type="button" v-else-if="permisosUser.actualizar && facturacion.estado==2" @click="pdfFormato(facturacion.id)" class="btn btn-info btn-sm" title="PDF">
                                                        <i class="icon-doc"></i> 
                                                        <!-- Descargar PDF -->
                                                    </button>

                                                    <button type="button" v-else class="btn btn-primary text-white" title="Registrar (Deshabilitado)">
                                                        <i class="fa fa-registered"></i> 
                                                        <!-- Registrar -->
                                                    </button>
                                                </template>

                                                <!--Botones de anular -->

                                                <template v-if="permisosUser.anular && facturacion.estado==1">
                                                    <button type="button" class="btn btn-primary text-white" @click="cambiarEstadoFacturacion(facturacion.id,'anular')" v-if="facturacion.estado!=4 && facturacion.estado!=3" title="Anular">
                                                        <i class="icon-trash"></i> 
                                                        <!-- Anular -->
                                                    </button>
                                                    <button type="button" class="btn btn-primary text-white" v-else title="Anular (Deshabilitado)">
                                                        <i class="icon-trash"></i> 
                                                        <!-- Anular (Deshabilitado) -->
                                                    </button>
                                                </template>
                                                <template v-else>
                                                    <button type="button" class="btn btn-primary text-white" title="Anular (Deshabilitado)">
                                                        <i class="icon-trash"></i> 
                                                        <!-- Anular (Deshabilitado) -->
                                                    </button>
                                                </template>                                              
                                            </td>
                                        </tr>                                
                                    </tbody>
                                    <tbody v-else>
                                        <tr><td colspan="11">No hay registros para mostrar</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Fin mostrar listado de tablas -->


                        </div>

                    </div>

                    <!-- VerFactura -->
                    <div class="card" v-show="listado==2">

                         <div class="card-header">
                           
                            <h6> <i class="fa fa-align-justify"></i> Factura</h6>
                        </div>  

                        <div class="card-body">
                            <div class="form-group row border">
                                <!--ppp-->
                                <div class="col-md-2">
                                    <label v-text="'Fecha: '+fecha" class="control-label"></label>
                                </div>
                                <div class="col-md-2">
                                    <label v-text="'N° factura: '+num_factura" v-if="num_factura!=null"></label>
                                    <label v-text="'N° factura: N/A'" v-else></label>
                                </div>
                                <div class="col-md-2">
                                    <label v-text="'Tercero: '+tercero"></label>
                                </div>
                                <div class="col-md-2">
                                    <label v-text="'Detalle: '+detalle" v-if="detalle!='' && detalle!=null"></label>
                                    <label v-text="'Detalle: N/A'" v-else></label>
                                </div>
                                <div class="col-md-2">
                                    <label v-text="'Lugar: '+lugar" v-if="lugar!=''"></label>
                                    <label v-text="'Lugar: N/A'" v-else></label>
                                </div>
                                <div class="col-md-2">
                                    <label v-text="'Estado: Activa'" class="btn-warning" v-if="estado==1" style="font-size: 100%;"></label>
                                    <label v-text="'Estado: Registrada'" class="btn-success" v-else-if="estado==2" style="font-size: 100%;"></label>
                                    <label v-text="'Estado: Enviada'" class="btn-primary" v-else-if="estado==3" style="font-size: 100%;"></label>
                                    <label v-text="'Estado: Anulada'" class="btn-danger" v-else-if="estado==4" style="font-size: 100%;"></label>
                                    <label v-text="'Estado: N/A'" v-else></label>
                                </div>
                            </div>
                            <div class="form-group row border">
                                <!--ppp-->
                                <table class="table table-bordered table-striped table-sm  table-responsive">
                                    <thead>
                                        <tr>
                                            <th>Artículo</th>
                                            <th>Precio</th>
                                            <th>Cantidad</th>
                                            <th>Descuento</th>
                                            <th>Iva</th>
                                            <th>Vr sin iva</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="arrayDetalleT.length">
                                        <tr v-for="detalle in arrayDetalleT" :key="detalle.id">
                                            <td v-if="detalle.padre==null || detalle.padre==''" v-text="detalle.codigo+' - '+detalle.articulo+' - '+detalle.nom_presentacion">
                                            </td>
                                            <td v-else v-text="detalle.codigo+' - '+detalle.articulo+' - '+detalle.nom_presentacion+' (Presentación asociada)'">
                                            </td>
                                            <td style="text-align: right;">
                                                $ {{detalle.precio}}
                                            </td>
                                            <td style="text-align: right;">
                                                {{detalle.cantidad}}
                                            </td>
                                            <td style="text-align: right;">
                                                $ {{detalle.valor_descuento}}
                                            </td>
                                            <td style="text-align: right;">
                                                $ {{detalle.valor_iva}}
                                            </td>
                                            <td style="text-align: right;">
                                                $ {{detalle.valor_subtotal}}
                                            </td>
                                        </tr>
                                        <tr style="background-color: #CEECF5; text-align: right;">
                                            <td colspan="5" align="right"><strong>Total iva:</strong></td>
                                            <td>$ {{valor_iva}}</td>
                                        </tr>
                                        <tr style="background-color: #CEECF5; text-align: right;">
                                            <td colspan="5" align="right"><strong>Total sin iva:</strong></td>
                                            <td>$ {{subtotal}}</td>
                                        </tr>
                                        <tr style="background-color: #CEECF5; text-align: right;">
                                            <td colspan="5" align="right"><strong>Total Neto:</strong></td>
                                            <td>$ {{total}}</td>
                                        </tr>
                                        <tr style="background-color: #CEECF5; text-align: right;">
                                            <td colspan="5" align="right"><strong>Abono:</strong></td>
                                            <td>$ {{abono}}</td>
                                        </tr>
                                        <tr style="background-color: #CEECF5; text-align: right;">
                                            <td colspan="5" align="right"><strong>Saldo:</strong></td>
                                            <td>$ {{saldo}}</td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr class="container-fluid">
                                            <td colspan="7">
                                                NO hay artículos agregados
                                            </td>
                                        </tr>
                                    </tbody>                                    
                                </table>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <button type="button" @click="ocultarDetalle()" class="btn btn-secondary">Cerrar</button>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!--Fin Ver/editar Factura -->

                  
                </div>
                <aside class="col-md-7">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title" v-text="tituloModal"></h4>
                            Añadir productos
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-sm-12 col-md-5 pb-sm-1">
                                    <label class="control-label col-sm-3 col-md-4 float-left"><i class="fa fa-search"></i> Buscar</label>
                                    <input type="text" v-model="buscarA" @keyup="listarArticulo(buscarA,criterioA,buscarCategoriaA)" class="form-control col-sm-9 col-md-8 float-right" placeholder="Texto a buscar">
                                </div>
                                <div class="col-sm-9 col-md-5">
                                    <label class="control-label col-sm-4 col-md-4 float-left"><i class="fa fa-search"></i> Categoría</label>
                                    <select class="form-control col-sm-8 col-md-8 float-right" v-model="buscarCategoriaA" @change="listarArticulo(buscarA,criterioA,buscarCategoriaA)">
                                        <option value="" disabled>Seleccione</option>
                                        <option v-for="categoria in arrayCategoria2" :key="categoria.id" :value="categoria.id" v-text="categoria.nombre"></option>
                                    </select>
                                </div>
                                <div class="col-sm-3 col-md-2 text-center">
                                    <button v-if="tipo_vista_articulo==1" type="button" class="btn btn-info"><i class="fa fa-list"></i></button>
                                    <button v-else type="button" class="btn btn-secondary" @click="tipo_vista_articulo=1"><i class="fa fa-list"></i></button>
                                    <button v-if="tipo_vista_articulo==2" type="button" class="btn btn-info"><i class="fa fa-th-large"></i></button>
                                    <button v-else type="button" class="btn btn-secondary" @click="tipo_vista_articulo=2"><i class="fa fa-th-large"></i></button>
                                </div>
                            </div>
                            <div v-if="tipo_vista_articulo==1" class="table-responsive" style="display: block;height: 35em;max-height: 35em;overflow-y: auto;">
                                <table class="table table-bordered table-striped table-sm  table-responsive">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>IMG</th>
                                            <th>Nombre</th>
                                            <th>Modelo contable</th>
                                            <th>Categoría</th>
                                            <th>Precio Venta</th>
                                            <th>Stock</th>
                                            <th>Cant</th>
                                            <th>Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="arrayArticulo.length">
                                        <tr v-for="(articulo, index) in arrayArticulo" :key="index">
                                            <td v-if="articulo.padre==''" v-text="articulo.codigo"></td>
                                            <td v-else></td>
                                            <td>
                                                <img v-if="`${articulo.img}`!='default.png'" :src="`${ruta}/Empresas/${articulo.id_empresa}_empresa/ImgProductos/${articulo.img}`" height="30" width="30">

                                                <img v-else :src="`${ruta}/Empresas/${articulo.img}`" height="30" width="30">
                                            </td>
                                            <td>
                                                <span v-text="articulo.nombre"></span>
                                                <span v-if="articulo.id_presentacion!=null" v-text="' - '+articulo.nom_presentacion"></span>
                                                <span v-else> - N/A presentacion</span>
                                                <span v-if="articulo.talla!=null" v-text="' - '+articulo.talla"></span>
                                                <span v-else> - N/A talla</span>
                                            </td>
                                            <td v-text="articulo.nom_modelo_contable"></td>
                                            <td v-text="articulo.nom_categoria"></td>
                                            <td v-text="articulo.precio_venta"></td>
                                            <td v-if="articulo.padre!=''" v-text="parseInt(articulo.stock/articulo.unidades)"></td>
                                            <td v-else v-text="articulo.stock"></td>
                                            <td><input type="number" v-model="articulo.cant">
                                            <input type="hidden" v-model="articulo.observaciones" value=""></td>
                                            <td v-if="articulo.cant">
                                                <button type="button" v-if="articulo.cant!=0 && articulo.cant!=''" @click="agregarDetalleModal(articulo),articulo.cant=''" class="btn btn-success btn-sm">
                                                    <i class="icon-check"></i>
                                                </button>
                                                <button type="button" v-else class="btn btn-secondary btn-sm">
                                                    <i class="icon-check"></i>
                                                </button>
                                            </td>
                                            <td v-else>
                                                <button type="button" class="btn btn-secondary btn-sm">
                                                    <i class="icon-check"></i>
                                                </button>
                                            </td>
                                        </tr>                                
                                    </tbody>
                                    <tbody v-else>
                                        <tr colspan="10">No hay registros para mostrar</tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-if="tipo_vista_articulo==2" class="container">
                                <div v-for="(articulo, index) in arrayArticulo" :key="index" @click="abrirModalCantidadArticulo(articulo)" class="col-sm-6 col-md-4 p-sm-2 mosaico">
                                    <div class="border col-md-12" style="height: 100%;">
                                        <div class="text-center py-md-2">
                                            <img v-if="`${articulo.img}`!='default.png'" :src="`${ruta}/Empresas/${articulo.id_empresa}_empresa/ImgProductos/${articulo.img}`" class="img-responsive img-thumbnail">

                                            <img v-else :src="`${ruta}/Empresas/${articulo.img}`" class="img-responsive img-thumbnail">
                                        </div>
                                        <div class="text-center col-md-12">
                                            <h6 v-text="articulo.nombre"></h6>
                                            <span v-if="articulo.id_presentacion!=null" v-text="articulo.nom_presentacion"></span>
                                            <span v-else>N/A presentacion</span>
                                        </div>
                                        <div class="text-center col-md-12 pb-md-1">
                                            <span v-text="'Precio: '+articulo.precio_venta"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                            <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarPersona()">Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarPersona()">Actualizar</button>
                        </div>
                    </div>
                </aside>
            </div>


            <!-- Modales -->

            <!-- Modal Impresoras -->
            <div class="modal fade" tabindex="-1" :class="{'mostrar' : mostrarModImp}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Imprimir ticket</h4>
                            <button type="button" class="close" @click="cerrarModalImpresion()" aria-label="Close">
                            <span aria-hidden="true" title="Cerrar">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="col-12">
                                    <h6 class="col-12 text-center">Por favor, seleccionar Impresora</h6>
                                    <div class="form-group">
                                        <select class="custom-select" id="" v-model="id_impresora">
                                            <option disabled>--Seleccionar impresora--</option>
                                            <option v-for="(imp, index) in arrayImpresora" :key="index" :value="imp.id"> {{imp.nombre_impresora}} </option>
                                        </select>
                                    </div>
                                </div>

                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" @click="cerrarModalImpresion()">Cerrar</button>
                            <button type="button" class="btn btn-success" @click="imprimirTicketFacturacion()">Imprimir</button>
                            
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- Fin modal Impresoras -->

             <!--Inicio del modal agregar/actualizar-->
            <div class="modal fade" tabindex="-1"  :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-xl" role="document">
                    <div class="modal-content  mt-4">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModal"></h4>
                            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                              <span aria-hidden="true" title="Cerrar">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <div class="col-sm-12 col-md-5 pb-sm-1">
                                    <label class="control-label col-sm-3 col-md-4 float-left"><i class="fa fa-search"></i> Buscar</label>
                                    <input type="text" v-model="buscarA" @keyup="listarArticulo(buscarA,criterioA,buscarCategoriaA)" class="form-control col-sm-9 col-md-8 float-right" placeholder="Texto a buscar">
                                </div>
                                <div class="col-sm-9 col-md-5">
                                    <label class="control-label col-sm-4 col-md-4 float-left"><i class="fa fa-search"></i> Categoría</label>
                                    <select class="form-control col-sm-8 col-md-8 float-right" v-model="buscarCategoriaA" @change="listarArticulo(buscarA,criterioA,buscarCategoriaA)">
                                        <option value="" disabled>Seleccione</option>
                                        <option v-for="categoria in arrayCategoria2" :key="categoria.id" :value="categoria.id" v-text="categoria.nombre"></option>
                                    </select>
                                </div>
                                <div class="col-sm-3 col-md-2 text-center">
                                    <button v-if="tipo_vista_articulo==1" type="button" class="btn btn-info"><i class="fa fa-list"></i></button>
                                    <button v-else type="button" class="btn btn-secondary" @click="tipo_vista_articulo=1"><i class="fa fa-list"></i></button>
                                    <button v-if="tipo_vista_articulo==2" type="button" class="btn btn-info"><i class="fa fa-th-large"></i></button>
                                    <button v-else type="button" class="btn btn-secondary" @click="tipo_vista_articulo=2"><i class="fa fa-th-large"></i></button>
                                </div>
                            </div>
                            <div v-if="tipo_vista_articulo==1" class="table-responsive" style="display: block;height: 35em;max-height: 35em;overflow-y: auto;">
                                <table class="table table-bordered table-striped table-sm  table-responsive">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>IMG</th>
                                            <th>Nombre</th>
                                            <th>Modelo contable</th>
                                            <th>Categoría</th>
                                            <th>Precio Venta</th>
                                            <th>Stock</th>
                                            <th>Cant</th>
                                            <th>Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="arrayArticulo.length">
                                        <tr v-for="(articulo, index) in arrayArticulo" :key="index">
                                            <td v-if="articulo.padre==''" v-text="articulo.codigo"></td>
                                            <td v-else></td>
                                            <td>
                                                <img v-if="`${articulo.img}`!='default.png'" :src="`${ruta}/Empresas/${articulo.id_empresa}_empresa/ImgProductos/${articulo.img}`" height="30" width="30">

                                                <img v-else :src="`${ruta}/Empresas/${articulo.img}`" height="30" width="30">
                                            </td>
                                            <td>
                                                <span v-text="articulo.nombre"></span>
                                                <span v-if="articulo.id_presentacion!=null" v-text="' - '+articulo.nom_presentacion"></span>
                                                <span v-else> - N/A presentacion</span>
                                                <span v-if="articulo.talla!=null" v-text="' - '+articulo.talla"></span>
                                                <span v-else> - N/A talla</span>
                                            </td>
                                            <td v-text="articulo.nom_modelo_contable"></td>
                                            <td v-text="articulo.nom_categoria"></td>
                                            <td v-text="articulo.precio_venta"></td>
                                            <td v-if="articulo.padre!=''" v-text="parseInt(articulo.stock/articulo.unidades)"></td>
                                            <td v-else v-text="articulo.stock"></td>
                                            <td><input type="number" v-model="articulo.cant">
                                            <input type="hidden" v-model="articulo.observaciones" value=""></td>
                                            <td v-if="articulo.cant">
                                                <button type="button" v-if="articulo.cant!=0 && articulo.cant!=''" @click="agregarDetalleModal(articulo),articulo.cant=''" class="btn btn-success btn-sm">
                                                    <i class="icon-check"></i>
                                                </button>
                                                <button type="button" v-else class="btn btn-secondary btn-sm">
                                                    <i class="icon-check"></i>
                                                </button>
                                            </td>
                                            <td v-else>
                                                <button type="button" class="btn btn-secondary btn-sm">
                                                    <i class="icon-check"></i>
                                                </button>
                                            </td>
                                        </tr>                                
                                    </tbody>
                                    <tbody v-else>
                                        <tr colspan="10">No hay registros para mostrar</tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-if="tipo_vista_articulo==2" class="container" style="display: block;height: 36em;max-height: 36em;overflow-y: auto;">
                                <div v-for="(articulo, index) in arrayArticulo" :key="index" @click="abrirModalCantidadArticulo(articulo)" class="col-sm-6 col-md-3 p-sm-2 p-md-1 mosaico">
                                    <div class="border col-md-12" style="height: 100%;">
                                        <div class="text-center py-md-2">
                                            <img v-if="`${articulo.img}`!='default.png'" :src="`${ruta}/Empresas/${articulo.id_empresa}_empresa/ImgProductos/${articulo.img}`" class="img-responsive img-thumbnail">

                                            <img v-else :src="`${ruta}/Empresas/${articulo.img}`" class="img-responsive img-thumbnail">
                                        </div>
                                        <div class="text-center col-md-12">
                                            <h6 v-text="articulo.nombre"></h6>
                                            <span v-if="articulo.id_presentacion!=null" v-text="articulo.nom_presentacion"></span>
                                            <span v-else>N/A presentacion</span>
                                        </div>
                                        <div class="text-center col-md-12 pb-md-1">
                                            <span v-text="'Precio: '+articulo.precio_venta"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                            <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarPersona()">Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarPersona()">Actualizar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Fin del modal articulos-->

             <!-- Modal cantidad por articulo-->
            <div class="modal fade" tabindex="-1" :class="{'mostrar' : modalCantidadArticulo}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModalCantidadArticulo"></h4>
                            <button type="button" class="close" @click="cerrarModalCantidadArticulo()" aria-label="Close">
                                <span aria-hidden="true" title="Cerrar">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 pb-sm-2 pb-md-1">
                                        <div class="col-sm-10 col-md-10 float-left">
                                            <label class="col-sm-3 col-md-4 float-left">Cant</label>
                                            <input type="number" class="form-control float-right col-sm-9 col-md-8" v-model="cantidadArticulo">
                                        </div>
                                        <div class="col-sm-2 col-md-2 float-right">
                                            <button v-if="cantidadArticulo!=0 && cantidadArticulo!=''" type="button" @click="agregarDetalleModalCantidadArticulo()" class="btn btn-success btn-sm float-right">
                                                <i class="icon-check"></i>
                                            </button>
                                            <button v-else type="button" class="btn btn-secondary btn-sm float-right">
                                                <i class="icon-check"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <div class="col-sm-4 col-md-5 float-right">
                                            <label class="col-sm-6 col-md-6 float-left">Saldo parcial</label>
                                            <label class="col-sm-6 col-md-6 float-right" v-text="precioArticulo"></label>
                                        </div>
                                        <div class="col-sm-4 col-md-5 float-left">
                                            <label class="col-sm-6 col-md-6 float-left">Stock</label>
                                            <label class="col-sm-6 col-md-6 float-right" v-text="stockCantidadArticulo"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModalCantidadArticulo()">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- fin modal cantidad por articulo -->

            <!-- Modal busqueda tercero-->
                <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal2}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-primary modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" v-text="tituloModal2"></h4>
                                <button type="button" class="close" @click="cerrarModalT()" aria-label="Close">
                                    <span aria-hidden="true" title="Cerrar">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group row">
                                    <div style="max-width: 120px !important;" class="col-md-2   ">
                                        <label style='margin-top: 3px; '><b>Tercero</b></label>                                
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="cta_busq" v-model="terc_busq" @keyup="buscarTercero()">
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-sm  table-responsive">
                                        
                                            <tr><th>Documento</th><th>Nombre</th><th style="    width: 35px;">-</th></tr>
                                        
                                            <tr v-for="tercero in arrayTerceros" :key="tercero.id">
                                                <td v-text="tercero.num_documento"></td>
                                                <td v-if="tercero.nombre && !tercero.nombre1">{{ tercero.nombre }}  </td>
                                                <td v-else>{{ tercero.nombre1 + ' ' + validarUnder(tercero.nombre2)+' '+tercero.apellido1+' '+validarUnder(tercero.apellido2) }} </td>
                                                <td>
                                                    <button type="button" style=" margin-right: -8px;" @click="cargarTercero(tercero)" class="btn btn-success btn-sm" title='Ver formato'>
                                                        <i class="icon-check"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- fin modal busqueda de terceros -->
        </div>

    </main>
    
</template>
<script>
    import vSelect from 'vue-select';
    export default {

        props:['ruta'],
        data(){
            return{

                permisosUser : {
                    'leer' : 1,
                    'escribir' : 1,
                    'crear' : 1,
                    'actualizar' : 1,
                    'anular' : 1,
                },
                // Vistas de interfaz
                listado :0,

                // Facturacion
                arrayFacturacion : [],
                arrayFacturacionT : [],
                arrayPendientes :[],
                arrayTarifario :[],
                arrayZonas : [],
                arrayDetalle:[],
                arrayDetalleT:[],
                arrayImpresora:[],
                arrayCategoria2 : [],
                arrayArticulo: [],

                rolusuario :0,
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },

                // Cajas y cierre de caja
                id_caja_facturacion : 0,
                id_cierre_caja_facturacion : 0,
                nom_caja_cierre_facturacion : '',
                cierre_caja_id : 0,
                vr_inicial_cierre : 0,
                obs_inicial_cierre : '',

                // Nueva factura
                estado: 0,
                cambiarEstado: 0,
                fecha : '',
                num_factura:0,
                tercero :'',
                id_tarifario : 2,
                lugar : '',
                detalle:'',
                idarticulo: 0,
                articulo: '',
                precio: 0,
                cantidad:1,
                stock : 0,
                codigo: '',
                
                saldo:0.0,
                detalle:'',
                descuento:0,
                fec_registra:'',
                fec_envia:'',
                fec_anula:'',
            
                

                // Añadir articulos
                modal : 0,
                tituloModal : '',
                tipo_vista_articulo:2,
                criterioA:'nombre',
                buscarA: '',
                buscarCategoriaA : '',

                tipoAccion : 0,
                // Creacion/Actualizacion de factura
                tipoAccion2 : 1,

                // Errores formulario
                errorFacturacion : 0,
                errorMostrarMsjFacturacion : [],

                
                //Impresion de factura en ticket
                mostrarModImp : 0,
                id_impresora: 0, 
                nombre_impresora : '',
                id_factura_imprimir:0,

                // modal cantidad articulo
                modalCantidadArticulo : 0,
                tituloModalCantidadArticulo : '',
                cantidadArticulo : 0,
                precioArticulo : 0,
                stockCantidadArticulo : 0,
                saldoParcialCantidadArticulo : false,
                arrayInfoArticuloModalCantidad : [],
                accionCodigoBarras : 0,
                abono:0.0,
                abono2:0.0,

                // Variables filtro
                numFacturaFiltro : '',
                estadoFiltro : '',
                idTerceroFiltro : '',
                terceroFiltro : '',
                desdeFiltro : '2019-01-01',
                hastaFiltro : '',
                ordenFiltro : '',
                idVendedorFiltro : '',
                vendedorFiltro : '',

                // variables modal buscar tercero
                modal2 : '',
                tercero :'',
                id_tercero : '',
                tituloModal2 : '',
                buscar : '',   
                ctn_ban : 0,       
                tipo_cta : '',    
                cta_busq : '',  
                terc_busq : '',
                tipo_ingreso: '',
                arrayTerceros : [],


            }
        },
        components: {
            vSelect
        },
        methods:{      
            listarPendientes (){
                let me=this;
                var url= this.ruta +'/facturacion/listarPendientes';
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayPendientes = respuesta.facturacion;
                })
                .catch(function (error) {
                    console.log(error);
                });
            }, 
            listarFacturacion (page,numFacturaFiltro,estadoFiltro,idTerceroFiltro,ordenFiltro,desdeFiltro,hastaFiltro,idVendedorFiltro){
                let me=this;
                var url= this.ruta +'/facturacion?page=' + page + '&numFacturaFiltro='+ numFacturaFiltro + '&estadoFiltro='+ estadoFiltro + '&idTerceroFiltro='+ idTerceroFiltro + '&ordenFiltro='+ ordenFiltro + '&desdeFiltro='+ desdeFiltro + '&hastaFiltro='+ hastaFiltro + '&idVendedorFiltro='+ idVendedorFiltro+'&id_cierre_caja='+me.id_cierre_caja_facturacion;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayFacturacion = respuesta.facturacion.data;
                    me.rolusuario = respuesta.idrol;
                    me.pagination= respuesta.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            listarCajas(){
                let me=this;
                var url= this.ruta +'/cierres_caja/validarCierreCajaWeb';
                axios.get(url).then(function (response) {
                    console.log(response.data)
                    var respuesta= response.data;
                    var ban = respuesta.ban;
                    me.arrayCierresXCajas = respuesta.cierres_cajas;

                    if(ban==0 || ban==1)
                    {                  
                        Swal.fire({                  
                            type: 'error',
                            title: 'Abrir caja',
                            position: 'center',
                            showConfirmButton: false,
                            timer: 2000
                        })
                    }
                    else
                    {
                        if(ban==3)
                        {
                            me.id_caja_facturacion = me.arrayCierresXCajas[0]['id'];
                            me.id_cierre_caja_facturacion = me.arrayCierresXCajas[0]['id'];
                            me.nom_caja_cierre_facturacion = me.arrayCierresXCajas[0]['nombre'];
                            me.cierre_caja_id = me.arrayCierresXCajas[0]['id'];
                            me.id_caja_cierre = me.arrayCierresXCajas[0]['id_caja'];                    
                            me.vr_inicial_cierre = me.arrayCierresXCajas[0]['vr_inicial'];
                            me.obs_inicial_cierre = me.arrayCierresXCajas[0]['obs_inicial'];
                            
                            // me.listarFacturacion();
                            me.listarPendientes();
                        }

                        if(ban==2)
                        {
                            Swal.fire({                   
                                type: 'error',
                                title: 'Abrir caja',
                                position: 'center',
                                showConfirmButton: false,
                                timer: 1700
                            })
                        }
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            listarImpresora (){
                let me=this;
                var url= this.ruta +'/impresora';
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayImpresora = respuesta.impresoras.data;                    
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            listarArticulo (buscar,criterio,categoria){
                let me=this;
                var var_categoria='';
                if(categoria && categoria!=''){var_categoria='&categoria='+categoria;}
                var url= this.ruta +'/articulo/listarArticulo?buscar='+ buscar + '&criterio='+ criterio+var_categoria+'&id_tarifario='+me.id_tarifario;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayArticulo = [];
                    me.arrayArticulo = respuesta.articulos;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            sugerirNumFactura(){
                    let me=this;
                    var url= this.ruta +'/facturacion/buscarNumFacturaSugerida';
                    
                    axios.get(url).then(function (response) {
                        var respuesta= response.data.facturacion;
                        
                        if(respuesta.length)
                        {
                            me.num_factura = parseInt(respuesta[0].num_factura)+1;
                        }
                        else
                        {
                            me.num_factura = 1;
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            selectZonas(){
                let me=this;
                var url= this.ruta + '/zona/selectZona';
                axios.get(url).then(function (response) {
                        
                    var respuesta= response.data;
                    me.arrayZonas = respuesta.zona;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            selectTarifarios(){
                let me=this;
                var url= this.ruta + '/con_tarifario/selectConTarifario2';
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayTarifario = respuesta.tarifario;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            selectCategoria2(){
                let me=this;
                var url= this.ruta + '/categoria/selectCategoria';
                axios.get(url).then(function (response) {
                        
                    var respuesta= response.data;
                    me.arrayCategoria2 = respuesta.categorias;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            mostrarDetalle(modelo, accion, data=[]){
                let me=this;
                
                switch(modelo){
                    case 'facturacion':{
                        me.listado=0;
                        switch(accion){
                            case 'registrar':{
                                // me.sugerirNumFactura();
                                me.tipoAccion2 = 1;
                                me.facturacion_id=0;
                                me.num_factura=null;
                                me.id_tercero=0;
                                me.tercero = '';
                                me.tercero_facturacion='';
                                me.fec_edita='';
                                me.subtotal=0.0;
                                me.valor_iva=0.0;
                                me.total=0.0;
                                me.abono=0.0;
                                me.saldo=0.0;
                                me.detalle='';
                                me.lugar = '',
                                me.descuento=0.0;
                                me.fec_registra='';
                                me.fec_envia='';
                                me.fec_anula='';

                                me.arrayArticulo=[];
                                me.arrayDetalle=[];
                                // me.arrayTerceros=[];
                                me.listarFacturacion(1,'','','','','','','');
                                break;
                            };
                            case 'actualizar':{
                                me.tipoAccion2 = 2;
                                me.facturacion_id=data['id'];
                                me.num_factura=data['num_factura'];
                                me.id_tercero=data['id_tercero'];
                                me.tercero=data['nom_tercero'];
                                me.fec_edita=me.fechaHoraActual;
                                me.subtotal=data['subtotal'];
                                me.valor_iva=data['valor_iva'];
                                me.total=data['total'];
                                me.abono=data['abono'];
                                me.abono2=data['abono'];
                                me.saldo=data['saldo'];
                                me.detalle=data['detalle'];
                                me.lugar = data['lugar'];
                                // me.descuento=data['descuento'];
                                me.fec_registra=data['fec_registra'];
                                me.fec_envia=data['fec_envia'];
                                me.fec_anula=data['fec_anula'];
                                me.fecha =data['fecha'];
                                me.id_tarifario =data['id_tarifario'];
                                me.estado = data['estado'];
                                

                                me.arrayArticulo=[];
                                me.arrayTerceros=[];
                                me.arrayDetalle=[]
                                // me.listarFacturacion(1,'','','','','','','');)
                                me.listarDetalle(data['id']);
                                break;
                            };
                        }
                        me.selectZonas();
                        me.selectTarifarios();
                        break;
                    }
                    case "cierres_caja":
                    {
                        switch(accion){
                            case 'registrar':
                            {
                                this.arrayCierresUsuario = [];
                                this.modalCierreCaja = 1;
                                this.tituloModalCierre = 'Abrir caja';
                                this.id_caja_cierre='';
                                this.vr_inicial_cierre=0;
                                this.obs_inicial_cierre = '';
                                this.vr_gastos_cierre = 0;
                                this.obs_gastos_cierre = '';
                                this.vr_software_cierre = 0;
                                this.vr_final_cierre = 0;
                                this.cierre_caja_id = 0;
                                this.tipoAccionCierre = 1;
                                break;
                            }
                            case 'cerrar_caja':
                            {
                                    
                                if(data.length!=0)
                                {
                                    this.arrayCierresUsuario = [];
                                    this.modalCierreCaja=1;
                                    this.tituloModalCierre='Cerrar caja';
                                    this.tipoAccionCierre=2;
                                    this.cierre_caja_id=data['id'];
                                    this.id_caja_cierre = data['id_caja'];
                                    this.vr_inicial_cierre = data['vr_inicial'];
                                    this.obs_inicial_cierre = data['obs_inicial'];
                                    this.vr_gastos_cierre = data['vr_gastos'];
                                    this.obs_gastos_cierre = data['obs_gastos'];
                                    this.vr_software_cierre = 0;
                                    this.vr_final_cierre = data['vr_final'];
                                }
                                else
                                {
                                    this.arrayCierresUsuario = [];
                                    this.modalCierreCaja=1;
                                    this.tituloModalCierre='Cerrar caja';
                                    this.tipoAccionCierre=2;
                                }
                                break;
                            }
                            case 'listar_cierres':
                            {
                                console.log(data);
                                Swal.fire({
                                title: 'ERROR!',
                                text: "Este usuario tiene una caja abierta desde hace mas de 24 horas",
                                type: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Ok!'
                                }).then((result) => {
                                    if(result.value){
                                        this.arrayCierresUsuario.push({
                                            created_at : data['created_at'],
                                            estado : data['estado'],
                                            id : data['id'],
                                            id_caja : data['id_caja'],
                                            nombre : data['nombre'],
                                            obs_gastos : data['obs_gastos'],
                                            obs_inicial : data['obs_inicial'],
                                            vr_final : data['vr_final'],
                                            vr_gastos : data['vr_gastos'],
                                            vr_inicial : data['vr_inicial'],
                                            vr_software : data['vr_software'],
                                        });
                                        
                                        this.modalCierreCaja=1;
                                        this.tituloModalCierre='Listado de cierres de caja';
                                        this.tipoAccionCierre=3;
                                    }
                                    else{
                                        result.dismiss === Swal.DismissReason.cancel
                                    }
                                })
                                break;
                            }
                            case 'ver':
                            {
                                this.tipoAccionCierre=4;
                                this.id_caja = data['id_caja'];
                                this.nombre_caja = data['nombre'];
                                this.vr_inicial_cierre = data['vr_inicial'];
                                this.obs_inicial_cierre = data['obs_inicial'];
                                this.vr_gastos_cierre = data['vr_gastos'];
                                this.obs_gastos_cierre = data['obs_gastos'];
                                this.vr_software_cierre = data['vr_software'];
                                this.vr_final_cierre = data['vr_final'];
                                break;
                            }
                        }
                        this.selectCajas();
                        break;
                    }
                }
            },
            ocultarDetalle(){
                let me=this;
                me.listado=1;
                me.facturacion_id=0;
                me.num_factura=0,
                me.id_tercero=0,
                me.tercero_facturacion='',
                me.id_usuario=0,
                me.fec_edita='',
                me.subtotal=0.0,
                me.valor_iva=0.0,
                me.iva = 0,
                me.total=0.0,
                me.abono=0.0,
                me.saldo=0.0,
                me.detalle='',
                me.lugar='',
                me.descuento=0.0,
                me.fec_registra='',
                me.fec_envia='',
                me.fec_anula='',
                // me.fecha = '',
                me.id_tarifario = 0;
                me.estado = 0,
                // me.arrayFacturacion=[];
                // me.arrayFacturacionT=[];
                me.arrayDetalle=[];
                me.arrayTerceros=[];    
                me.listarFacturacion();
                
                
            },
            
            listarDetalle(id_factura){
                let me=this;
                var url= this.ruta +'/detalle_facturacion/buscarDetalleFacturacion?id_factura=' + id_factura;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayDetalle = respuesta.detalles;
                    me.arrayDetalleT = respuesta.detalles;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            abrirModalImpresion(factura){
                let me = this;
                this.mostrarModImp = 1;
                // this.listarImpresora();
                this.id_factura_imprimir = factura;
                me.listarImpresora();
                
            },
            cerrarModalImpresion(){
                let me = this;
                this.mostrarModImp = 0;
                this.id_factura_imprimir = 0
            },
            // funcion abrir modal Terceros
            abrirModalT(accion){               
                this.arrayTerceros=[];
                this.modal2 = 1;
                switch(accion){
                    case 'terceros':{
                        this.tituloModal2 = 'Seleccione un tercero';
                        this.tipoAccionModalTerceros = 1;
                        break;
                    }
                    case 'tercero_filtro':{
                        this.tituloModal2 = 'Seleccione un tercero para el filtro';
                        this.tipoAccionModalTerceros = 2;
                        break;
                    }
                    case 'vendedor_filtro':{
                        this.tituloModal2 = 'Seleccione un vendedor';
                        this.tipoAccionModalTerceros = 3;
                        break;
                    }
                }
            },
            
            cerrarModalT(){
                this.modal2=0;
                this.tituloModal2='';
                this.terc_busq = '';
            },
            cargarTercero(tercero){
                if(this.tipoAccionModalTerceros==1)
                {
                    this.tercero = tercero['num_documento'];
                    this.id_tercero = tercero['id'];
                }
                if(this.tipoAccionModalTerceros==2)
                {
                    this.terceroFiltro = tercero['num_documento'];
                    this.idTerceroFiltro = tercero['id'];
                }
                if(this.tipoAccionModalTerceros==3)
                {
                    this.vendedorFiltro = tercero['num_documento'];
                    this.idVendedorFiltro = tercero['id'];
                }

                this.cerrarModalT();
            },

            buscarTercero(){
                let me=this;
                var search = this.terc_busq;
                var url= this.ruta +'/cliente/selectCliente?filtro='+search;
                    axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayTerceros = respuesta.clientes;                    
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            //Evaluar valor tercero
            validarUnder(valor)
            {
                if(valor == "undefined" || valor=="" || !valor)
                    return "";
                else
                    return valor;
            },
            quitar(opc){
                //alert(opc);
                if(opc==1){
                    this.id_cuenta_ini = '';
                    this.cuenta_ini = '';
                }
                if(opc==2){
                    this.id_cuenta_fin = '';
                    this.cuenta_fin = '';
                }
                if(opc==3){
                    this.id_tercero = '';
                    this.tercero = '';
                }
                if(opc==4){
                    this.idTerceroFiltro = '';
                    this.terceroFiltro = '';
                }
                if(opc==5){
                    this.idVendedorFiltro = '';
                    this.vendedorFiltro = '';
                }
            },

            // Añadir articulos
// Pendiente borrar
            abrirModal(){               
                this.arrayArticulo=[];
                this.modal = 1;
                this.tituloModal = 'Seleccione uno o varios artículos';
                this.selectCategoria2();
                this.listarArticulo(this.buscarA,this.criterioA,this.buscarCategoriaA)
            },
            cerrarModal(){
                this.modal=0;
                
            }, 

// fin pendiente borrar
            abrirModalCantidadArticulo(data){               
                this.modalCantidadArticulo = 1;
                this.tituloModalCantidadArticulo = 'Cantidad del articulo';
                this.arrayInfoArticuloModalCantidad = data;
                this.stockCantidadArticulo = data['stock']
            },
            cerrarModalCantidadArticulo(){
                this.modalCantidadArticulo=0;
                this.tituloModalCantidadArticulo='';
                this.cantidadArticulo = 0;
                this.idDetalleAsociado = 0;
                this.stockCantidadArticulo = 0;
                this.saldoParcialCantidadArticulo = false;
                this.arrayInfoArticuloModalCantidad = [];
            }, 
            agregarDetalleModal(data =[]){
                let me=this;

                if(me.encuentra(data['id'], data['id_asociado'])){
                    Swal.fire({
                        type: 'error',
                        title: 'Error...',
                        text: 'Ese artículo ya se encuentra agregado!',
                        })
                }
                else{
                    var p = '';
                    if(data['padre']!='') {p = ' '+data['nom_presentacion']+' (Presentacion asociada)';}
                    else {p = ' - '+data['nom_presentacion'];}

                    var ivaVenta = 0;
                    data['productos_iva'].forEach(function(iva){
                        if(iva['tipo_iva']=='Venta'){ivaVenta=iva['porcentaje'];}
                    });

                    var descu = 0;
                    if(data['descuento'] && data['descuento']!='') {descu=data['descuento'];}

                    me.arrayDetalle.push({
                        idarticulo: data['id'],
                        id_asociado: data['id_asociado'],
                        codigo: data['codigo'],
                        articulo: data['nombre'],
                        porcentaje : ivaVenta,
                        cantidad: data['cant'],
                        precio: data['precio_venta'],
                        precio_venta: data['precio_venta'],
                        iva : ivaVenta,
                        nom_presentacion : data['nom_presentacion'],
                        id_presentacion : data['id_presentacion'],
                        padre : data['padre'],
                        unidades : data['unidades'],
                        descuento : 0,
                        valor_descuento : 0,
                        observaciones:''
                    }); 
                }
            },
            agregarDetalle(){
                let me=this;
                // console.log("por aca ")
                if(me.idarticulo==0 || me.cantidad==0 || me.precio==0){
                }
                else{
                    if(me.encuentra(me.idarticulo, me.idDetalleAsociado)){
                        Swal.fire({
                            type: 'error',
                            title: 'Error...',
                            text: 'Ese artículo ya se encuentra agregado!',
                            })
                    }
                    else{
                        me.arrayDetalle.push({
                            idarticulo: me.idarticulo,
                            articulo: me.articulo,
                            cantidad: me.cantidad,
                            valor_descuento: me.descuento,
                            precio: me.precio,
                            iva: me.iva,
                            stock : me.stock,
                            descuento : me.descuento,
                            nom_presentacion : me.nom_presentacion,
                            id_presentacion : me.id_presentacion,
                            padre : me.padreDetalle,
                            observaciones : ''
                        });
                        me.codigo="";
                        me.idarticulo=0;
                        me.articulo="";
                        me.cantidad=0;
                        me.precio=0;
                        me.iva = 0;
                        me.descuento = 0;
                        me.nom_presentacion = '';
                        me.id_presentacion = 0;
                        me.padreDetalle = '';
                        me.idDetalleAsociado = 0;
                    }
                    
                }
            },
            agregarDetalleModalCantidadArticulo(){
                let me=this;
                
                if(me.encuentra(me.arrayInfoArticuloModalCantidad.id && me.arrayInfoArticuloModalCantidad.id_asociado)){
                    Swal.fire({
                        type: 'error',
                        title: 'Error...',
                        text: 'Ese artículo ya se encuentra agregado!',
                        })
                }
                else{
                    var p = ''
                    if(me.arrayInfoArticuloModalCantidad.padre!='') {p = ' '+data['nom_presentacion']+' (Presentacion asociada)';}
                    else {p = ' - '+me.arrayInfoArticuloModalCantidad.padre;}

                    var ivaVenta = 0;
                    me.arrayInfoArticuloModalCantidad.productos_iva.forEach(function(iva){
                        if(iva['tipo_iva']=='Venta'){ivaVenta=iva['porcentaje'];}
                    });

                    me.arrayDetalle.push({
                        idarticulo: me.arrayInfoArticuloModalCantidad.id,
                        id_asociado: me.arrayInfoArticuloModalCantidad.id_asociado,
                        articulo: me.arrayInfoArticuloModalCantidad.codigo+' - '+me.arrayInfoArticuloModalCantidad.nombre,
                        porcentaje : ivaVenta,
                        cantidad: me.cantidadArticulo,
                        precio: me.arrayInfoArticuloModalCantidad.precio_venta,
                        precio_venta : me.arrayInfoArticuloModalCantidad.precio_venta,
                        iva: ivaVenta,
                        nom_presentacion : me.arrayInfoArticuloModalCantidad.nom_presentacion,
                        id_presentacion : me.arrayInfoArticuloModalCantidad.id_presentacion,
                        padre : me.arrayInfoArticuloModalCantidad.padre,
                        descuento : 0,
                        valor_descuento : 0,
                        observaciones : ''
                    }); 
                }
                me.cerrarModalCantidadArticulo();
            },
            encuentra(id,id_asociado){
                let me=this;
                var sw=0;
                for(var i=0;i<this.arrayDetalle.length;i++){
                    if(this.arrayDetalle[i].id==id)
                    {
                        if(this.arrayDetalle[i].id_asociado=='' && id_asociado=='')
                        {
                            sw=true;
                        }
                    }
                }
                return sw;
            },
            registrarFacturacion(){
                if (this.validarFacturacion()){
                    return;
                }
                
                let me = this;
            
                axios.post(this.ruta +'/facturacion/registrar',{
                    'num_factura': null,
                    'id_tercero': me.id_tercero,
                    'fec_edita': null,
                    'usu_edita': null,
                    'subtotal': me.subtotal,
                    'valor_iva': me.valor_iva,
                    'total': me.valor_final,
                    'abono': me.abono,
                    'saldo': me.saldo,
                    'detalle': me.detalle,
                    'lugar': me.lugar,
                    'descuento': me.calcularDescuento,
                    'fec_registra': null,
                    'fec_envia': null,
                    'fec_anula': null,
                    'usu_registra': null,
                    'usu_envia': null,
                    'usu_anula': null,
                    'fecha': me.fecha,
                    'id_tarifario': me.id_tarifario,
                    'id_cierre_caja': me.id_cierre_caja_facturacion,
                    'data': me.arrayDetalle,
                    'tipo_movimiento' : 4,
                    'sumatoria' : 0
                }).then(function (response) {
                    me.ocultarDetalle();
                    me.listarFacturacion(1,'','','','','','','');
                    me.listado=0;
                    me.listarPendientes();
                }).catch(function (error) {
                    console.log(error);
                });
            },
            actualizarFacturacion(){
                if (this.validarFacturacion()){
                    return;
                }                
                let me = this;
                
                if(me.estado==2){
                    me.sugerirNumFactura();
                }
                
                axios.put(this.ruta +'/facturacion/actualizar',{
                    'num_factura': me.num_factura,
                    'id_tercero': me.id_tercero,
                    'fec_edita': me.fechaHoraActual,
                    'subtotal': me.subtotal,
                    'valor_iva': me.valor_iva,
                    'total': me.valor_final,
                    'abono': me.abono,
                    'saldo': me.saldo,
                    'detalle': me.detalle,
                    'lugar': me.lugar,
                    'descuento': me.calcularDescuento,
                    'fec_registra': null,
                    'fec_envia': null,
                    'fec_anula': null,
                    'usu_registra': null,
                    'usu_envia': null,
                    'usu_anula': null,
                    'fecha': me.fecha,
                    'id_tarifario': me.id_tarifario,
                    'estado': me.estado,
                    'data': me.arrayDetalle,
                    'tipo_movimiento' : 4,
                    'sumatoria' : 0,
                    'id' : me.facturacion_id, 
                    'observaciones' : ''
                    
                }).then(function (response) {
                    me.ocultarDetalle();
                    me.listarFacturacion(1,'','','','','','','');
                    me.listado=0;
                    me.listarPendientes();
                }).catch(function (error) {
                    console.log(error);
                });
            },
            validarFacturacion(){
                this.errorFacturacion=0;
                this.errorMostrarMsjFacturacion =[];

                if (this.fecha==0) this.errorMostrarMsjFacturacion.push("Ingrese la fecha");
                // if (this.num_factura==0) this.errorMostrarMsjFacturacion.push("Seleccione el comprobante");
                // if (!this.id_tercero) this.errorMostrarMsjFacturacion.push("Seleccione un tercero");
                if (!this.id_tercero) this.id_tercero=999999;
                if (!this.lugar) this.errorMostrarMsjFacturacion.push("Seleccione un lugar");
                if (this.arrayDetalle.length<=0) this.errorMostrarMsjFacturacion.push("Ingrese detalles");

                if (this.errorMostrarMsjFacturacion.length) this.errorFacturacion = 1;

                return this.errorFacturacion;
            },
            cambiarEstadoFacturacion(id_factura, accion){
                let me=this;
                var cambiarEstado = '';
                var nomEstado = '';

                switch(accion)
                {
                    case 'registrar':{
                        me.sugerirNumFactura();
                        cambiarEstado = '2';
                        nomEstado = '"'+'Registrado'+'"';
                        break;
                    };
                    case 'enviar':{
                        cambiarEstado = '3';
                        nomEstado = '"'+'Enviado'+'"';
                        break;
                    };
                    case 'anular':{
                        cambiarEstado = '4';
                        nomEstado = '"'+'Anulado'+'"';
                        break;
                    };
                }

                Swal.fire({
                title: 'Esta seguro de cambiar el estado a '+nomEstado+'?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar!',
                cancelButtonText: 'Cancelar',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
                reverseButtons: true
                }).then((result) => {
                if (result.value) {
                    let me = this;

                    axios.put(this.ruta +'/facturacion/cambiarEstado',{
                        'estado': cambiarEstado,
                        'num_factura': me.num_factura,
                        'id': id_factura
                    }).then(function (response) {
                        me.ocultarDetalle();
                        me.listarFacturacion(1,'','','','','','','');
                        me.listarPendientes();
                    }).catch(function (error) {
                        console.log(error);
                    });
                    
                    
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    
                    }
                }) 
            },
            buscarArticulo(){
                let me=this;
                var url= this.ruta +'/articulo/buscarArticulo?filtro=' + me.codigo;

                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayArticulo = respuesta.articulos;

                    if (me.arrayArticulo.length>0){
                        if (me.arrayArticulo[0]['productos_asociados'].length>0){
                            me.buscarA = me.arrayArticulo[0]['codigo'];
                            me.abrirModal();
                        }
                        else {
                            var p = '';
                            if(me.arrayArticulo[0]['padre']!='')
                            {
                                p = ' - (Presentacion asociada: '+me.arrayArticulo[0]['nom_presentacion']+')';
                            }
                            else
                            {
                                p = ' - '+me.arrayArticulo[0]['nom_presentacion'];
                            }
                            me.codigo=me.arrayArticulo[0]['codigo'];
                            me.articulo= me.arrayArticulo[0]['nombre'];
                            me.idarticulo=me.arrayArticulo[0]['id'];
                            me.precio = me.arrayArticulo[0]['precio_venta'];
                            me.cantidad = 1;
                            me.stock = me.arrayArticulo[0]['stock'];
                            for(var i=0; i<me.arrayArticulo[0]['productos_iva'].length; i++)
                            { 
                                if(me.arrayArticulo[0]['productos_iva'][i]['tipo_iva']=='Venta')
                                {
                                    me.porcentaje = me.arrayArticulo[0]['productos_iva'][i]['porcentaje'];
                                }
                            }
                            // me.porcentaje = me.arrayArticulo[0]['porcentaje'];
                            me.descuento = 0;
                            me.id_presentacion = me.arrayArticulo[0]['id_presentacion'];
                            me.idDetalleAsociado = me.arrayArticulo[0]['id_asociado'];
                            // me.cantidad=me.arrayArticulo[0]['stock'];
                        }
                    }
                    else{
                        me.articulo='No existe artículo';
                        me.idarticulo=0;
                        me.precio = 0;
                        me.cantidad = 0;
                        me.stock = 0;
                        me.padreDetalle = '';
                        me.idDetalleAsociado = 0;
                        me.descuento = 0;
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            eliminarDetalle(index){
                let me = this;
                me.arrayDetalle.splice(index, 1);
            },
        },
        computed:{
            isActived: function(){
                return this.pagination.current_page;
            },
            //Calcula los elementos de la paginación
            pagesNumber: function() {
                if(!this.pagination.to) {
                    return [];
                }
                
                var from = this.pagination.current_page - this.offset; 
                if(from < 1) {
                    from = 1;
                }

                var to = from + (this.offset * 2); 
                if(to >= this.pagination.last_page){
                    to = this.pagination.last_page;
                }  

                var pagesArray = [];
                while(from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            },

            calcularSubtotal: function(){
                var resultado=0.0;
                for(var i=0;i<this.arrayDetalle.length;i++){
                    resultado=resultado+((this.arrayDetalle[i].precio*this.arrayDetalle[i].cantidad)-this.arrayDetalle[i].valor_descuento);
                }
                resultado = resultado-this.calcularTotalIva;
                return resultado;
            },
            calcularDescuento: function(){
                var resultado=0.0;
                for(var i=0;i<this.arrayDetalle.length;i++){
                    resultado=resultado+parseInt(this.arrayDetalle[i].valor_descuento);
                }
                return resultado;
            },
            calcularTotal: function(){
                var resultado=0.0;
                for(var i=0;i<this.arrayDetalle.length;i++){
                    resultado=resultado+((this.arrayDetalle[i].precio*this.arrayDetalle[i].cantidad)-this.arrayDetalle[i].valor_descuento)
                }
                return resultado;
            },
            calcularTotalIva: function(){
                var resultado=0.0;
                for(var i=0;i<this.arrayDetalle.length;i++){
                    resultado += this.arrayDetalle[i].valor_iva;
                }
                // resultado = this.total-resultado;
                resultado = Math.round(resultado);
                return resultado;
            },
            calcularSaldo: function(){
                var resultado=0.0;

                resultado=resultado+(this.calcularTotal-this.abono);
                
                return resultado;
            },
            
        },
        mounted(){
            let me = this;
            var d = new Date();
                        
            var dd = d.getDate();
            var mm = d.getMonth()+1;
            var yyyy = d.getFullYear();
            var h = d.getHours();
            var min = d.getMinutes();
            var sec = d.getSeconds();

            
            
            if(dd<10){
                dd='0'+dd;
            } 
            if(mm<10){
                mm='0'+mm;
            } 
            d = yyyy+'-'+mm+'-'+dd;
            me.fechaActual = d;
            me.hastaFiltro = d;
            me.fecha = d;
            me.fechaHoraActual = d+' '+h+':'+min+':'+sec;


        this.selectCategoria2();
            this.listarArticulo(this.buscarA,this.criterioA,this.buscarCategoriaA)
            me.listarCajas();
            me.selectZonas();
        }
        
    }
</script>
<style>
    .mostrar{
        display: list-item !important;
        opacity: 1 !important;
        position: absolute !important;
        background-color: #3c29297a !important;
    }
    .mosaico{
        display: inline-block;
        float: left;
    }
</style>