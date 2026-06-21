@if(session('swal_success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.Swal.fire({
            title: '¡Operación Exitosa!',
            text: "{{ session('swal_success') }}",
            icon: 'success',
            confirmButtonColor: '#d97706'
        });
    });
</script>
@endif

@if(session('toast_error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.Toast.fire({
            icon: 'error',
            title: "{{ session('toast_error') }}"
        });
    });
</script>
@endif