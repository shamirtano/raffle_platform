import './bootstrap';
import Swal from 'sweetalert2';

window.Swal = Swal;

// Configuración predeterminada para Toasters rápidos
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
});
window.Toast = Toast;

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
