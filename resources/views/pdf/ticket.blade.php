<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket #{{ $ticket->id }}</title>
    <style>
        /* Configuración del tamaño de papel térmico de 80mm */
        @page {
            margin: 0px;
            size: 80mm auto; /* Ajuste automático de altura según contenido */
        }
        body {
            font-family: 'Courier New', Courier, monospace; /* Fuente clásica de ticket */
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background-color: #fff;
            padding: 10px;
            margin: 0;
            width: 72mm; /* Espacio real imprimible */
        }
        .text-center {
            text-align: center;
        }
        .header {
            margin-bottom: 10px;
        }
        .logo {
            max-width: 120px;
            height: auto;
            margin: 5px auto;
            display: block;
            filter: grayscale(100%); /* Optimiza para impresión térmica */
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 5px 0;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .info-table td.label {
            font-weight: bold;
            width: 35%;
        }
        .numbers-box {
            background-color: #fff;
            border: 1px solid #000;
            border-radius: 5px;
            color: #000;
            text-align: center;
            padding: 8px;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 10px 0;
        }
        .footer {
            font-size: 11px;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <div class="text-center header">
        <div class="title">El Palomo Negro</div>
        <p style="margin: 2px 0; font-size: 11px;">Dinámicas y Sorteos</p>
        {{-- Comprobar si el logo existe localmente para evitar fallos de carga en DomPDF --}}
        @if(file_exists(public_path('logo_palomo.jpg')))
            <img src="{{ public_path('logo_palomo.jpg') }}" class="logo" alt="Logo">
        @endif
        <p style="margin: 5px 0; font-size: 10px;">Comprobante de Reserva</p>
        <p style="margin: 0; font-weight: bold;">TICKET: #{{ str_pad($ticket->id, 6, '0', STR_PAD_LEFT) }}</p>
    </div>

    <div class="divider"></div>

    <table class="info-table">
        <tr>
            <td class="label">Fecha:</td>
            <td>{{ $ticket->created_at->format('d/m/Y h:i A') }}</td>
        </tr>
        <tr>
            <td class="label">Cliente:</td>
            <td>{{ $ticket->customer_name }}</td>
        </tr>
        <tr>
            <td class="label">WhatsApp:</td>
            <td>{{ $ticket->customer_phone }}</td>
        </tr>
        <tr>
            <td class="label">Sorteo:</td>
            <td>{{ $ticket->raffle->title }}</td>
        </tr>
        <tr>
            <td class="label">Estado:</td>
            <td style="text-transform: uppercase; font-weight: bold;">
                {{ $ticket->payment_status === 'PAID' ? 'PAGADO' : 'PENDIENTE' }}
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="text-center" style="font-weight: bold; font-size: 11px; text-transform: uppercase;">
        Tus números de la suerte:
    </div>
    
    <div class="numbers-box">
        @php
            // Soporta tanto array directo como estructura {"numbers": [...]}
            $numbers = $ticket->ticket_numbers['numbers'] ?? $ticket->ticket_numbers ?? [];
        @endphp
        {{ is_array($numbers) ? implode(' - ', $numbers) : $numbers }}
    </div>

    <div class="divider"></div>

    <div class="text-center footer">
        <p style="margin: 0; font-weight: bold;">
            ¡Gracias por tu apoyo!
        </p>
        <p style="margin: 5px 0 0 0; font-size: 9px;">Conserva este ticket para reclamar tu premio.</p>
    </div>

</body>
</html>