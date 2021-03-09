<template>
    <main class="main">

        <div class="row">
                <div class="col-12">
                    <h3>Informes venta de categorias</h3>
                </div>
                <div class="col-12">
                    <div class="filtros row">
                         <div class="form-group col-md-3 col-sm-4">
                            <label>Desde:</label>                                   
                            <input v-if="permisosUser.leer" type="date" class="form-control" v-model="desdeFiltro" @keyup="listarVentas(page, noCategoriaFiltro, desdeFiltro, hastaFiltro)">
                            <input v-else disabled type="date" class="form-control" v-model="desdeFiltro">
                            
                        </div>
                        <div class="form-group col-md-3 col-sm-4">
                            <label>Hasta:</label>                                   
                            <input v-if="permisosUser.leer" type="date" class="form-control" v-model="hastaFiltro" @keyup="listarVentas(page, noCategoriaFiltro, desdeFiltro, hastaFiltro)">
                            <input v-else disabled type="date" class="form-control" v-model="hastaFiltro">
                            
                        </div>
                       
                        <div class="form-group col-md-3 col-sm-4">
                            <label for="" class="form-label">Categoria</label>
                            <input type="text" class="form-control" id="" placeholder="Categoria" v-model="noCategoriaFiltro" @change="listarVentas(page, noCategoriaFiltro, desdeFiltro, hastaFiltro);">                            
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-success btn-block"  @click="listarVentas(page, noCategoriaFiltro, desdeFiltro, hastaFiltro)">Buscar</button>
                        </div>
                        
                        <div class="form-group col-md-3">
                            <button type="button" @click="abrirModalImpresion();" class=" btn btn-primary btn-sm" title="imprimir">
                                <i class="icon-printer"></i> Imprimir listado
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        <div>
            <table class="table table-sm table-responsive mr-auto ml-auto">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        
                        <th>Categoria</th>
                        <th>Cantidad</th>
                        
                        <th class="text-right">Precio total</th>

                    </tr>
                </thead>
                <tbody v-if="permisosUser.leer && arrayVentas.length">
                    <tr v-for="venta in arrayVentas" :key="venta.id">
                        <td v-text="venta.id"></td>
                        <td>{{venta.fec_crea}}</td>
                        <td>{{venta.categoria}}</td>
                        <td>{{venta.cantidad}}</td>
                        
                        <td scope="row" class="text-right">{{venta.valor_final}}</td>

                    </tr>
                    <tr class="text-dark h5">
                        <td colspan="3"></td>
                        <td colspan="1">Total</td>
                        <td>{{totalizado = CalcularTotalizado}}</td>
                    </tr>

                </tbody>

            </table>
        </div>    
        <nav>
            <ul class="pagination">
                <li class="page-item" v-if="pagination.current_page > 1">
                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1, noCategoriaFiltro, desdeFiltro, hastaFiltro)">Ant</a>
                </li>
                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page, noCategoriaFiltro, desdeFiltro, hastaFiltro)" v-text="page"></a>
                </li>
                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1, noCategoriaFiltro, desdeFiltro, hastaFiltro)">Sig</a>
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
                        <button type="button" class="btn btn-success" @click="imprimirTicketFacturacion(noCategoriaFiltro,desdeFiltro,hastaFiltro)">Imprimir</button>
                        
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
            page : 1,
            noCategoriaFiltro : '',
            desdeFiltro : '',
            hastaFiltro : '',
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
        CalcularTotalizado: function(){
            var resultado=0.0;
            for(var i=0;i<this.arrayVentas.length;i++){
                resultado=resultado+this.arrayVentas[i].valor_final;
            }
            return resultado;
        },
    },
    methods:{
        listarVentas(page, noCategoriaFiltro, desdeFiltro, hastaFiltro){
            let me=this;

            var url= this.ruta +'/informe/categorias?page='+page+'&noCategoriaFiltro='+me.noCategoriaFiltro +'&desdeFiltro='+me. desdeFiltro + '&hastaFiltro='+ me.hastaFiltro;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                // console.log(response)
                me.arrayVentas = respuesta.detalles_facturacion.data;
                me.pagination= respuesta.pagination;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cambiarPagina(page, noCategoriaFiltro, desdeFiltro, hastaFiltro){
            let me = this;
            //Actualiza la página actual
            me.pagination.current_page = page;
            me.listarVentas(page, noCategoriaFiltro, desdeFiltro, hastaFiltro);
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
            axios.get(this.ruta+'/informe/imprimir-ticket-informe-categorias?noCategoriaFiltro='+me.noCategoriaFiltro +'&desdeFiltro='+me. desdeFiltro + '&hastaFiltro='+ me.hastaFiltro+'&id_impresora='+this.id_impresora).then(function(response){                 

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
            me.desdeFiltro = d;
        me.listarVentas(1,me.noCategoriaFiltro,me.desdeFiltro,me.hastaFiltro);
        
    }
}
</script>