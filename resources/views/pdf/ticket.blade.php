<div style="font-family: sans-serif; padding: 20px;">
    <h1>El Palomo Negro - Comprobante de Rifa</h1>
    {{-- Imagen o logotimo --}}
    <img src="{{ asset('logo_palomo.jpg') }}" alt="Logo" style="max-width: 200px; height: auto;">
    <hr>
    <p><strong>Cliente:</strong> {{ $ticket->customer_name }}</p>
    <p><strong>Teléfono:</strong> {{ $ticket->customer_phone }}</p>
    <p><strong>Rifa:</strong> {{ $ticket->raffle->title }}</p>
    <p><strong>Números:</strong> {{ implode(', ', $ticket->ticket_numbers) }}</p>
    <p><strong>Estado del pago:</strong> {{ __($ticket->payment_status) }}</p>
    <br>
    <p>¡Gracias por tu compra! 🍀</p>
</div>