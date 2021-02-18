<template>
    <div>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha Cierre</th>
                    <th>Caja</th>
                    <th>Valor inicial</th>
                    <th>Valor Gastos</th>
                    <th>Valor final</th>
                    

                </tr>
            </thead>
            <tbody>
                <tr v-for="venta in arrayVentas" :key="venta.id">
                    <td v-text="venta.id"></td>
                    <td v-text="venta.fecha_cierre"></td>
                    <td v-text="venta.nombre_caja"></td>
                    <td v-text="venta.vr_inicial"></td>
                    <td v-text="venta.vr_gastos"></td>
                    <td v-text="venta.vr_final"></td>

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
        listarVentas(){
            let me=this;

            var url= this.ruta +'/informe/cajas';
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                console.log(response)
                me.arrayVentas = respuesta.cajas_cierres;
            })
            .catch(function (error) {
                console.log(error);
            });
        },

    },
    mounted(){
        let me = this;
        me.listarVentas();
    }
}
</script>