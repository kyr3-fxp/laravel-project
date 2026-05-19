/**
 * Bootstrap configuration for Laravel
 * Este archivo importa y configura las dependencias necesarias
 */

import axios from 'axios';

// Configurar axios
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Token CSRF si está disponible
const token = document.querySelector('meta[name="csrf-token"]')?.content;
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
}
