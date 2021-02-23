<template>
    <main class="main">

        <div class="row">
                <div class="col-12">
                    <h3>Informes venta de productos y categorias</h3>
                </div>
                <div class="col-12">
                    <div class="filtros">
                        <div class="form-group">
                             <label for="exampleFormControlInput1" class="form-label">Email address</label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com" v-model="noProductoFiltro">
                            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
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
                <tbody>
                    <tr v-for="venta in arrayVentas" :key="venta.id">
                        <td v-text="venta.id"></td>
                        <td>{{venta.fec_crea}}</td>
                        <td>{{venta.articulo}}</td>
                        <td>{{venta.categoria}}</td>
                        <td>{{venta.cantidad}}</td>
                        <td>{{venta.valor_venta}}</td>
                        <td scope="row">{{venta.valor_final}}</td>

                    </tr>
                    <tr>
                        <td colspan="6">Total</td>
                    </tr>

                </tbody>

            </table>
        </div>    
    </main>
</template>

<script>
export default {
    props : ['ruta'],
    data(){
        return{
            arrayVentas:[],


            // Filtros
            page : '',
            noProductoFiltro : '',
            noCategoriaFiltro : '',
            desdeFiltro : '2021-01-01',
            hastaFiltro : ''
        }

    },
    methods:{
        listarVentas(page, noProductoFiltro, noCategoriaFiltro, desdeFiltro, hastaFiltro){
            let me=this;

            var url= this.ruta +'/informe/productos';
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                console.log(response)
                me.arrayVentas = respuesta.detalles_facturacion;
            })
            .catch(function (error) {
                console.log(error);
            });
        },

    },
    mounted(){
        let me = this;
        me.listarVentas(me.page,me.noProductoFiltro,me.noCategoriaFiltro,me.desdeFiltro,me.hastaFiltro);
    }
}
</script>