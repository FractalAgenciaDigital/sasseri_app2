<template>
        <main class="main">
            <!-- Breadcrumb -->
            
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Observaciones                       
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                
                                    <input v-if="permisosUser.leer" type="text" v-model="buscar" @keyup="listarObservacion(1,buscar,criterio)" class="form-control" placeholder="Texto a buscar">
                                    <input v-else disabled type="text" v-model="buscar" class="form-control" placeholder="Texto a buscar">
                                
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped table-sm table-responsive table-earning">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Articulo Asociado</th>
                                    <th class="col-1">Estado</th>
                                    <th class="col-1">Opciones</th>
                                </tr>
                            </thead>
                            <tbody v-if="permisosUser.leer && arrayObservacion.length">
                                <tr v-for="observacion in arrayObservacion" :key="observacion.id">
                                    <td v-text="observacion.observacion"></td>
                                    <td v-text="observacion.nombre_articulo"></td>
                                    <td class="td-estado">
                                        <template v-if="permisosUser.anular">
                                            <a v-if="observacion.estado" href="#" class="btn text-success" @click="desactivarObservacion(observacion.id)" title="Desactivar">
                                                <i class="fa fa-check-circle"></i>
                                            </a>
                                            <a v-else href="#" class="btn text-danger" @click="activarObservacion(observacion.id)" title="Activar">
                                                <i class="fa fa-times-circle"></i>
                                            </a>
                                        </template>
                                        <template v-else>
                                            <a v-if="observacion.estado" href="#" class="btn text-secondary btn-sm" title="Desactivar (Deshabilitado)">
                                                <i class="fa fa-check-circle"></i>
                                            </a>
                                            <a v-else href="#" class="btn text-secondary btn-sm" title="Activar (Deshabilitado)">
                                                <i class="fa fa-times-circle"></i>
                                            </a>
                                        </template>
                                    </td>
                                    <td>
                                        <button v-if="permisosUser.actualizar && observacion.estado" type="button" @click="abrirModal('observacion','actualizar',observacion)" class="btn btn-success btn-sm" title="Actualizar">
                                          <i class="icon-pencil"></i>
                                        </button>
                                        <button v-else type="button" class="btn btn-secondary btn-sm" title="Actualizar (Deshabilitado)">
                                          <i class="icon-pencil"></i>
                                        </button> 
                                    </td>
                                </tr>                                
                            </tbody>
                            <tbody v-else>
                                <tr><td colspan="2">No hay registros para mostrar</td></tr>
                            </tbody>
                        </table>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="pagination.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar,criterio)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar,criterio)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar,criterio)">Sig</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>
            <!--Inicio del modal agregar/actualizar-->
            <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModal"></h4>
                            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                              <span aria-hidden="true" title="Cerrar">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="form-row">
                                    <div class="form-group col-12">
                                        <label class="form-control-label" for="text-input">Nombre</label>
                                        <input type="text" v-model="observacion" class="form-control">                                       
                                    </div>                                
                                    <!-- <div class="form-group col-12">
                                        <label class="form-control-label" for="text-input">Observación</label>                                        
                                        <textarea type="text" v-model="observacion" class="form-control col-md-12"></textarea>                                        
                                    </div> -->
                                </div>
                                <div v-show="errorObservacion" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in errorMostrarMsjObservacion" :key="error" v-text="error">

                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" @click="cerrarModal()">Cerrar</button>
                            <button type="button" v-if="tipoAccion==1" class="btn btn-success" @click="registrarObservacion()">Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-success" @click="actualizarObservacion()">Actualizar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Fin del modal-->
        </main>
</template>

<script>
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
                observacion_id: 0,
                observacion : '',
                arrayObservacion : [],
                modal : 0,
                tituloModal : '',
                tipoAccion : 0,
                errorObservacion : 0,
                errorMostrarMsjObservacion : [],
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 10,
                criterio : 'observacion',
                buscar : ''
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

            }
        },
        methods : {
            listarObservacion (page,buscar,criterio){
                let me=this;
                var url= this.ruta +'/observacion?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayObservacion = respuesta.observacion.data;
                    me.pagination= respuesta.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            cambiarPagina(page,buscar,criterio){
                let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarObservacion(page,buscar,criterio);
            },
            registrarObservacion(){
                if (this.validarObservacion()){
                    return;
                }
                
                let me = this;

                axios.post(this.ruta +'/observacion/registrar',{
                    'observacion': this.observacion,
                    'id_articulo_obs': this.id_articulo_obs,
                }).then(function (response) {
                    me.cerrarModal();
                    me.listarObservacion(1,'','nombre');
                }).catch(function (error) {
                    console.log(error);
                });
            },
            actualizarObservacion(){
                if (this.validarObservacion()){
                    return;
                }
                
                let me = this;

                axios.put(this.ruta +'/observacion/actualizar',{
                    'observacion': this.observacion,
                    'id': this.observacion_id
                }).then(function (response) {
                    me.cerrarModal();
                    me.listarObservacion(1,'','nombre');
                }).catch(function (error) {
                    console.log(error);
                }); 
            },
            validarObservacion(){
                this.errorObservacion=0;
                this.errorMostrarMsjObservacion =[];

                if (!this.observacion || this.observacion=='') this.errorMostrarMsjObservacion.push("Ingrese el nombre de la observacion");

                if (this.errorMostrarMsjObservacion.length) this.errorObservacion = 1;

                return this.errorObservacion;
            },
            desactivarObservacion(id){
               Swal.fire({
                title: 'Esta seguro de desactivar esta observacion?',
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

                    axios.put(this.ruta +'/observacion/desactivar',{
                        'id': id
                    }).then(function (response) {
                        me.listarObservacion(1,'','nombre');
                        Swal.fire(
                        'Desactivado!',
                        'El registro ha sido desactivado con éxito.',
                        'success'
                        )
                    }).catch(function (error) {
                        console.log(error);
                    });
                    
                    
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    
                }
                }) 
            },
            activarObservacion(id){
               Swal.fire({
                title: 'Esta seguro de activar esta observacion?',
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

                    axios.put(this.ruta +'/observacion/activar',{
                        'id': id
                    }).then(function (response) {
                        me.listarObservacion(1,'','nombre');
                        Swal.fire(
                        'Activado!',
                        'El registro ha sido activado con éxito.',
                        'success'
                        )
                    }).catch(function (error) {
                        console.log(error);
                    });
                    
                    
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    
                }
                }) 
            },
            cerrarModal(){
                this.modal=0;
                this.tituloModal='';
                this.nombre='';
                this.errorObservacion = 0;
                this.errorMostrarMsjObservacion = [];
            },
            abrirModal(modelo, accion, data = []){
                switch(modelo){
                    case "observacion":
                    {
                        switch(accion){
                            case 'registrar':
                            {
                                this.modal = 1;
                                this.tituloModal = 'Registrar Observacion';
                                this.observacion= '';
                                this.observacion= '';
                                this.tipoAccion = 1;
                                break;
                            }
                            case 'actualizar': 
                            {
                                //console.log(data);
                                this.modal=1;
                                this.tituloModal='Actualizar Observacion';
                                this.tipoAccion=2;
                                this.observacion_id=data['id'];
                                this.observacion = data['observacion'];
                                this.observacion = data['observacion'];
                                break;
                            }
                        }
                    }
                }
            }
        },
        mounted() {
            this.listarObservacion(1,this.buscar,this.criterio);
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
        position: absolute !important;
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
</style>
