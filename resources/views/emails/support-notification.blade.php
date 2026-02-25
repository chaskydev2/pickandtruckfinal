<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #1e3a8a; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background-color: #f3f4f6; padding: 30px; border-radius: 0 0 8px 8px; }
        .data-box { background-color: white; padding: 20px; border-radius: 8px; margin-top: 20px; }
        .data-item { padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .data-item:last-child { border-bottom: none; }
        .data-label { font-weight: bold; color: #1e3a8a; display: inline-block; width: 180px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">{{ $subject }}</h1>
    </div>
    
    <div class="content">
        <p>Se ha recibido una nueva solicitud desde el landing page.</p>
        
        <div class="data-box">
            @foreach($data as $key => $value)
            <div class="data-item">
                <span class="data-label">{{ $key }}:</span>
                <span>{{ $value }}</span>
            </div>
            @endforeach
        </div>

        <p style="margin-top: 20px;">
            <strong>Fecha:</strong> {{ date('d/m/Y H:i:s') }}
        </p>
    </div>
</body>
</html>
