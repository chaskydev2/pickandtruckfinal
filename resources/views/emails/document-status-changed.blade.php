<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualización de Documento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: {{ $document->status === 'aprobado' ? '#10b981' : ($document->status === 'rechazado' ? '#ef4444' : '#f59e0b') }};
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f3f4f6;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .status-box {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            border-left: 4px solid {{ $document->status === 'aprobado' ? '#10b981' : ($document->status === 'rechazado' ? '#ef4444' : '#f59e0b') }};
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            background-color: {{ $document->status === 'aprobado' ? '#d1fae5' : ($document->status === 'rechazado' ? '#fee2e2' : '#fef3c7') }};
            color: {{ $document->status === 'aprobado' ? '#065f46' : ($document->status === 'rechazado' ? '#991b1b' : '#92400e') }};
        }
        .comment-box {
            background-color: #fee2e2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #1e3a8a;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #1e40af;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
            font-size: 14px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #1e3a8a;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">
            @if($document->status === 'aprobado')
                ✓ Documento Aprobado
            @elseif($document->status === 'rechazado')
                ✗ Documento Rechazado
            @else
                Actualización de Documento
            @endif
        </h1>
    </div>
    
    <div class="content">
        <p>Hola <strong>{{ $document->user->name ?? 'Usuario' }}</strong>,</p>
        
        @if($document->status === 'aprobado')
            <p>¡Excelentes noticias! Tu documento <strong>{{ $document->requiredDocument->name ?? 'documento' }}</strong> ha sido revisado y <span style="color: #10b981; font-weight: bold;">APROBADO</span> por nuestro equipo.</p>
            
            <div class="status-box">
                <div class="info-item">
                    <span class="info-label">Documento:</span> {{ $document->requiredDocument->name ?? 'N/A' }}
                </div>
                <div class="info-item">
                    <span class="info-label">Estado:</span> <span class="status-badge">APROBADO</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Fecha de revisión:</span> {{ $document->updated_at->format('d/m/Y H:i') }}
                </div>
            </div>
            
            <p>Ya puedes continuar utilizando todas las funciones de la plataforma.</p>
            
        @elseif($document->status === 'rechazado')
            <p>Lamentablemente, tu documento <strong>{{ $document->requiredDocument->name ?? 'documento' }}</strong> ha sido <span style="color: #ef4444; font-weight: bold;">RECHAZADO</span>.</p>
            
            <div class="status-box">
                <div class="info-item">
                    <span class="info-label">Documento:</span> {{ $document->requiredDocument->name ?? 'N/A' }}
                </div>
                <div class="info-item">
                    <span class="info-label">Estado:</span> <span class="status-badge">RECHAZADO</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Fecha de revisión:</span> {{ $document->updated_at->format('d/m/Y H:i') }}
                </div>
            </div>
            
            @if($document->comments)
                <div class="comment-box">
                    <strong>Motivo del rechazo:</strong><br>
                    {{ $document->comments }}
                </div>
            @else
                <p><em>No se proporcionó un motivo específico para el rechazo.</em></p>
            @endif
            
            <p><strong>¿Qué debes hacer ahora?</strong></p>
            <ol>
                <li>Revisa el motivo del rechazo mencionado arriba</li>
                <li>Corrige los problemas indicados en tu documento</li>
                <li>Vuelve a subir el documento corregido desde tu panel de usuario</li>
            </ol>
            
        @else
            <p>El estado de tu documento <strong>{{ $document->requiredDocument->name ?? 'documento' }}</strong> ha sido actualizado.</p>
            
            <div class="status-box">
                <div class="info-item">
                    <span class="info-label">Documento:</span> {{ $document->requiredDocument->name ?? 'N/A' }}
                </div>
                <div class="info-item">
                    <span class="info-label">Estado:</span> <span class="status-badge">{{ strtoupper($document->status) }}</span>
                </div>
            </div>
        @endif
        
        <div style="text-align: center;">
            <a href="{{ config('app.url') }}/profile/documents" class="btn">
                Ver mis documentos
            </a>
        </div>
        
        @if($document->admin_notes)
            <div class="status-box" style="margin-top: 20px; background-color: #eff6ff;">
                <strong style="color: #1e3a8a;">Nota del administrador:</strong><br>
                {{ $document->admin_notes }}
            </div>
        @endif
        
        <div class="footer">
            <p>Si tienes alguna duda, por favor responde a este correo o contáctanos a través de nuestro panel de soporte.</p>
            <p><strong>Equipo PICK N TRUCK</strong></p>
            <p style="font-size: 12px; color: #9ca3af;">
                Este es un correo automático, por favor no respondas directamente a este mensaje.
            </p>
        </div>
    </div>
</body>
</html>
