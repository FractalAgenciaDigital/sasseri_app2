<template>
    <main class="main">
        <div class="row">
            <div class="col-12">
                <h3>Informes arqueo de cajas</h3>
            </div>

        </div>
   
        <div>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha Cierre</th>
                        <th>Caja</th>
                        
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
    </main>  
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