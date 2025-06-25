<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Resuelve tus dudas sobre cómo usar MLDataTrends para gestionar competidores, analizar precios y optimizar ventas en MercadoLibre. ¡Consulta nuestras Preguntas Frecuentes!">
    <meta name="keywords" content="preguntas frecuentes mldatatrends, cómo seguir competidores mercadolibre, análisis de precios mercadolibre, gestión de competidores ml, tutorial mldatatrends">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/preguntas-frecuentes') }}">
    <title>Preguntas Frecuentes - MLDataTrends: Gestión de Ventas en MercadoLibre</title>

    <!-- Open Graph -->
    <meta property="og:title" content="Preguntas Frecuentes - MLDataTrends">
    <meta property="og:description" content="Aprendé cómo seguir competidores, analizar precios y optimizar tus ventas en MercadoLibre con nuestras Preguntas Frecuentes.">
    <meta property="og:url" content="{{ url('/preguntas-frecuentes') }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('images/faq.webp') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Preguntas Frecuentes - MLDataTrends">
    <meta name="twitter:description" content="Aprendé cómo seguir competidores, analizar precios y optimizar tus ventas en MercadoLibre con nuestras Preguntas Frecuentes.">
    <meta name="twitter:image" content="{{ asset('images/faq.webp') }}">

    <!-- Bootstrap y FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" media="print" onload="this.media='all'">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" media="print" onload="this.media='all'">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" media="print" onload="this.media='all'">
</head>
<body>
    <!-- Header -->
    <header role="banner">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">MLDataTrends</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Iniciar Sesión</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Registrarse</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('faq.index') }}">Preguntas Frecuentes</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- FAQ Section -->
    <section class="faq-section py-5" role="region" aria-label="Preguntas Frecuentes">
        <div class="container">
            <h1 class="text-center mb-5">Preguntas Frecuentes sobre MLDataTrends</h1>
            <p class="lead text-center mb-5">¿No sabés cómo empezar a usar MLDataTrends para gestionar tus ventas en MercadoLibre? Acá te explicamos todo paso a paso, desde seguir competidores hasta optimizar tu stock.</p>
            <div itemscope itemtype="https://schema.org/FAQPage">
                <div class="accordion" id="faqAccordion">
                    <!-- Pregunta 1 -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h2 class="accordion-header" id="faq1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false" aria-controls="collapse1">
                                <i class="fas fa-search me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Cómo seguir publicaciones de competidores en MercadoLibre?</span>
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>Para seguir publicaciones de competidores en MercadoLibre con MLDataTrends, seguí estos pasos:</p>
                                    <ol>
                                        <li>Iniciá sesión en tu cuenta de MLDataTrends.</li>
                                        <li>Dirigite a la sección <strong>Gestión de Competidores</strong>.</li>
                                        <li>Agregá un competidor ingresando su <em>Seller ID</em> o <em>Nickname</em> en el formulario.</li>
                                        <li>En la tabla de <strong>Publicaciones Descargadas</strong>, marcá los checkboxes de las publicaciones que querés seguir.</li>
                                        <li>Hacé clic en <strong>Seguir Publicación Seleccionada</strong> para guardar tus selecciones.</li>
                                    </ol>
                                    <p>Las publicaciones seguidas tendrán un fondo azul claro y serán más fáciles de monitorear para cambios en precios o descuentos.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 2 -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h2 class="accordion-header" id="faq2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                <i class="fas fa-user me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Qué es el Seller ID y cómo lo encuentro?</span>
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>El <em>Seller ID</em> es un número único que identifica a un vendedor en MercadoLibre. Para encontrarlo:</p>
                                    <ol>
                                        <li>Ingresá a una publicación del vendedor en MercadoLibre.</li>
                                        <li>Hacé clic en el nombre del vendedor para ir a su perfil.</li>
                                        <li>En la URL del perfil, buscá un número después de <code>/perfil/</code> (por ejemplo, <code>https://perfil.mercadolibre.com.ar/123456789</code>).</li>
                                        <li>Ese número (123456789) es el <em>Seller ID</em>.</li>
                                    </ol>
                                    <p>En MLDataTrends, también podés ingresar el <em>Nickname</em> del vendedor y la herramienta buscará el <em>Seller ID</em> automáticamente.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 3 -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h2 class="accordion-header" id="faq3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                <i class="fas fa-sync me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Cómo actualizo los datos de las publicaciones de un competidor?</span>
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>Para actualizar los datos de un competidor en MLDataTrends:</p>
                                    <ol>
                                        <li>Dirigite a la sección <strong>Gestión de Competidores</strong>.</li>
                                        <li>En la tabla de competidores, buscá el competidor que querés actualizar.</li>
                                        <li>Hacé clic en el botón <strong>Actualizar</strong> junto a su nombre.</li>
                                        <li>La herramienta recopilará los datos más recientes de MercadoLibre, como precios, descuentos y URLs.</li>
                                    </ol>
                                    <p>El proceso puede tomar unos minutos, dependiendo de la cantidad de publicaciones.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 4 -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h2 class="accordion-header" id="faq4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                <i class="fas fa-file-excel me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Cómo exporto las publicaciones de competidores a Excel?</span>
                            </button>
                        </h2>
                        <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>Para exportar publicaciones de competidores a un archivo Excel:</p>
                                    <ol>
                                        <li>Dirigite a la sección <strong>Gestión de Competidores</strong>.</li>
                                        <li>En la tabla de <strong>Publicaciones Descargadas</strong>, hacé clic en <strong>Exportar a Excel</strong>.</li>
                                        <li>Se descargará un archivo con todos los datos, como títulos, precios, URLs y estado de seguimiento.</li>
                                    </ol>
                                    <p>Este archivo te permite analizar los datos offline o compartirlos con tu equipo.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 5 -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h2 class="accordion-header" id="faq5">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                <i class="fas fa-filter me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Cómo filtro publicaciones en la sección de competidores?</span>
                            </button>
                        </h2>
                        <div id="collapse5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>Para filtrar publicaciones de competidores:</p>
                                    <ol>
                                        <li>En la sección <strong>Gestión de Competidores</strong>, hacé clic en <strong>Mostrar Filtros</strong>.</li>
                                        <li>Completá los campos como <em>Nickname</em>, <em>Título</em>, <em>Es Full</em> o <em>Following</em>.</li>
                                        <li>Seleccioná un criterio de orden (por ejemplo, precio o última actualización).</li>
                                        <li>Hacé clic en <strong>Filtrar</strong> para ver los resultados.</li>
                                    </ol>
                                    <p>Los filtros te ayudan a encontrar publicaciones específicas rápidamente.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <p>¿No encontraste la respuesta que buscás? <a href="{{ url('/contacto') }}" class="btn btn-primary">Contactanos</a></p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4" role="contentinfo">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Sobre Nosotros</h5>
                    <p>Potenciamos a los vendedores de MercadoLibre con herramientas avanzadas para crecer y competir.</p>
                </div>
                <div class="col-md-4">
                    <h5>Enlaces Rápidos</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('login') }}">Iniciar Sesión</a></li>
                        <li><a href="{{ route('register') }}">Registrarse</a></li>
                        <li><a href="{{ route('faq.index') }}">Preguntas Frecuentes</a></li>
                        <li><a href="{{ url('/terminos-y-condiciones') }}" target="_blank">Términos y Condiciones</a></li>
                        <li><a href="{{ url('/politica-privacidad') }}" target="_blank">Política de Privacidad</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contacto</h5>
                    <p><i class="fas fa-envelope me-2" aria-hidden="true"></i> soporte@mldatatrends.com</p>
                </div>
            </div>
            <hr class="bg-light">
            <p class="text-center mb-0">© 2025 MLDataTrends. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="//code.tidio.co/jo26sf9xwxm54yytrbdsaeflv2b1timh.js" async></script>
</body>
</html>
