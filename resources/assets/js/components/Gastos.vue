<template>
    <main class="main">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                     <i class="fa fa-align-justify"></i> Detalles de gastos
                    <button v-if="permisosUser.crear" type="button" @click="abrirModal('gastos','registrar')" class="btn btn-primary">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                    <button v-else type="button" class="btn btn-secondary">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>

                    <button type="button" @click="abrirModalImpresion();" class=" btn btn-primary btn-sm" title="imprimir">
                        <i class="icon-printer"></i> Imprimir listado
                    </button>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Detalle</th>
                                <th>Valor</th>
                                <th>Caja</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody v-if="arrayGastos.length>0">
                            <tr v-for="gasto in arrayGastos" :key="gasto.id">
                                <td>{{gasto.id}}</td>
                                <td>{{gasto.detalle_gasto}}</td>
                                <td>{{gasto.valor_gasto}}</td>
                                <td>{{gasto.nombre_caja}}</td>
                                <td>                                                                        
                                    <button 
                                        type="button" 
                                        @click="abrirModal('gastos','actualizar',gasto)" 
                                        class="btn btn-success"
                                        title="Actualizar"
                                    >
                                        <i class="icon-pencil"></i>
                                    </button>
                                
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>       

        <!-- Modal -->
        <div class="modal fade"  :class="{'mostrar' : modal}" id="modalGastos" tabindex="-1" aria-labelledby="ModalGastos" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" v-text="tituloModal"></h5>
                    <button type="button" class="btn-close" @click="cerrarModal();" aria-label="Close"></button>
                </div>

                <div class="modal-body">                    
                    <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                        <div class="col-12">
                             <div class="form-group">
                                <label for="formGroupExampleInput">Detalles gasto</label>
                                <input type="text" class="form-control" id="formGroupExampleInput" placeholder="Describir los gastos" v-model="detalle_gasto">
                            </div>

                            <div class="form-group">
                                <label for="formGroupExampleInput">Valor de gasto</label>
                                <input type="number" class="form-control" id="formGroupExampleInput" placeholder="Valor del gasto" v-model="valor_gasto">
                            </div>

                            <div class="form-group">
                                <label for="caja">Caja</label>
                                <select class="custom-select" v-model="id_caja_cierre">
                                    <option value="" selected>--Seleccionar--</option>
                                    <option :value="cajas.id_cierre_caja"  v-for="cajas in arrayCajasAbiertas" :key="cajas.id_cierre_caja">{{cajas.nombre}}</option>
                                </select>
                            </div>
                           
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click="cerrarModal()">Cerrar</button>
                    <button type="button" v-if="tipoAccion==1" class="btn btn-success" @click="registrarGasto()">Guardar</button>
                    <button type="button" v-if="tipoAccion==2" class="btn btn-success" @click="actualizarGasto()">Actualizar</button>
                </div>
                </div>
            </div>
        </div>
        <!-- fin modal crear gastis -->

        <!-- Modal de impresion -->
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
        <!-- Cierre de modal de impresion -->

    </main>
    
</template>

<script>  
    import vSelect from 'vue-select';
    import Vue from 'vue'
    export default {
        props : ['ruta'],
        data (){
            return {
                permisosUser : {
                    'leer' : 1,
                    'escribir' : 1,
                    'crear' : 1,
                    'actualizar' : 1,
                    'anular' : 1,
                },
                
                //arrays
                arrayGastos:[],
                arrayCajasAbiertas:[],
            
                //Datos de modal
                modal:0,
                tituloModal:'',
                id_caja_cierre:0,
                valor_gasto:0,
                detalle_gasto:'',
                gasto_id:0,
                tipoAccion:0,
                
                // Variables de impresion
                mostrarModImp :0,
                id_impresora:0,
                arrayImpresora : [],

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
            listarGastos(){
                let me=this;                
                var url= this.ruta +'/detalle_gastos/';
                axios.get(url).then(function (response) {
                    console.log(response);
                    var respuesta= response.data;                    
                    me.arrayGastos = respuesta.detalle_gastos.data;                    
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            listarCajasAbiertas(id_factura){
                let me=this;
                
                var url= this.ruta +'/detalle_gastos/seleccionar-caja-abierta';
                axios.get(url).then(function (response) {
                    console.log(response);
                    var respuesta= response.data;                    
                    me.arrayCajasAbiertas = respuesta.cierres_caja;
                })
                .catch(function (error) {
                    console.log(error);
                });
            }, 
            cerrarModal(){
                this.modal=0;
                this.tituloModal='';
                this.nombre='';
                this.descripcion='';
            },
            abrirModal(modelo, accion, data = []){
                switch(modelo){
                    case "gastos":
                    {
                        switch(accion){
                            case 'registrar':
                            {
                                this.modal = 1;
                                this.tituloModal = 'Registrar detalle de gasto';
                                this.valor_gasto= '';
                                this.detalle_gasto = '';
                                this.id_caja_cierre = '';
                                this.tipoAccion = 1;
                                break;
                            }
                            case 'actualizar':
                            {                                  
                                this.modal=1;
                                this.tituloModal='Actualizar detalle de gasto';
                                this.tipoAccion=2;
                                this.gasto_id=data['id'];
                                this.detalle_gasto = data['detalle_gasto'];
                                this.id_caja_cierre = data['id_caja_cierre'];
                                this.valor_gasto = data['valor_gasto'];                                
                                break;
                            }
                        }
                    }
                }
            },
            registrarGasto(){
                 let me = this;

                axios.post(this.ruta +'/detalle_gastos/registrar',{
                    'valor_gasto': this.valor_gasto,
                    'detalle_gasto': this.detalle_gasto,
                    'id_caja_cierre': this.id_caja_cierre
                }).then(function (response) {
                    me.cerrarModal();
                    me.listarGastos();
                }).catch(function (error) {
                    console.log(error);
                });
            },
            actualizarGasto(){
                let me = this;
                axios.put(this.ruta +'/detalle_gastos/actualizar',{
                    'valor_gasto': this.valor_gasto,
                    'detalle_gasto': this.detalle_gasto,
                    'id_caja_cierre': this.id_caja_cierre,
                    'id': this.gasto_id
                }).then(function (response) {
                    me.cerrarModal();                    
                }).catch(function (error) {
                    console.log(error);
                });
            },
           
            imprimirTicket(id){
                let me = this;
                console.log(id);
                axios.get(this.ruta+'/detalle_facturacion/imprimir-ticket?id='+id).then(function(response){
                    //console.log(response)
                })
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

        imprimirTicketFacturacion(){
            let me = this;            
            axios.get(this.ruta+'/informe/imprimir-ticket-informe-gastos?id_impresora='+this.id_impresora).then(function(response){                 

            }).catch(function (error) {
            Swal.fire({
                    
                    type:'warning',
                    title: 'Oops...',
                    text: 'No se pudo imprimir',
                    
                })
                                
            });
        },

        abrirModalImpresion(){
            let me = this;
            this.mostrarModImp = 1;
            this.listarImpresora();
            // this.id_factura_imprimir = factura;
            // me.listarImpresora(1,'','nombre_impresora');
            
        },
        cerrarModalImpresion(){
            let me = this;
            this.mostrarModImp = 0;
            this.id_factura_imprimir = 0
        },
            
        },
        mounted() {
             
            let me= this;
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
            me.desdeFiltro = d;
            me.fecha = d;
            me.fechaHoraActual = d+' '+h+':'+min+':'+sec;
            
            me.listarGastos();
            me.listarCajasAbiertas();
             
        }
    }
</script>
<style>    
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
