<template>
    <main class="main">
        <div class="row">
            <div class="col-12 row">
                <h3 class="col-md-12">Informes arqueo de cajas</h3>
                
            </div>
             <div class="col-12">
                <div class="filtros row">
                    <div class="form-group col-md-3">
                        <label for="exampleFormControlInput1" class="form-label">Caja</label>
                        <select  v-model="noCajaFiltro" class="custom-select" @change="listarVentas(1,idFiltro,noCajaFiltro,desdeFiltro,hastaFiltro)">
                           <option value="0">Seleccione</option>
                            <option v-for="(caja, index) in arrayCajas" :value="caja.id" v-text="caja.nombre" :key="index"></option>
                        </select>
                        <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
                    </div>
                     <div class="form-group col-md-3">
                        <label>Nro Registro:</label>                                   
                        <input v-if="permisosUser.leer" type="number" class="form-control" v-model="idFiltro" @change="listarVentas(1,idFiltro,noCajaFiltro,desdeFiltro,hastaFiltro)">
                        <input v-else disabled type="number" class="form-control" v-model="idFiltro">
                        
                    </div>

                    <div class="form-group col-md-3">
                        <label>Desde:</label>                                   
                        <input v-if="permisosUser.leer" type="date" class="form-control" v-model="desdeFiltro" @change="listarVentas(1,idFiltro,noCajaFiltro,desdeFiltro,hastaFiltro)">
                        <input v-else disabled type="date" class="form-control" v-model="desdeFiltro">
                        
                    </div>
                    <div class="form-group col-md-3">
                        <label>Hasta:</label>                                   
                        <input v-if="permisosUser.leer" type="date" class="form-control" v-model="hastaFiltro" @change="listarVentas(1,idFiltro,noCajaFiltro,desdeFiltro,hastaFiltro)">
                        <input v-else disabled type="date" class="form-control" v-model="hastaFiltro">
                        
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-success" @change="listarVentas(1,idFiltro,noCajaFiltro,desdeFiltro,hastaFiltro)">Buscar</button>
                    </div>
                    <div class="form-group col-md-3">
                        <!-- <label><i class="icon-printer"></i></label>    <br> -->

                        <button type="button" @click="abrirModalImpresion();" class=" btn btn-primary btn-sm" title="imprimir">
                            <i class="icon-printer"></i> Imprimir listado
                        </button>
                    </div>
                </div>
            </div>

        </div>  
        <div>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha Cierre</th>
                        <th>Caja</th>                        
                        <th>Cant. Facturas</th>           
                        <th>Valor inicial</th>
                        <th>Valor Ventas</th>
                        <th>Valor Gastos</th>
                        <th>Valor Reportado</th>
                        <th>Estado</th>
                        <th>Vr Diferencia</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="venta in arrayVentas" :key="venta.id">
                        <td v-text="venta.id"></td>
                        <td v-text="venta.fecha_cierre"></td>
                        <td v-text="venta.nombre_caja"></td>
                        <td v-text="venta.no_facturas"></td>
                        <td v-text="venta.vr_inicial"></td>
                        <td v-text="venta.total_ventas"></td>                        
                        <td v-text="venta.vr_gastos"></td>          
                        <th v-text="venta.vr_final"></th>
                        <td v-if="venta.estado == 1" class=""> <i class="icon-check text-primary"></i>Correcto</td>
                        <td v-if="venta.estado == 2" class=""><i class="fa fa-times-circle text-danger"></i>Faltante</td>
                        <td v-if="venta.estado == 3" class=""><i class="fa fa-times-circle text-danger"></i>Sobra</td>
                        <th v-text="venta.diferencia"></th>
                    </tr>
                   

                </tbody>

            </table>
        </div>  
        <nav>
            <ul class="pagination">
                <li class="page-item" v-if="pagination.current_page > 1">
                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,idFiltro,noCajaFiltro,desdeFiltro,hastaFiltro)">Ant</a>
                </li>
                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page,idFiltro,idFiltro,noCajaFiltro,desdeFiltro,hastaFiltro)" v-text="page"></a>
                </li>
                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,idFiltro,noCajaFiltro,desdeFiltro,hastaFiltro)">Sig</a>
                </li>
            </ul>
        </nav>
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
                        <button type="button" class="btn btn-success" @click="imprimirTicketFacturacion(idFiltro,noCajaFiltro,desdeFiltro,hastaFiltro)">Imprimir</button>
                        
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
export default {
    props : ['ruta'],
    data(){
        return{
            permisosUser : {
                'leer' : 1,
                'escribir' : 1,
                'crear' : 1,
                'actualizar' : 1,
                'anular' : 1,
            },
            arrayVentas:[],
            arrayCajas:[],
            arrayImpresora : [],

            pagination : {
                'total' : 0,
                'current_page' : 0,
                'per_page' : 0,
                'last_page' : 0,
                'from' : 0,
                'to' : 0,
            },
              
            // Filtros          
            page:1,  
            noCajaFiltro:'',
            desdeFiltro : '2021-01-01',
            hastaFiltro : '',
            idFiltro:'',

            // Variables de impresion
            mostrarModImp :0,
            id_impresora:0,

        }

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
    methods:{
        listarVentas(page,idFiltro,noCajaFiltro,desdeFiltro,hastaFiltro){
            let me=this;

            var url= this.ruta +'/informe/cajas?page='+me.page+'&idFiltro='+me.idFiltro +'&noCajaFiltro='+me.noCajaFiltro +'&desdeFiltro='+me. desdeFiltro + '&hastaFiltro='+ me.hastaFiltro ;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                console.log(response)
                me.arrayVentas = respuesta.cajas_cierres.data;
                me.pagination= respuesta.pagination;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cambiarPagina(page,idFiltro,noCajaFiltro,desdeFiltro,hastaFiltro){
            let me = this;
            //Actualiza la página actual
            me.pagination.current_page = page;
            me.listarVentas(page,idFiltro,noCajaFiltro,desdeFiltro,hastaFiltro);
        },

        listarCajas (){
            let me=this;
            // var url= this.ruta +'/cajas?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio;
            var url= this.ruta +'/cajas';
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayCajas = respuesta.cajas.data;
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

        imprimirTicketFacturacion(){
            let me = this;            
            axios.get(this.ruta+'/informe/imprimir-ticket-informe-cajas?idFiltro='+me.idFiltro +'&noCajaFiltro='+me.noCajaFiltro +'&desdeFiltro='+me. desdeFiltro + '&hastaFiltro='+ me.hastaFiltro+'&id_impresora='+this.id_impresora).then(function(response){                 

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
        me.desdeFiltro = d;
        me.fecha = d;
        me.fechaHoraActual = d+' '+h+':'+min+':'+sec;

        me.listarVentas();
        me.listarCajas();
    }
}
</script>

<style>    
    .modal-content{
        width: 100% !important;
        position: absolute !important;
        margin-top: 5em;
    }
    .mostrar{
        display: list-item !important;
        opacity: 1 !important;
        position: fixed !important;
        background-color: #3c29297a !important;
    }
</style>