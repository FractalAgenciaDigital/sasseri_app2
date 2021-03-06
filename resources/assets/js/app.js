/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require ('./bootstrap');

window.$ = window.jQuery =  require('jquery');
window.Vue = require('vue');

import Multiselect from 'vue-multiselect';
// ES6 Modules or TypeScript
import Swal from 'sweetalert2';
import Notifications from 'vue-notification'
// import Echo from 'laravel-echo';


window.Swal = Swal;
Vue.use(Notifications);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('rol', require('./components/Rol.vue'));
Vue.component('user', require('./components/User.vue'));
Vue.component('terceros', require('./components/Terceros.vue'));
Vue.component('modulo', require('./components/Modulo.vue'));
Vue.component('configgenerales', require('./components/ConfigGenerales.vue'));
Vue.component('zona', require('./components/Zona.vue'));
Vue.component('facturacion', require('./components/Facturacion.vue'));
Vue.component('facturacion_mobile', require('./components/FacturacionMobile.vue'));
Vue.component('articulo', require('./components/Articulo.vue'));
Vue.component('categoria', require('./components/Categoria.vue'));
Vue.component('presentacion', require('./components/Presentacion.vue'));
Vue.component('und_medida', require('./components/UndMedida.vue'));
Vue.component('concentracion', require('./components/Concentracion.vue'));
Vue.component('stock', require('./components/Stock.vue'));
Vue.component('ingreso', require('./components/Ingreso.vue'));
Vue.component('egreso', require('./components/Egreso.vue'));
Vue.component('cliente', require('./components/Cliente.vue'));
Vue.component('con_tarifario', require('./components/ConTarifario.vue'));
Vue.component('iva', require('./components/Iva.vue'));
Vue.component('cajas', require('./components/Cajas.vue'));
Vue.component('cajas_admin', require('./components/CajasAdmin.vue'));
Vue.component('cierrescaja', require('./components/CierresXCaja.vue'));
Vue.component('informes', require('./components/Informes.vue'));
Vue.component('informe_arqueo', require('./components/InformeArqueo.vue'));
Vue.component('informe_producto', require('./components/InformeProducto.vue'));
Vue.component('informe_categoria', require('./components/InformeCategoria.vue'));
Vue.component('cuentasxcobrar', require('./components/CuentasxCobrar.vue'));
Vue.component('cuentasxpagar', require('./components/CuentasxPagar.vue'));
Vue.component('punto_venta', require('./components/PuntoVenta.vue'));
Vue.component('impresora', require('./components/Impresora.vue'));
Vue.component('cocina', require('./components/Cocina.vue'));
Vue.component('observacion', require('./components/Observacion.vue'));
Vue.component('historial', require('./components/Historial.vue'));
Vue.component('gastos', require('./components/Gastos.vue'));
Vue.component('multiselect', Multiselect);

const app = new Vue({
    el: '#app',
    data: {
        menu: 0,       
        ruta: 'http://localhost/sasseri_app2/public',
        permisosUser: {
            'leer': 1,
            'escribir': 1,
            'crear': 1,
            'actualizar': 1,
            'anular': 1,
        },
      
    },
    created(){
      
    },
    
    mounted() {
        let me = this;
        var url = this.ruta + '/permisos/listarPermisosLogueado';
        axios.get(url).then(function(response) {
            me.permisosUser = response.data.permisosLogueado;
        })
        .catch(function(error) {
            console.log(error);
        });
    }
});