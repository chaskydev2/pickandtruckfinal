<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Demo Recibida</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #10b981; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background-color: #f3f4f6; padding: 30px; border-radius: 0 0 8px 8px; }
        .info-box { background-color: white; padding: 20px; border-radius: 8px; margin-top: 20px; border-left: 4px solid #10b981; }
        .highlight-box { background-color: #dbeafe; border-left: 4px solid #1e3a8a; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #1e3a8a; color: white; text-decoration: none; border-radius: 6px; margin-top: 20px; }
        .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 14px; }
        .info-label { font-weight: bold; color: #1e3a8a; }
        .timeline-item { padding: 10px 0; border-left: 2px solid #10b981; padding-left: 15px; margin-left: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">✓ Solicitud de Demo Recibida</h1>
    </div>
    
    <div class="content">
        <p>Hola <strong>{{ $user->name }}</strong>,</p>
        
        <p>¡Gracias por tu interés en <strong>Pick & Truck</strong>! Hemos recibido tu solicitud para una demostración de nuestra plataforma.</p>
        
        <div class="info-box">
            <h3 style="margin-top: 0; color: #10b981;">Información de tu Solicitud</h3>
            <p><span class="info-label">Nombre:</span> {{ $user->name }}</p>
            <p><span class="info-label">Empresa:</span> {{ $user->company_name }}</p>
            <p><span class="info-label">Email:</span> {{ $user->email }}</p>
            <p><span class="info-label">Teléfono:</span> {{ $user->phone }}</p>
            <p><span class="info-label">Tipo:</span> {{ $user->role === 'forwarder' ? 'Agente de Carga' : 'Empresa de Transporte' }}</p>
            <p><span class="info-label">Fecha:</span> {{ $demoRequest->requested_at ? $demoRequest->requested_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</p>
        </div>

        <div class="highlight-box">
            <h3 style="margin-top: 0; color: #1e3a8a;">📋 Próximos Pasos</h3>
            <div class="timeline-item"><strong>Revisión de Solicitud</strong><br>Nuestro equipo está revisando tu información.</div>
            <div class="timeline-item"><strong>Contacto</strong><br>Nos pondremos en contacto contigo dentro de las próximas <strong>72 horas</strong> (3 días laborales).</div>
            <div class="timeline-item"><strong>Demostración</strong><br>Agendaremos una demostración personalizada de la plataforma.</div>
        </div>

        <p>Mientras tanto, si tienes alguna pregunta, no dudes en contactarnos.</p>

        <div style="text-align: center;">
            <a href="mailto:soporte@pickntruck.com" class="btn">Contactar Soporte</a>
        </div>

        <p style="margin-top: 30px;">Gracias por confiar en Pick & Truck.</p>
        
        <p style="margin-top: 20px;">Saludos cordiales,<br><strong>El equipo de Pick & Truck</strong></p>
    </div>

    <div class="footer">
        <p>Para consultas: <a href="mailto:soporte@pickntruck.com">soporte@pickntruck.com</a></p>
        <p>© {{ date('Y') }} Pick & Truck - Makoto Global Logistics and Trade LLC</p>
    </div>
</body>
</html>
