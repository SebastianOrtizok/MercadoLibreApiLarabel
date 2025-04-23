<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Optimiza tus ventas en Mercado Libre con nuestra plataforma: gestiona multicuentas, analiza competidores, controla stock y maximiza promociones. ¡Prueba gratis hoy!">
    <meta name="keywords" content="Mercado Libre, ecommerce, ventas online, gestión de stock, análisis de competidores, promociones, estadísticas de ventas, multicuentas">
    <meta name="author" content="Plataforma de Gestión">
    <meta name="robots" content="index, follow">
    <title>Plataforma de Gestión para Mercado Libre - Potenciá tus Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content container text-center">
            <h1>Potenciá tus Ventas en Mercado Libre</h1>
            <p class="lead">La plataforma definitiva para gestionar multicuentas, analizar competidores, optimizar stock y maximizar tus ganancias.</p>
            <div class="hero-buttons">
                <a href="{{ route('register') }}" class="btn btn-primary btn-hero">¡Registrate Gratis!</a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-hero">Iniciar Sesión</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">Todo lo que necesitás para dominar Mercado Libre</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <i class="fas fa-users"></i>
                        <h3>Gestión de Multicuentas</h3>
                        <p>Controlá todas tus cuentas de Mercado Libre desde un solo lugar, con datos detallados y sincronización en tiempo real.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <i class="fas fa-search"></i>
                        <h3>Seguimiento de Competidores</h3>
                        <p>Monitoreá publicaciones y cambios de tus competidores al instante para ajustar tu estrategia.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <i class="fas fa-chart-bar"></i>
                        <h3>Estadísticas Avanzadas</h3>
                        <p>Analizá ventas por día, top 10 productos, facturación y más, con filtros por fechas personalizados.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <i class="fas fa-box"></i>
                        <h3>Gestión de Stock</h3>
                        <p>Calculá días de stock, manejá fulfillment y depósito, y recibí alertas de stock crítico.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <i class="fas fa-tags"></i>
                        <h3>Promociones Inteligentes</h3>
                        <p>Listá y renová promociones fácilmente para destacar tus productos y aumentar ventas.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <i class="fas fa-file-excel"></i>
                        <h3>Exportación a Excel</h3>
                        <p>Descargá reportes de ventas, stock y publicaciones en Excel con un solo clic.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <i class="fas fa-star"></i>
                        <h3>Optimización de Catálogo</h3>
                        <p>Recibí sugerencias para convertir tus publicaciones en ganadoras y destacar en el catálogo.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <i class="fas fa-barcode"></i>
                        <h3>SKUs Personalizados</h3>
                        <p>Agregá tus propios SKUs para filtrar y buscar productos rápidamente.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <i class="fas fa-tachometer-alt"></i>
                        <h3>Panel de Control</h3>
                        <p>Visualizá métricas clave como productos por estado y stock crítico en un solo lugar.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
<section class="pricing-section py-5">
    <div class="container">
        <h2 class="text-center mb-5">Comienza a gestionar tu cuenta de Mercado Libre</h2>
        <div class="row">
            <!-- Opción 1: Usuario Test -->
            <div class="col-md-4 mb-4">
                <div class="pricing-card" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; text-align: center;">
                    <h3 style="font-size: 1.5rem; color: #333;">Prueba con usuario test</h3>
                    <p class="lead" style="font-size: 1.2rem; color: #666;">Recorre el programa gratis</p>
                    <p style="font-size: 1rem; color: #666; margin-bottom: 20px;">
                        Ingresá con las siguientes credenciales para explorar las funcionalidades básicas:
                    </p>
                    <div style="background-color: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                        <p style="margin: 0; font-weight: bold; color: #333;">Usuario: test@test.com</p>
                        <p style="margin: 0; font-weight: bold; color: #333;">Contraseña: test1234</p>
                    </div>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary" style="padding: 10px 20px; font-weight: bold;">Iniciar Sesión</a>
                </div>
            </div>

            <!-- Opción 2: Regístrate y Navega Gratis -->
            <div class="col-md-4 mb-4">
                <div class="pricing-card" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; text-align: center;">
                    <h3 style="font-size: 1.5rem; color: #333;">Regístrate y navega gratis</h3>
                    <p class="lead" style="font-size: 1.2rem; color: #666;">Prueba gratuita de 7 días</p>
                    <p style="font-size: 1rem; color: #666; margin-bottom: 20px;">
                        Gestioná tu token y accedé a todas las funcionalidades sin cargo durante 7 días.
                    </p>
                    <ul class="list-unstyled" style="margin-bottom: 20px;">
                        <li><i class="fas fa-check text-success me-2"></i>Multicuentas ilimitadas</li>
                        <li><i class="fas fa-check text-success me-2"></i>Estadísticas avanzadas</li>
                        <li><i class="fas fa-check text-success me-2"></i>Seguimiento de competidores</li>
                    </ul>
                    <a href="https://mercadolibreapi.onrender.com/register" class="btn btn-primary" style="padding: 10px 20px; font-weight: bold;">Registrarme</a>
                </div>
            </div>

            <!-- Opción 3: Adquirí un Plan -->
            <div class="col-md-4 mb-4">
                <div class="pricing-card" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; text-align: center;">
                    <h3 style="font-size: 1.5rem; color: #333;">Adquirí uno de nuestros planes</h3>
                    <p class="lead" style="font-size: 1.2rem; color: #666;">Acceso completo y soporte</p>
                    <p style="font-size: 1rem; color: #666; margin-bottom: 20px;">
                        Elegí el plan que mejor se adapte a tus necesidades y comenzá a optimizar tus ventas.
                    </p>
                    <ul class="list-unstyled" style="margin-bottom: 20px;">
                        <li><i class="fas fa-check text-success me-2"></i>Soporte prioritario</li>
                        <li><i class="fas fa-check text-success me-2"></i>Informes personalizados</li>
                        <li><i class="fas fa-check text-success me-2"></i>Integraciones avanzadas</li>
                    </ul>
                    <a href="https://mercadolibreapi.onrender.com/plans" class="btn btn-outline-primary" style="padding: 10px 20px; font-weight: bold;">Ver Planes</a>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- CTA Section -->
    <section class="cta-section py-5">
        <div class="container text-center">
            <h2>¡Transformá tu negocio en Mercado Libre hoy!</h2>
            <p>Unite a miles de vendedores que ya optimizan sus ventas con nuestra plataforma.</p>
            <a href="{{ route('register') }}" class="btn btn-primary btn-hero">Comenzá Gratis</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Sobre Nosotros</h5>
                    <p>Potenciamos a los vendedores de Mercado Libre con herramientas avanzadas para crecer y competir.</p>
                </div>
                <div class="col-md-4">
                    <h5>Enlaces Rápidos</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('login') }}">Iniciar Sesión</a></li>
                        <li><a href="{{ route('register') }}">Registrarse</a></li>
                        <li><a href="#">Soporte</a></li>
                        <li><a href="#">Términos y Condiciones</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contacto</h5>
                    <p><i class="fas fa-envelope me-2"></i> soporte@plataforma.com</p>
                    <p><i class="fas fa-phone me-2"></i> +54 11 1234-5678</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <hr class="bg-light">
            <p class="text-center mb-0">© {{ date('Y') }} Plataforma de Gestión. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
