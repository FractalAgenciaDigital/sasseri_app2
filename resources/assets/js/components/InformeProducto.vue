<template>
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
</template>

<script>
export default {
    props : ['ruta'],
    data(){
        return{
            arrayVentas:[]
        }

    },
    methods:{
        listarFacturacion(){
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
        me.listarFacturacion();
    }
}
</script>