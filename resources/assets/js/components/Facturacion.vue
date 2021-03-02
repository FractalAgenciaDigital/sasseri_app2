<template>
    <main>
        <div class="container">
            <div class="row">
                <div class="col-12 text-left">
                    <h5>Facturacion</h5>
                    <hr>
                </div>
                <section class="col-12">
                    <div class="card p-0">
                        <div class="card-header">
                            
                            <h6>   <i class="fa fa-align-justify"></i> Pendientes</h6>
                        </div>
                        <div class="card-body" style="margin:auto;">

                            <table class="table table-bordered table-striped table-responsive table-earning ">
                                <thead>
                                        <tr>
                                        <td colspan="4" scope="col" class="text-center">
                                            <button class="btn btn-success" @click="listarPendientes();">Recargar Pedidos</button>
                                        </td>
                                    </tr>       
                                    <tr>     
                                        <th>Lugar</th>                                        
                                        <th>Total</th>                                   
                                        <th>Editar / Imprimir</th>
                                        <th class="text-right">Pagar $</th>
                                    </tr>
                                </thead>
                                <tbody v-if="permisosUser.leer && arrayPendientes.length">
                                    <tr v-for="pendientes in arrayPendientes" :key="pendientes.id" style="text-align: right;">                                        
                                        <td v-text="pendientes.nom_lugar"></td>                                        
                                        <td v-text="pendientes.total"></td>
                                        <td v-if="pendientes.num_factura" v-text="pendientes.num_factura"></td>
                                        <td v-else class="text-left">
                                           
                                            <template>
                                                <button href="#" @click="mostrarDetalle('facturacion','actualizar',pendientes)" class="btn btn-success btn-sm" v-if="permisosUser.actualizar  && pendientes.estado==1" title="Actualizar">
                                                    <i class="icon-pencil"></i>
                                                </button>
                                                <button href="#" class="btn btn-secondary btn-sm" v-else title="Actualizar (Deshabilitado)">
                                                    <i class="icon-pencil"></i>
                                                </button>
                                            </template>
                                            <button type="button" @click="abrirModalImpresion(pendientes.id);" class=" btn btn-primary btn-sm" title="imprimir">
                                            <i class="icon-printer"></i> 
                                            </button>

                                            
                                            <!-- boton eliminar -->
                                            <template v-if="permisosUser.anular && pendientes.estado==1">
                                                <button type="button" class="btn btn-primary text-white" @click="cambiarEstadoFacturacion(pendientes.id,'anular')" v-if="pendientes.estado!=4 && pendientes.estado!=3" title="Anular">
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
                                                                                
                                        <td>
                                            <!-- Botones de registrar -->
                                            <template> 
                                                <button type="button" v-if="permisosUser.actualizar && pendientes.estado==1" class="btn btn-primary text-white" @click="validarRegreso(pendientes.id, pendientes.total);" title="Registrar">
                                                    <!-- cambiarEstadoFacturacion(pendientes.id,'registrar',pendientes.total) -->
                                                    Pagar
                                                    <!-- Registrar -->
                                                </button>

                                                <button type="button" v-else-if="permisosUser.actualizar && pendientes.estado==2" @click="pdfFormato(pendientes.id)" class="btn btn-info btn-sm" title="PDF">
                                                    <i class="icon-doc"></i> 
                                                    <!-- Descargar PDF -->
                                                </button>

                                                <button type="button" v-else class="btn btn-primary text-white" title="Registrar (Deshabilitado)">
                                                    <i class="fa fa-registered"></i> 
                                                    <!-- Registrar -->
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
                    </div>
                </section>
            </div>
            <div class="modal fade" :class="{'mostrar':modalRegreso}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true" >
                <div  class="modal-dialog modal-primary" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Registro de Pago</h5>
                            <button type="button" class="close" @click="cerrarModalRegreso();" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div class="row border">
                                <div class="row col-12">
                                    <h5 class="col-6">Valor total: </h5>
                                    <input class="form-control col-6  text-right" type="text" v-model="valorPagar" aria-label="readonly input example" readonly>
                                </div>
                                <div class="row col-12">
                                    <h5 class="col-6">Efectivo: </h5>
                                    <input class="form-control col-6  text-right" type="text" placeholder="$0" v-model="valorEfectivo" aria-label="readonly input example" >
                                    
                                </div>
                                 <div class="row col-12">
                                    <h5 class="col-6">Cambio: </h5>
                                    <input class="form-control col-6  text-right" type="text" v-model="valorRegreso" placeholder="$0" aria-label="readonly input" readonly>
                                </div>
                                
                            </div>

                            <div class="row border">
                                <div class="col-4">
                                    <input class="btn btn-primary btn-block"  @click="valorEfectivo=1000" :value="1000">
                                    <input class="btn btn-primary btn-block"  @click="valorEfectivo=2000" :value="2000">
                                    <input class="btn btn-primary btn-block"  @click="valorEfectivo=5000" :value="5000">
                                    <input class="btn btn-primary btn-block"  @click="valorEfectivo=10000" :value="10000">
                                    <input class="btn btn-primary btn-block"  @click="valorEfectivo=20000" :value="20000">
                                    <input class="btn btn-primary btn-block"  @click="valorEfectivo=50000" :value="50000">
                                    <input class="btn btn-primary btn-block"  @click="valorEfectivo=100000" :value="100000">
                                    
                                </div>
                                <div class="col-8 row">
                                    <button class="btn btn-light col-4" @click="append(1);">1</button>
                                    <button class="btn btn-light col-4" @click="append(2);">2</button>
                                    <button class="btn btn-light col-4" @click="append(3);">3</button>
                                    <button class="btn btn-light col-4" @click="append(4);">4</button>

                                    <button class="btn btn-light col-4" @click="append(5);">5</button>
                                    <button class="btn btn-light col-4" @click="append(6);">6</button>
                                    <button class="btn btn-light col-4" @click="append(7);">7</button>
                                    <button class="btn btn-light col-4" @click="append(8);">8</button>

                                    <button class="btn btn-light col-4" @click="append(9);">9</button>
                                    <button class="btn btn-danger col-4" @click="limpiarRegreso();">x</button>
                                    <button class="btn btn-light col-4" @click="append(0);">0</button>
                                     <button type="button" class="btn btn-success col-4" @click="calcularRegreso();">
                                        <i class="icon-check"></i>
                                    </button>
                                    

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModalRegreso();">Close</button>
                            <button type="button" class="btn btn-primary" @click="cambiarEstadoFacturacion(facturacion_id,'registrar')">Pagar</button>
                        </div>
                    </div>
                </div>

            </div>
            <!--Fin modal de regreso -->

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
            arrayPendientes :[],
            arrayImpresora:[],
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

            //Impresion de factura en ticket
            mostrarModImp : 0,
            id_impresora: 0, 
            nombre_impresora : '',
            id_factura_imprimir:0,

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

            // Calculo de efectivos}
            valorPagar:0,
            valorEfectivo:0,
            valorRegreso:0,
            modalRegreso:0,
            operatorClicked: false



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
                if(accion=='registrar'){

                    axios.get(this.ruta+'/facturacion/imprimir-ticket-facturacion?id='+this.facturacion_id+'&id_impresora=5&valorEfectivo='+this.valorEfectivo+'&valorCambio='+this.valorRegreso).then(function(response){                 

                    }).catch(function (error) {
                        console.log(error);
                    });
                
                }
            } else if (
                // Read more about handling dismissals
                result.dismiss === Swal.DismissReason.cancel
            ) {
                
                }
            }) 
        },
        
        validarRegreso(id_factura, total_pagar){
            let me = this;
            me.modalRegreso = 1;
            me.valorPagar = total_pagar;
            me.facturacion_id = id_factura;
        },
        cerrarModalRegreso(){
            let me = this;
            me.modalRegreso = 0;
            me.valorEfectivo = 0;
            me.valorPagar = 0;
            me.valorRegreso = 0;
        }, 
        calcularRegreso(){
            let me = this; 
            me.valorRegreso = me.valorEfectivo-me.valorPagar;

        }, 
        limpiarRegreso(){
            let me = this;
            me.valorEfectivo=0;
            me.valorRegreso=0;
        },
        append(number) {
            if (this.operatorClicked === true) {
                this.valorEfectivo = '';
                this.operatorClicked = false;
            }
            this.valorEfectivo =
                this.valorEfectivo === 0
                ? (this.valorEfectivo = number)
                : '' + this.valorEfectivo + number;
        },
        imprimirTicketFacturacion(){
            let me = this;            
            axios.get(this.ruta+'/facturacion/imprimir-ticket-facturacion?id='+this.id_factura_imprimir+'&id_impresora='+this.id_impresora).then(function(response){                 

            }).catch(function (error) {
                console.log(error);
            });
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
        me.listarCajas();
        me.selectZonas();
        
    }
    
}
</script>
<style>
    .mostrar{
        display: list-item !important;
        opacity: 1 !important;
        position: fixed !important;
        background-color: #3c29297a !important;
    }
    .mosaico{
        display: inline-block;
        float: left;
    }
</style>