<template>
    <main class="main">
        <notifications group="foo" />
        <div>           
            <div v-show="position==1">  <!-- VISTA NUEVA FACTURA -->
                
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            
                            <div class="col-5 pr-1">
                                <button class="btn btn-danger" @click="ocultarDetalle(); position=2;">
                                    <i class="fa fa-trash"></i>Descartar 
                                </button>
                            </div>
                        </div>                                
                    </div>
                    <div class="card-body">
                        <div class="form-group">                          
                        
                            <div class="row ">
                                <div class="col-12">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th>Observaciones</th>
                                                <th>Preparado</th>
                                                <!-- <th>Estado de impresion</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(detalle) in arrayDetalle" :key="detalle.id">
                                                
                                                
                                                <td v-if="detalle.padre==null || detalle.padre==''" >{{detalle.articulo+' - '+detalle.nom_presentacion}}</td>
                                                <td v-else >
                                                    {{detalle.articulo+' - '+detalle.nom_presentacion+' (Presentación asociada)'}}
                                                </td>
                                                <td>{{detalle.cantidad}}</td>
                                                <td v-text="detalle.observaciones"></td>
                                                <td>
                                                    <button class="btn btn-lg text-danger" style="font-size:1.5rem"  v-if="detalle.preparado==0" title="Activar" @click="productoListo(detalle.id),2">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                    <button class="btn btn-lg text-success" style="font-size:1.5rem" v-if="detalle.preparado==1" title="Desactivar" @click="productoNoListo(detalle.id)">
                                                        
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                </td>
                                               
                                            </tr>
                                        </tbody>
                                    </table>                                 
                                </div>
                            </div> 
                            
                        </div>
                    </div>
                </div>      
                
            </div>
            
            <div v-show="position==2"> <!-- listado de facturas -->
                <div class="card">
                   
                    <div class="card-header col-12"> 
                        <h1>Ordenes en cocina</h1> 
                        <button class="btn btn-sm btn-success" @click="recargarPagina();">Recargar página</button>
                    </div> 

                    <div class="">

                        <table class="table table-bordered table-striped table-sm table-hover table-sm-responsive table-earning h4" align="center">
                            <thead class="thead-primary">
                                <tr>
                                                               
                                    <th  scope="col" rowspan="2">Mesa</th>                                    
                                    <th  scope="col" rowspan="2" class="text-center">Finalizar</th>          
                                    <th scope="col" colspan="1" rowspan="1" class="text-center">Productos</th>
                                    
                                </tr>
                                <tr>
                                    <th colspan="1" rowspan="1">
                                        <div class="list-groupx list-group-flushx row">
                                            <div class="list-group-itemx py-1 col-7">Articulo</div>
                                            <div class="list-group-itemx py-1 col-5" >Nota</div>
                                            <!--<div class="list-group-itemx py-1 col-3">Preparado</div>-->
                                        </div>
                                    </th>                                    
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(det,index) in arrayDetalle" :key="index" class="text-dark">                                      
                                                                           
                                    <th class="text-center">
                                        {{det.nombre_lugar}}
                                        <br>
                                        
                                        <small v-if="det.estado==1"><span>Activa</span></small>
                                        <small v-else-if="det.estado==2"><span>Registrada</span></small>
                                        <small v-else-if="det.estado==3"><span>Enviada</span></small>
                                        <small v-else-if="det.estado==4"><span>Anulada</span></small>
                                        <br>
                                        <i class="icon-cup"></i>  
                                        <h5 class="text-uppercase font-weight-bold">{{det.mesero}}</h5>

                                    </th>
                                    <td class="text-center">
                                        <button class="btn btn-primary btn-sm" @click="productoListo(det.id,1);">Finalizar todo <br> el pedido</button>
                                    </td>
                                    <td>
                                        <div class="list-groupx list-groupx-flush row" v-for="prod in det.productos" :key="prod.id">
                                            <div class="list-group-itemx py-1 text-left col-7">
                                                <b>#<span class="badge rounded-pill bg-primary text-white h4">{{prod.cantidad}}</span></b>
                                                 -  
                                                <span class="text-uppercase">{{prod.articulo}}</span>
                                            </div>   
                                            <div class="list-group-itemx py-1 text-left col-5">                                                
                                                {{prod.observaciones}}
                                            </div>   
                                            <!--<div class="list-group-itemx p-0 col-3">
                                                <a class="btn h5 text-danger"  v-if="prod.preparado==0" title="Activar" @click="productoListo(prod.id,2)">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                                <a class="btn h5 text-success" v-if="prod.preparado==1" title="Desactivar" @click="productoNoListo(prod.id)">
                                                    
                                                    <i class="fa fa-check"></i> 
                                                </a>                        
                                            </div>   -->                                             
                                        </div>
                                    </td>    
                                                   
                                                   
                                    
                                </tr>
                                
                                
                            </tbody>
                        </table>                                   
                    </div>
                </div>  
            </div>
        </div>
        
    </main>
</template>
<script>
 alertify.alert('Ready!');
</script>
<script>  

    import vSelect from 'vue-select';
    import Vue from 'vue'
    export default {
        props : ['ruta'],
        data (){
            return {
                position: 2,
                arrayDetalle : [],
                arrayDetalleT : [],
                arrayFacturacion : [],
                arrayFacturacionT : [],
            
                fechaActual: '',
                fechaHoraActual:'',
                estado: 0,             
                numFacturaFiltro : '',
                estadoFiltro : '',
                idTerceroFiltro : '',
                terceroFiltro : '',
                desdeFiltro : '2019-01-01',
                hastaFiltro : '',
                ordenFiltro : '',
                idVendedorFiltro : '',
                vendedorFiltro : '',

                prod_nuevo : 0,
            }
        },
        components: {
            vSelect
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
        
        },
        methods : {
         
            
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
            listarCajas(){
                let me=this;
                var url= this.ruta +'/cierres_caja/validarCierreCaja';
                axios.get(url).then(function (response) {
                    //console.log(response.data);
                    var respuesta= response.data;
                    var ban = respuesta.ban;
                    me.arrayCierresXCajas = respuesta.cierres_cajas;

                    if(ban==0 || ban==1)
                    {
                        me.no_caja = 1;
                      
                        Swal.fire({
                         
                            type: 'error',
                            title: 'Abir caja',
                            position: 'center',
                            showConfirmButton: false,
                            timer: 1700
                        })
                    }
                    else
                    {
                        if(ban==3)
                        {
                            me.no_caja = 0;
                            me.id_caja_facturacion = me.arrayCierresXCajas[0]['id'];
                            me.id_cierre_caja_facturacion = me.arrayCierresXCajas[0]['id'];
                            me.nom_caja_cierre_facturacion = me.arrayCierresXCajas[0]['nombre'];
                            me.cierre_caja_id = me.arrayCierresXCajas[0]['id'];
                            me.id_caja_cierre = me.arrayCierresXCajas[0]['id_caja'];
                            me.nom_caja_cierre = me.arrayCierresXCajas[0]['nombre'];
                            me.vr_inicial_cierre = me.arrayCierresXCajas[0]['vr_inicial'];
                            me.obs_inicial_cierre = me.arrayCierresXCajas[0]['obs_inicial'];
                            //console.log("entra al ban 3");
                            me.listarFacturacion(1,me.numFacturaFiltro,me.estadoFiltro,me.idTerceroFiltro,me.ordenFiltro,me.desdeFiltro,me.hastaFiltro,me.idVendedorFiltro);
                        }

                        if(ban==2)
                        {
                            me.no_caja = 1;
                          
                            Swal.fire({
                                // toast: true,
                                // position: 'top-end',
                                type: 'error',
                                title: 'Abir caja',
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
            productoListo(id,tipo){
                let me = this;
                console.log(tipo);
                console.log(id);
                var id_fac = me.facturacion_id;

                
                 Swal.fire({
                title: '¿Este producto está listo?',
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

                    axios.put(this.ruta +'/detalle_facturacion/cocinado',{
                        'id': id,
                        'tipo':tipo,
                    }).then(function (response) {
                       me.listarDetalle(id_fac);                      
                        Swal.fire(
                        'Listo!',
                        'El registro ha sido activado con éxito.',
                        'success'
                        )
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
            productoNoListo(id){
                let me = this;
                console.log(me.facturacion_id);
                var id_fac = me.facturacion_id;

                Swal.fire({
                title: '¿No ha sido preparado?',
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

                    axios.put(this.ruta +'/detalle_facturacion/sin-cocinar',{
                        'id': id
                    }).then(function (response) {
                        me.listarDetalle(id_fac);            
                       
                        Swal.fire(
                        'Preparando...!',       
                        '',
                        'warning'
                        )
                        
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
                                me.arrayTerceros=[];
                                me.listarFacturacion(1,'','','','','','','');
                                break;
                            };
                            case 'actualizar':{
                                me.tipoAccion2 = 2;
                                me.facturacion_id=data['id'];
                                me.num_factura=data['num_factura'];
                                me.id_tercero=data['id_tercero'];
                                me.tercero=data['nombre1'] ?  data['nombre1']+' '+data['nombre2']+' '+data['apellido1']+' '+data['apellido2'] : data['nom_tercero'];
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
                //me.id_tarifario = 0;
                me.estado = 0,
               
                me.arrayDetalle=[];
                me.arrayTerceros=[];       
            },
            imprimirTicket(id){
                let me = this;
                console.log(id);
                axios.get(this.ruta+'/detalle_facturacion/imprimir-ticket?id='+id).then(function(response){
                    console.log(response)

                })
            },         
            listarDetalle(id_factura){
                let me=this;
                
                var url= this.ruta +'/detalle_facturacion/productosPreparados';
                axios.get(url).then(function (response) {
                    console.log(response);
                    var respuesta= response.data;
                    
                    me.arrayDetalle = respuesta.factura;
                    
                })
                .catch(function (error) {
                    console.log(error);
                });
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
            recargarPagina(){
                let me = this;
                this.listarDetalle();
                this.listarCajas();
                this.buscarTercero();
                this.selectZonas();
            },
           
        },
        mounted() {
            $(".mul-select").select2({
                placeholder: "- Categorias -", //placeholder
                tags: true,
                allowClear: true,
                tokenSeparators: ['/',',',';'," "] 
            });
            //console.log( $(".mul-select"));
             
            let me= this;
            var d = new Date();
            $(".mul-select").on("change", function (e) { 
                me.listarArticulo(me.buscarA,me.criterioA,me.buscarCategoriaA) 
            });
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
            me.desdeFiltro = d;
            me.fecha = d;
            me.fechaHoraActual = d+' '+h+':'+min+':'+sec;
            this.ocultarDetalle();
            this.selectCategoria2();
            me.listarCajas();
            me.buscarTercero();
            me.selectZonas();
            
           // me.llamarMensaje();
            this.listarArticulo(this.buscarA,this.criterioA,this.buscarCategoriaA);
            me.listarFacturacion(1,me.numFacturaFiltro,me.estadoFiltro,me.idTerceroFiltro,me.ordenFiltro,me.desdeFiltro,me.hastaFiltro,me.idVendedorFiltro);
            me.listarDetalle();
             
        }
    }
</script>
<style> 
    /* {
            font-size: 12px;
            font-family: 'Times New Roman';
    }*/
    .ex1 {
        border: 1px solid rgb(49, 173, 45);
        outline-style: solid;
        outline-color: rgba(45, 189, 76, 0.87);
        outline-width: thin;
}
    .btn-edit-factura {
        font-size: 15px !important;
    }
    .minimizar {
        font-size: 9px;
    }
    .espacio-1 {
        margin-top: 0 !important; 
        margin-bottom: 0rem !important;
    }
    .centrado {
        text-align: center;
        align-content: center;
    }
    .ticket {
        width: 288px;
        max-width: 320px;
        margin: auto;
        line-height: 1;
    }
     .img-logo {
        max-width: 87px;
        margin-left: 100px;
    }
    .select2-search__field {
            width: 100% !important;
    }
    .select2-container {
            width: 100% !important
    }
    .nombre-tercero {
        font-size: 16px !important;
    }
    .txt-nom-prod {
        line-height: 1 !important;
        min-height: 35px;
        border-radius: 3px;
    }
    .separa-cards {
        padding-right: 0.15rem !important;
        padding-left: 0.15rem !important;       
    }
    .img-prods {
        width: 89px;
        height: 89px
    }
    .txt-price-prod {
        font-size: 12px;
        border-radius: 3px !important;
    }

    h3.ocultar{
        display: none !important;
    }
    
    div.resaltar:hover, div.active:hover, div h3.resaltar:hover, div>h3.active:hover  {
        background-color: #d1d3e2!important;
       
    }
    .mul-select  {
         width: 100%;
    }
    .precio-prod{
        position: absolute;
        float: right;
        top: 74px;
        right: 33px;
        font-size: 95%;
        z-index: 2;
        min-width: 85px;
    }
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .mostrar{
        display: list-item !important;
        opacity: 1 !important;
        position: fixed !important;
        background-color: #3c29297a !important;
    }
    .div-error{
        display: flex;
        justify-content: center;
    }
    .text-error{
        color: red !important;
        font-weight: bold;
    }
    .mosaico{
        display: inline-block;
        float: left;
    }
    @media (min-width: 600px) {
        .btnagregar {
            margin-top: 2rem;
        }
    }

</style>
