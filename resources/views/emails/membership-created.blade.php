<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Pick & Truck</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #1e3a8a 0%, #10b981 100%); color: white; padding: 30px 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background-color: #f3f4f6; padding: 30px; border-radius: 0 0 8px 8px; }
        .membership-box { background-color: white; padding: 20px; border-radius: 8px; margin-top: 20px; border-left: 4px solid #10b981; }
        .tier-badge { display: inline-block; padding: 10px 20px; border-radius: 20px; font-weight: bold; font-size: 18px; margin: 10px 0; }
        .highlight-box { background-color: #dbeafe; border-left: 4px solid #1e3a8a; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .warning-box { background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .feature-item { padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #10b981; color: white; text-decoration: none; border-radius: 6px; margin-top: 20px; font-weight: bold; }
        .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 14px; }
        .info-label { font-weight: bold; color: #1e3a8a; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 28px;">¡Bienvenido a Pick & Truck!</h1>
        <p style="margin: 10px 0 0 0;">Tu plataforma de conexión logística</p>
    </div>
    
    <div class="content">
        <p>Hola <strong>{{ $user->name }}</strong>,</p>
        
        <p>¡Felicitaciones! Tu registro en Pick & Truck ha sido exitoso. Estamos emocionados de tenerte como parte de nuestra comunidad.</p>
        
        <div class="membership-box">
            <h3 style="margin-top: 0; color: #10b981;">Tu Membresía</h3>
            
            <div class="tier-badge" style="
                @if($membership->tier === 'pioneros')
                    background-color: #fef3c7; color: #92400e;
                @elseif($membership->tier === 'visionarios')
                    background-color: #dbeafe; color: #1e3a8a;
                @else
                    background-color: #fee2e2; color: #991b1b;
                @endif
            ">
                @if($membership->tier === 'pioneros')
                    🌟 PIONEROS
                @elseif($membership->tier === 'visionarios')
                    🔮 VISIONARIOS
                @else
                    🛡️ CONSERVADORES
                @endif
            </div>
            
            <p><span class="info-label">Plan:</span> {{ $membership->billing_cycle === 'monthly' ? 'Mensual' : 'Anual' }}</p>
            
            @if($membership->price_paid)
            <p><span class="info-label">Monto:</span> 
                @if($membership->tier === 'pioneros' && $membership->billing_cycle === 'annually')
                    3333 BOB
                @else
                    ${{ number_format($membership->price_paid, 2) }}
                @endif
            </p>
            @endif
            
            <p><span class="info-label">Estado:</span> <span style="color: #f59e0b; font-weight: bold;">{{ $membership->status === 'pending' ? 'Pendiente de Verificación' : 'Activa' }}</span></p>
        </div>

        <div class="warning-box">
            <strong>⚠️ Verificación de Cuenta</strong><br>
            Tu cuenta está en proceso de verificación. Nuestro equipo revisará tu información en las próximas <strong>24-48 horas</strong>. Una vez aprobada, tendrás acceso completo a la plataforma.
        </div>

        <div style="background-color: white; padding: 20px; border-radius: 8px; margin-top: 15px;">
            <h3 style="margin-top: 0; color: #1e3a8a;">Funcionalidades</h3>
            <div class="feature-item">✓ Coincidencia carga-camión en tiempo real</div>
            <div class="feature-item">✓ Acceso completo a la red de {{ $user->role === 'forwarder' ? 'transportistas' : 'agentes de carga' }}</div>
            <div class="feature-item">✓ Sistema de cotizaciones y ofertas</div>
            <div class="feature-item">✓ Seguimiento de tus operaciones</div>
            @if($membership->tier === 'pioneros')
            <div class="feature-item">✓ Soporte prioritario 24/7</div>
            <div class="feature-item">✓ Análisis avanzado y rastreo de beneficios</div>
            @endif
        </div>

        <div class="highlight-box">
            <h3 style="margin-top: 0;">📋 Próximos Pasos</h3>
            <ol style="margin: 10px 0; padding-left: 20px;">
                <li>Espera la verificación de tu información y comprobante de pago.</li>
                <li>Recibirás un email cuando tu cuenta sea verificada.</li>
                <li>Completa tu perfil empresarial.</li>
                <li>Comienza a publicar ofertas o buscar oportunidades.</li>
            </ol>
        </div>

        <div style="text-align: center;">
            <a href="https://app.pickntruck.com" class="btn">Ir a la Plataforma</a>
        </div>

        <p style="margin-top: 30px;">Si tienes preguntas, nuestro equipo de soporte está disponible para ayudarte.</p>
        
        <p>¡Gracias por unirte a Pick & Truck!<br><strong>El equipo de Pick & Truck</strong></p>
    </div>

    <div class="footer">
        <p>Para soporte: <a href="mailto:soporte@pickntruck.com">soporte@pickntruck.com</a></p>
        <p>© {{ date('Y') }} Pick & Truck - Makoto Global Logistics and Trade LLC</p>
    </div>
</body>
</html>
