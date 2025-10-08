import 'bootstrap';
import axios from 'axios';

// Configuración de Axios
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Configuración de Bootstrap
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// La inicialización de Echo se ha movido a app.js