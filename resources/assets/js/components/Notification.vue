<template>
    <li class="nav-item dropdown no-arrow mx-1">
        <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-bell fa-fw"></i>
            <!-- Counter - Alerts -->
            <span class="badge badge-danger badge-counter">{{notifications.length}}</span>
        </a>
        <!-- Dropdown - Alerts -->
        <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
            aria-labelledby="alertsDropdown">
            <h6 class="dropdown-header">
                Notificaciones
            </h6>
            <div v-if="notifications.length">
                <li v-for="item in listar" :key="item.id">
                    <div class="dropdown-item d-flex align-items-center" href="#" @click="eliminarNotificacion(item.id)">
                        <div class="mr-3">
                            <div class="icon-circle bg-primary">
                                <i class="fa fa-file-alt text-white"></i>
                            </div>                            
                        </div>
                        <div>
                            <div class="font-weight-bold">
                                <span v-text="nombresProductos[item.data.datos.id_producto]"></span>  <br>
                                <small> CANTIDAD : {{item.data.datos.cantidad}}</small>                              
                            </div>
                            <div>
                                <span v-text="item.data.datos.estado"></span>
                                <!-- <span v-text="item"></span> -->
                            </div>
                            <div class="small text-gray-500" v-text="item.created_at"></div>
                            
                        </div>
                    </div>
                </li>
            </div>
            <div v-else class="p-2">
                Sin notificaciones
            </div>
            <a class="dropdown-item text-center small text-gray-500" href="#">Mostrar lista completa</a>
        </div>
    </li>
</template>

<script>
// import func from '../../../../vue-temp/vue-editor-bridge'
export default {
    props : ['notifications', 'ruta'],
    data(){
        return{
            arrayNotifications:[],
            nombresProductos : []
        }
    },
    methods:{
        nombreProducto(){
        let me = this;
        // var url= this.ruta+'/articulo';
        axios.get('http://192.168.0.100/sasseri_app2/public/articulo')
        .then(function (response){
          var auxProducto = response.data.articulos.data;
          console.log(response.data.articulos.data)
          auxProducto.forEach(element => me.nombresProductos[element.id] = element.nombre);
        })
      },
      eliminarNotificacion(id){
        let me = this;
        
        axios.post('notification/delete',{
            'id': id
        }).then(function (response) {

        })
        .catch(function (error) {
            console.log(error);
        });
                        
      },
    },
    mounted() {
        this.nombreProducto()
    },
    computed:{
        listar: function(){
            // return this.notifications[0];
            this.arrayNotifications= Object.values(this.notifications);
            if(this.notifications == ''){
                return this.arrayNotifications = [];
            }else{
                this.arrayNotifications= Object.values(this.notifications);
                // if(this.arrayNotifications.length>3){
                //     return Object.values(this.arrayNotifications);
                // }else{
                //     return Object.values(this.arrayNotifications);
                // }
                return Object.values(this.arrayNotifications);
            }

            
        }
    }
}
</script>
