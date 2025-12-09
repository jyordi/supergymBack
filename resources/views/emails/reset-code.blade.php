<!DOCTYPE html>
<html>
<head>
    <title>Recuperar Contraseña</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 10px; text-align: center;">
        
        <h2 style="color: #3b82f6;">SuperGym</h2>
        
        <p style="font-size: 16px; color: #555;">Hola,</p>
        <p style="font-size: 16px; color: #555;">Recibimos una solicitud para restablecer tu contraseña. Usa el siguiente código para continuar:</p>
        
        <div style="background-color: #f0f9ff; border: 1px solid #3b82f6; color: #3b82f6; font-size: 32px; font-weight: bold; padding: 15px; margin: 20px 0; letter-spacing: 5px; border-radius: 8px;">
            {{ $code }}
        </div>
        
        <p style="font-size: 14px; color: #999;">Si no solicitaste este cambio, ignora este correo.</p>
        
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        <small style="color: #bbb;">Este código expira en 15 minutos.</small>
    </div>

</body>
</html>