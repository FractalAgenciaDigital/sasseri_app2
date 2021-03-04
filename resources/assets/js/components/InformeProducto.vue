<template>
    <main class="main">

        <div class="row">
                <div class="col-12">
                    <h3>Informes venta de productos y categorias</h3>
                </div>
                <div class="col-12">
                    <div class="filtros row">
                         <div class="form-group col-md-3 col-sm-4">
                            <label>Desde:</label>                                   
                            <input v-if="permisosUser.leer" type="date" class="form-control" v-model="desdeFiltro" @keyup="listarVentas(page, noProductoFiltro, noCategoriaFiltro, desdeFiltro, hastaFiltro)">
                            <input v-else disabled type="date" class="form-control" v-model="desdeFiltro">
                            
                        </div>
                        <div class="form-group col-md-3 col-sm-4">
                            <label>Hasta:</label>                                   
                            <input v-if="permisosUser.leer" type="date" class="form-control" v-model="hastaFiltro" @keyup="listarVentas(page, noProductoFiltro, noCategoriaFiltro, desdeFiltro, hastaFiltro)">
                            <input v-else disabled type="date" class="form-control" v-model="hastaFiltro">
                            
                        </div>
                        <div class="form-group col-md-3 col-sm-4">
                            <label for="" class="form-label">Producto</label>
                            <input type="text" class="form-control" id="" placeholder="Producto" v-model="noProductoFiltro" @change="listarVentas(page, noProductoFiltro, noCategoriaFiltro, desdeFiltro, hastaFiltro);">                            
                        </div>
                        <div class="form-group col-md-3 col-sm-4">
                            <label for="" class="form-label">Categoria</label>
                            <input type="text" class="form-control" id="" placeholder="Categoria" v-model="noCategoriaFiltro" @change="listarVentas(page, noProductoFiltro, noCategoriaFiltro, desdeFiltro, hastaFiltro);">                            
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-success btn-block"  @click="listarVentas(page, noProductoFiltro, noCategoriaFiltro, desdeFiltro, hastaFiltro)">Buscar</button>
                        </div>

                    </div>
                </div>
            </div>
        <div>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Categoria</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Precio total</th>

                    </tr>
                </thead>
                <tbody v-if="permisosUser.leer && arrayVentas.length">
                    <tr v-for="venta in arrayVentas" :key="venta.id">
                        <td v-text="venta.id"></td>
                        <td>{{venta.fec_crea}}</td>
                        <td>{{venta.articulo}}</td>
                        <td>{{venta.categoria}}</td>
                        <td>{{venta.cantidad}}</td>
                        <td>{{venta.valor_venta}}</td>
                        <td scope="row">{{venta.valor_final}}</td>

                    </tr>
                    <tr class="text-dark h5">
                        <td colspan="5"></td>
                        <td colspan="1">Total</td>
                        <td>{{totalizado = CalcularTotalizado}}</td>
                    </tr>

                </tbody>

            </table>
        </div>    
        <nav>
            <ul class="pagination">
                <li class="page-item" v-if="pagination.current_page > 1">
                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,noProductoFiltro, noCategoriaFiltro, desdeFiltro, hastaFiltro)">Ant</a>
                </li>
                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page,noProductoFiltro, noCategoriaFiltro, desdeFiltro, hastaFiltro)" v-text="page"></a>
                </li>
                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,noProductoFiltro, noCategoriaFiltro, desdeFiltro, hastaFiltro)">Sig</a>
                </li>
            </ul>
        </nav>
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
            pagination : {
                'total' : 0,
                'current_page' : 0,
                'per_page' : 0,
                'last_page' : 0,
                'from' : 0,
                'to' : 0,
            },

            // Filtros
            page : '',
            noProductoFiltro : '',
            noCategoriaFiltro : '',
            desdeFiltro : '2021-01-01',
            hastaFiltro : ''
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
                resultado=resultado+this.arrayVentas[i].valor_venta;
            }
            return resultado;
        },
    },
    methods:{
        listarVentas(page,noProductoFiltro, noCategoriaFiltro, desdeFiltro, hastaFiltro){
            let me=this;

            var url= this.ruta +'/informe/productos?page='+me.page+'&noCategoriaFiltro='+me.noCategoriaFiltro +'&noProductoFiltro='+me.noProductoFiltro +'&desdeFiltro='+me. desdeFiltro + '&hastaFiltro='+ me.hastaFiltro;
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
        cambiarPagina(page,noProductoFiltro, noCategoriaFiltro, desdeFiltro, hastaFiltro){
            let me = this;
            //Actualiza la página actual
            me.pagination.current_page = page;
            me.listarVentas(page,noProductoFiltro, noCategoriaFiltro, desdeFiltro, hastaFiltro);
        },

    },
    mounted(){
        let me = this;
        me.listarVentas(me.page,me.noProductoFiltro,me.noCategoriaFiltro,me.desdeFiltro,me.hastaFiltro);
        
    }
}
</script>