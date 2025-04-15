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
                <a href="{{ route('register') }}" class="btn btn-primary btn-hero">¡Empezá Gratis!</a>
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
            <h2 class="text-center mb-5">Planes para todos los vendedores</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="pricing-card">
                        <h3>Gratis</h3>
                        <p class="lead">$0/mes</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>1 cuenta de Mercado Libre</li>
                            <li><i class="fas fa-check text-success me-2"></i>Análisis básico de ventas</li>
                            <li><i class="fas fa-check text-success me-2"></i>Seguimiento de 1 competidor</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary">Empezar Gratis</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="pricing-card">
                        <h3>Pro</h3>
                        <p class="lead">Consultar precio</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Multicuentas ilimitadas</li>
                            <li><i class="fas fa-check text-success me-2"></i>Estadísticas avanzadas</li>
                            <li><i class="fas fa-check text-success me-2"></i>Seguimiento de competidores ilimitado</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-primary">Probar Ahora</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="pricing-card">
                        <h3>Enterprise</h3>
                        <p class="lead">Consultar precio</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Soporte prioritario</li>
                            <li><i class="fas fa-check text-success me-2"></i>Informes personalizados</li>
                            <li><i class="fas fa-check text-success me-2"></i>Integraciones avanzadas</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary">Contactar</a>
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
