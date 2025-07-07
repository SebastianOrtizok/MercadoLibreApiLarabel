<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a MLDataTrends</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #007bff;
            color: #ffffff;
            padding: 10px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            padding: 20px;
        }
        h1, h2 {
            color: #007bff;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            padding: 10px;
            border-top: 1px solid #eee;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Bienvenido a MLDataTrends!</h1>
        </div>
        <div class="content">
            <p>Estimado/a {{ $user->name }},</p>
            <p>¡Bienvenido/a a <strong>MLDataTrends</strong>! Estamos emocionados de que te hayas unido a nuestra plataforma diseñada para ayudarte a analizar y optimizar tus estrategias en MercadoLibre. Tu cuenta ha sido creada con éxito. Aquí están los detalles de tu cuenta:</p>
            <p><strong>Usuario:</strong> {{ $user->email }}</p>
            <p>Para que puedas aprovechar al máximo todas las funcionalidades de nuestro sistema, es <strong>fundamental</strong> que completes un paso inicial: generar tu token de acceso.</p>

            <h2>¿Por qué es importante el token?</h2>
            <p>El token es la clave que conecta tu cuenta de MercadoLibre con MLDataTrends, permitiéndonos acceder a la información necesaria para brindarte análisis detallados, tendencias de mercado y herramientas personalizadas. Sin este token, el sistema no podrá funcionar correctamente.</p>

            <h2>¿Cómo generar tu token?</h2>
            <ol>
                <li>Inicia sesión en tu cuenta de <a href="https://www.mldatatrends.com">MLDataTrends</a>.</li>
                <li>Al ingresar, te aparecerá un botón grande en la pantalla principal que dice:</li>
                <blockquote>
                    <strong>Dashboard MercadoLibre</strong><br>
                    <em>Paso 1: Configura tu Token de Mercado Libre</em><br>
                    Para comenzar a gestionar tus cuentas, publicaciones y ventas en MercadoLibre, necesitas generar un token de acceso. Este token permitirá que nuestra plataforma se conecte de forma segura a tu cuenta de MercadoLibre y sincronice tus datos automáticamente.
                </blockquote>
                <li>Haz clic en "Generar Token" y sigue las instrucciones para autorizar la conexión con MercadoLibre.</li>
                <li>Serás redirigido/a a una página de MercadoLibre para otorgar los permisos necesarios.</li>
            </ol>

            <h2>Aclaración sobre el mensaje de MercadoLibre</h2>
            <p>Durante el proceso de autorización, es posible que MercadoLibre muestre un mensaje indicando que nuestro sitio "no es confiable" o que "no está verificado". <strong>No te preocupes, esto es completamente normal</strong>. Este mensaje aparece porque MLDataTrends es una aplicación externa que interactúa con la API de MercadoLibre, y es parte del procedimiento estándar de seguridad de MercadoLibre para proteger a sus usuarios. Te aseguramos que nuestra plataforma cumple con los estándares de seguridad y que tus datos están protegidos.</p>

            <p>Una vez que generes el token, podrás disfrutar de todas las herramientas de MLDataTrends sin inconvenientes. Si tienes alguna duda o necesitas ayuda durante el proceso, nuestro equipo de soporte está disponible para ayudarte. Escríbenos a <a href="mailto:soporte@mldatatrends.com">soporte@mldatatrends.com</a> o consulta nuestra <a href="https://mldatatrends.com/preguntas-frecuentes">sección de preguntas frecuentes</a> en el sitio.</p>

            <p>¡Estamos ansiosos por ayudarte a potenciar tu estrategia en MercadoLibre! <a href="https://www.mldatatrends.com" class="button">Genera tu token ahora</a> y comienza a descubrir las tendencias que marcarán la diferencia.</p>

            <p>Saludos cordiales,<br>
            <strong>El equipo de MLDataTrends</strong></p>
        </div>
        <div class="footer">
            <p><a href="https://www.mldatatrends.com">www.mldatatrends.com</a> | <a href="mailto:soporte@mldatatrends.com">soporte@mldatatrends.com</a></p>
        </div>
    </div>
</body>
</html>
