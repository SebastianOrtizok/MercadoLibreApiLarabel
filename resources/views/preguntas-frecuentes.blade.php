<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Resuelve tus dudas sobre cómo usar MLDataTrends para gestionar cuentas, publicaciones y listado completo de artículos en MercadoLibre. ¡Consulta nuestras Preguntas Frecuentes detalladas!">
    <meta name="keywords" content="preguntas frecuentes mldatatrends, vincular cuenta mercadolibre, sincronizar artículos mercadolibre, ver publicaciones mercadolibre, listado completo artículos, tutorial mldatatrends, gestionar ventas mercadolibre">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/preguntas-frecuentes') }}">
    <title>Preguntas Frecuentes - MLDataTrends: Gestión de Ventas en MercadoLibre</title>

    <!-- Open Graph -->
    <meta property="og:title" content="Preguntas Frecuentes - MLDataTrends">
    <meta property="og:description" content="Aprendé cómo vincular tu cuenta de MercadoLibre, sincronizar artículos, gestionar publicaciones y ver tu listado completo con nuestras Preguntas Frecuentes detalladas.">
    <meta property="og:url" content="{{ url('/preguntas-frecuentes') }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('images/faq.webp') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Preguntas Frecuentes - MLDataTrends">
    <meta name="twitter:description" content="Aprendé cómo vincular tu cuenta de MercadoLibre, sincronizar artículos, gestionar publicaciones y ver tu listado completo con nuestras Preguntas Frecuentes detalladas.">
    <meta name="twitter:image" content="{{ asset('images/faq.webp') }}">

    <!-- Bootstrap y FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" media="print" onload="this.media='all'">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" media="print" onload="this.media='all'">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" media="print" onload="this.media='all'">

    <style>
        .faq-section h2 { font-size: 1.8rem; font-weight: 600; }
        .accordion-button { font-size: 1.2rem; }
        .accordion-body { font-size: 1rem; line-height: 1.6; }
        .faq-index { background-color: #f8f9fa; padding: 20px; border-radius: 8px; }
        .faq-index a { text-decoration: none; }
        .faq-index a:hover { text-decoration: underline; }
    </style>
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
                        <li class="nav-item"><a class="nav-link active" href="{{ route('faq.index') }}">Preguntas Frecuentes</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- FAQ Section -->
    <section class="faq-section py-5" role="region" aria-label="Preguntas Frecuentes">
        <div class="container">
            <h1 class="text-center mb-5">Preguntas Frecuentes sobre MLDataTrends</h1>
            <p class="lead text-center mb-5">¿No sabés cómo empezar a usar MLDataTrends para gestionar tus ventas en MercadoLibre? Acá te explicamos paso a paso cómo vincular tu cuenta, sincronizar artículos, gestionar publicaciones y ver tu listado completo, con ejemplos prácticos y soluciones a problemas comunes.</p>

            <!-- Índice de secciones -->
            <div class="faq-index mb-5">
                <h3>Índice</h3>
                    <ul class="list-unstyled">
                        <li><a href="#cuentasAccordion">Cuentas</a></li>
                        <li><a href="#publicacionesAccordion">Publicaciones</a></li>
                        <li><a href="#listadoCompletoAccordion">Listado completo</a></li>
                        <li><a href="#skuAccordion">SKU</a></li>
                        <li><a href="#ventasAccordion">Ventas</a></li>
                        <li><a href="#promocionesAccordion">Promociones</a></li>
                        <li><a href="#catalogoAccordion">Catálogo</a></li>
                        <li><a href="#estadisticasAccordion">Estadísticas</a></li>
                        <li><a href="#sincronizacionAccordion">Sincronización</a></li>
                    </ul>
            </div>

            <div itemscope itemtype="https://schema.org/FAQPage">
                <!-- Sección: Cuentas -->
                <h2 class="mb-4" id="cuentasAccordion">Cuentas</h2>
                <div class="accordion" id="cuentasAccordion">
                    <!-- Pregunta 1: Vincular cuenta -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="cuentas1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCuentas1" aria-expanded="false" aria-controls="collapseCuentas1">
                                <i class="fas fa-link me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Cómo vinculo mi cuenta de MercadoLibre a MLDataTrends?</span>
                            </button>
                        </h3>
                        <div id="collapseCuentas1" class="accordion-collapse collapse" data-bs-parent="#cuentasAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>Vincular tu cuenta de MercadoLibre a MLDataTrends es el primer paso para gestionar tus ventas y publicaciones. Este proceso usa la autenticación OAuth de MercadoLibre para conectar tu cuenta de manera segura. Seguí estos pasos:</p>
                                    <ol>
                                        <li>Iniciá sesión en MLDataTrends en <a href="https://mldatatrends.com/login">MLDataTrends.com/login</a>.</li>
                                        <li>En el panel de control, navegá a <strong>Cuentas</strong> (encontrarás el enlace en la barra lateral).</li>
                                        <li>Hacé clic en <strong>Vincular cuenta de MercadoLibre</strong>. Esto te redirigirá a la página de autorización de MercadoLibre.</li>
                                        <li>Iniciá sesión en MercadoLibre con tu email y contraseña (asegurá que sea la cuenta correcta si tenés varias).</li>
                                        <li>Aceptá los permisos que solicita MLDataTrends (por ejemplo, acceso a publicaciones y ventas).</li>
                                        <li>Una vez autorizado, serás redirigido a MLDataTrends, y verás tu cuenta listada con el <em>Seller ID</em> y el estado "Vinculada".</li>
                                    </ol>
                                    <p>Si la vinculación falla, verificá que tu navegador permita pop-ups y que tu cuenta de MercadoLibre esté activa. Este proceso corresponde a la funcionalidad del <code>AccountController</code>, que maneja la autenticación OAuth y guarda el token de acceso.</p>
                                    <p><strong>Tidio trigger sugerido</strong>: "vincular cuenta mercadolibre" → Mostrar esta FAQ.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 2: Información de la cuenta -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="cuentas2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCuentas2" aria-expanded="false" aria-controls="collapseCuentas2">
                                <i class="fas fa-info-circle me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Qué información puedo ver de mi cuenta de MercadoLibre en MLDataTrends?</span>
                            </button>
                        </h3>
                        <div id="collapseCuentas2" class="accordion-collapse collapse" data-bs-parent="#cuentasAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>La sección <strong>Cuentas</strong> te muestra un resumen detallado de tu cuenta de MercadoLibre vinculada, lo que te ayuda a verificar que todo esté configurado correctamente. Basado en el <code>AccountController</code>, esta sección incluye:</p>
                                    <ul>
                                        <li><strong>Seller ID</strong>: El identificador único de tu cuenta (por ejemplo, 123456789).</li>
                                        <li><strong>Nombre de la cuenta</strong>: El nombre o apodo asociado a tu perfil de MercadoLibre (por ejemplo, "TiendaEjemplo").</li>
                                        <li><strong>Estado de vinculación</strong>: Indica si la cuenta está activa, pendiente de autorización, o necesita revinculación (por ejemplo, "Token expirado").</li>
                                        <li><strong>Última sincronización</strong>: Fecha y hora de la última sincronización de datos (por ejemplo, "2025-06-25 10:30").</li>
                                        <li><strong>Permisos otorgados</strong>: Qué datos puede acceder MLDataTrends (publicaciones, ventas, etc.).</li>
                                    </ul>
                                    <p>Para acceder, iniciá sesión y hacé clic en <strong>Cuentas</strong> en el panel. Si no ves la información, asegurá que la cuenta esté vinculada correctamente. Este módulo usa <code>ConsultaMercadoLibreService</code> para obtener datos de la API de MercadoLibre.</p>
                                    <p><strong>Tidio trigger sugerido</strong>: "información cuenta mercadolibre" → Mostrar esta FAQ.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 3: Sincronizar artículos -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="cuentas3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCuentas3" aria-expanded="false" aria-controls="collapseCuentas3">
                                <i class="fas fa-sync-alt me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Cómo sincronizo mis artículos de MercadoLibre con MLDataTrends?</span>
                            </button>
                        </h3>
                        <div id="collapseCuentas3" class="accordion-collapse collapse" data-bs-parent="#cuentasAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>Sincronizar tus artículos es clave para que MLDataTrends pueda mostrar tus publicaciones, ventas y otros datos. La funcionalidad está implementada en el <code>AccountController</code> (método <code>primeraSincronizacionDB</code>) y usa el <code>ConsultaMercadoLibreService</code> para descargar datos vía la API de MercadoLibre. Seguí estos pasos:</p>
                                    <ol>
                                        <li>Iniciá sesión en MLDataTrends y navegá a <strong>Cuentas</strong>.</li>
                                        <li>Seleccioná la cuenta vinculada que querés sincronizar (si tenés varias, elegí una).</li>
                                        <li>Hacé clic en <strong>Iniciar sincronización</strong>. Esto ejecutará un proceso que descarga tus publicaciones activas, incluyendo título, precio, stock, y estado.</li>
                                        <li>Esperá a que el proceso termine (puede tomar unos minutos si tenés muchas publicaciones, por ejemplo, 500 artículos pueden tardar 2-3 minutos).</li>
                                        <li>Una vez finalizado, los datos aparecerán en <strong>Publicaciones</strong> y <strong>Listado completo</strong>.</li>
                                    </ol>
                                    <p>Podés verificar el progreso en la sección <strong>Sincronización</strong>. Si la sincronización no inicia, asegurá que tu token de MercadoLibre esté activo. Este proceso es esencial para usar las demás funcionalidades de MLDataTrends.</p>
                                    <p><strong>Tidio trigger sugerido</strong>: "sincronizar artículos mercadolibre" → Mostrar esta FAQ.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 4: Problemas con sincronización -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="cuentas4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCuentas4" aria-expanded="false" aria-controls="collapseCuentas4">
                                <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Por qué no veo mis publicaciones después de sincronizar?</span>
                            </button>
                        </h3>
                        <div id="collapseCuentas4" class="accordion-collapse collapse" data-bs-parent="#cuentasAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>Si no ves tus publicaciones después de sincronizar, puede haber varios motivos relacionados con el proceso de <code>ConsultaMercadoLibreService</code>. Revisá estos puntos:</p>
                                    <ul>
                                        <li><strong>Token expirado</strong>: El token de acceso de MercadoLibre puede haber caducado. Volvé a <strong>Cuentas</strong>, hacé clic en "Revincular" y seguí los pasos para autorizar nuevamente.</li>
                                        <li><strong>Sin publicaciones activas</strong>: Asegurá que tu cuenta de MercadoLibre tenga publicaciones activas. Podés verificarlo en <a href="https://www.mercadolibre.com.ar">MercadoLibre.com.ar</a>.</li>
                                        <li><strong>Errores de API</strong>: La API de MercadoLibre puede tener límites de consultas. Esperá 5-10 minutos y reintentá la sincronización.</li>
                                        <li><strong>Filtros aplicados</strong>: En <strong>Publicaciones</strong> o <strong>Listado completo</strong>, asegurá que no haya filtros (por ejemplo, "Activas" o búsqueda por título) que oculten los resultados.</li>
                                    </ul>
                                    <p>Si el problema persiste, revisá las notificaciones en <strong>Sincronización</strong> para ver mensajes de error específicos (por ejemplo, "API rate limit exceeded"). Contactá a support@mldatatrends.com si necesitás ayuda adicional.</p>
                                    <p><strong>Tidio trigger sugerido</strong>: "no veo publicaciones sincronizadas" → Mostrar esta FAQ.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Publicaciones -->
                <h2 class="mt-5 mb-4" id="publicacionesAccordion">Publicaciones</h2>
                <div class="accordion" id="publicacionesAccordion">
                    <!-- Pregunta 1: Ver publicaciones -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="publicaciones1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePublicaciones1" aria-expanded="false" aria-controls="collapsePublicaciones1">
                                <i class="fas fa-list me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Cómo veo mis publicaciones de MercadoLibre en MLDataTrends?</span>
                            </button>
                        </h3>
                        <div id="collapsePublicaciones1" class="accordion-collapse collapse" data-bs-parent="#publicacionesAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>La sección <strong>Publicaciones</strong> te permite visualizar todas tus publicaciones activas de MercadoLibre en un formato claro y organizado, usando datos sincronizados por el <code>ConsultaMercadoLibreService</code>. Esto es ideal para monitorear precios, stock y estados sin entrar a MercadoLibre. Seguí estos pasos:</p>
                                    <ol>
                                        <li>Iniciá sesión en MLDataTrends en <a href="https://mldatatrends.com/login">MLDataTrends.com/login</a>.</li>
                                        <li>En el panel de control, hacé clic en <strong>Publicaciones</strong> (en la barra lateral).</li>
                                        <li>Verás una lista con tus publicaciones, mostrando detalles como título (por ejemplo, "Zapatillas Nike Air"), precio ($10,000), stock (15 unidades), y estado ("Activa" o "Pausada").</li>
                                        <li>Podés ordenar la lista por cualquier columna (por ejemplo, precio descendente) haciendo clic en el encabezado.</li>
                                    </ol>
                                    <p>Para que las publicaciones aparezcan, primero debés vincular tu cuenta en <strong>Cuentas</strong> y sincronizar los datos en <strong>Sincronización</strong>. Si tenés muchas publicaciones (por ejemplo, más de 100), la carga inicial puede tomar unos segundos. Esta funcionalidad está soportada por el <code>ListadoArticulosController</code>, que consulta la base de datos local.</p>
                                    <p><strong>Tidio trigger sugerido</strong>: "ver publicaciones mercadolibre" → Mostrar esta FAQ.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 2: Buscar por ID -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="publicaciones2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePublicaciones2" aria-expanded="false" aria-controls="collapsePublicaciones2">
                                <i class="fas fa-search me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Cómo busco una publicación específica por ID en MLDataTrends?</span>
                            </button>
                        </h3>
                        <div id="collapsePublicaciones2" class="accordion-collapse collapse" data-bs-parent="#publicacionesAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>Si necesitás encontrar una publicación específica, MLDataTrends te permite buscar por el ID de MercadoLibre (MLA ID), que es el código único de cada publicación (por ejemplo, MLA123456789). Esta funcionalidad está implementada en el <code>ListadoArticulosController</code>. Seguí estos pasos:</p>
                                    <ol>
                                        <li>Navegá a <strong>Publicaciones</strong> en el panel de control.</li>
                                        <li>En el campo de búsqueda, ingresá el MLA ID completo (por ejemplo, MLA987654321).</li>
                                        <li>Hacé clic en el ícono de lupa o presioná Enter.</li>
                                        <li>Si la publicación está sincronizada, aparecerá con detalles como título, precio, stock y un enlace directo a MercadoLibre.</li>
                                    </ol>
                                    <p>Para encontrar el MLA ID, abrí la publicación en MercadoLibre y buscá el número en la URL (por ejemplo, <code>https://www.mercadolibre.com.ar/MLA-123456789</code>). Asegurá que la publicación esté sincronizada (revisá <strong>Sincronización</strong>) y que el ID sea correcto. Si no aparece, puede que la publicación esté pausada o no vinculada a la cuenta seleccionada.</p>
                                    <p><strong>Tidio trigger sugerido</strong>: "buscar publicación por id" → Mostrar esta FAQ.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 3: Filtrar por estado -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="publicaciones3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePublicaciones3" aria-expanded="false" aria-controls="collapsePublicaciones3">
                                <i class="fas fa-filter me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Cómo filtro mis publicaciones por estado o categoría en MLDataTrends?</span>
                            </button>
                        </h3>
                        <div id="collapsePublicaciones3" class="accordion-collapse collapse" data-bs-parent="#publicacionesAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>Filtrar tus publicaciones te ayuda a enfocarte en un grupo específico, como publicaciones activas o de una categoría particular. El <code>ListadoArticulosController</code> soporta filtros dinámicos para facilitar la gestión. Seguí estos pasos:</p>
                                    <ol>
                                        <li>En <strong>Publicaciones</strong>, localizá el panel de filtros en la parte superior de la lista.</li>
                                        <li>Seleccioná un estado en el menú desplegable (por ejemplo, "Activas", "Pausadas", "Finalizadas").</li>
                                        <li>Para filtrar por categoría, elegí una opción como "Electrónica" o "Ropa" (si está disponible en tu cuenta).</li>
                                        <li>Podés combinar con una búsqueda por palabra clave (por ejemplo, "Zapatillas" en el título).</li>
                                        <li>Hacé clic en <strong>Aplicar filtros</strong> para ver los resultados actualizados.</li>
                                    </ol>
                                    <p>Por ejemplo, para ver solo publicaciones activas de "Electrónica", seleccioná "Activas" y "Electrónica" en los filtros. Si no ves resultados, quitá los filtros o verificá que tus datos estén sincronizados en <strong>Sincronización</strong>. Esto usa el <code>ListadoArticulosService</code> para consultar la base de datos local.</p>
                                    <p><strong>Tidio trigger sugerido</strong>: "filtrar publicaciones mercadolibre" → Mostrar esta FAQ.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 4: No veo publicaciones -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="publicaciones4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePublicaciones4" aria-expanded="false" aria-controls="collapsePublicaciones4">
                                <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Por qué no veo todas mis publicaciones en MLDataTrends?</span>
                            </button>
                        </h3>
                        <div id="collapsePublicaciones4" class="accordion-collapse collapse" data-bs-parent="#publicacionesAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>Si no ves todas tus publicaciones en la sección <strong>Publicaciones</strong>, puede haber problemas relacionados con la vinculación, sincronización o filtros. El <code>ListadoArticulosController</code> depende de datos sincronizados por el <code>ConsultaMercadoLibreService</code>. Revisá estos puntos:</p>
                                    <ul>
                                        <li><strong>Cuenta no vinculada</strong>: Asegurá que tu cuenta esté activa en <strong>Cuentas</strong>. Si ves "Token expirado", revinculá siguiendo los pasos de vinculación.</li>
                                        <li><strong>Sincronización incompleta</strong>: Verificá en <strong>Sincronización</strong> si el proceso terminó correctamente. Si está en curso, esperá unos minutos.</li>
                                        <li><strong>Filtros aplicados</strong>: Quitá todos los filtros (estado, categoría, búsqueda) haciendo clic en "Restablecer filtros".</li>
                                        <li><strong>Sin publicaciones activas</strong>: Confirmá en MercadoLibre que tenés publicaciones activas. Por ejemplo, si todas están pausadas, no aparecerán en "Activas".</li>
                                    </ul>
                                    <p>Si el problema persiste, revisá las notificaciones en <strong>Sincronización</strong> para errores específicos (por ejemplo, "Error 429: Too Many Requests"). Intentá sincronizar nuevamente después de 10 minutos o contactá a support@mldatatrends.com.</p>
                                    <p><strong>Tidio trigger sugerido</strong>: "no veo publicaciones mldata" → Mostrar esta FAQ.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Listado completo -->
                <h2 class="mt-5 mb-4" id="listadoCompletoAccordion">Listado completo</h2>
                <div class="accordion" id="listadoCompletoAccordion">
                    <!-- Pregunta 1: Acceder al listado -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="listadoCompleto1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseListadoCompleto1" aria-expanded="false" aria-controls="collapseListadoCompleto1">
                                <i class="fas fa-table me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Cómo accedo al listado completo de mis artículos en MLDataTrends?</span>
                            </button>
                        </h3>
                        <div id="collapseListadoCompleto1" class="accordion-collapse collapse" data-bs-parent="#listadoCompletoAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>La sección <strong>Listado completo</strong> te ofrece una vista detallada de todos tus artículos sincronizados, ideal para gestionar tu inventario en MercadoLibre. Esta funcionalidad está soportada por el <code>ListadoArticulosController</code> y el <code>ListadoArticulosService</code>, que consultan la base de datos local. Seguí estos pasos:</p>
                                    <ol>
                                        <li>Iniciá sesión en MLDataTrends en <a href="https://mldatatrends.com/login">MLDataTrends.com/login</a>.</li>
                                        <li>En el panel de control, hacé clic en <strong>Listado completo</strong> (en la barra lateral).</li>
                                        <li>Verás una tabla con todos tus artículos, incluyendo título (por ejemplo, "Smartphone Samsung Galaxy"), SKU interno (por ejemplo, "SAM123"), precio ($50,000), stock (10 unidades), y estado ("Activo").</li>
                                        <li>Podés ordenar la tabla por cualquier columna, como precio o stock, para analizar rápidamente.</li>
                                    </ol>
                                    <p>Para que los artículos aparezcan, primero debés vincular tu cuenta en <strong>Cuentas</strong> y completar la sincronización en <strong>Sincronización</strong>. Esta sección es útil para vendedores con grandes inventarios, ya que permite exportar los datos a Excel (si está habilitado).</p>
                                    <p><strong>Tidio trigger sugerido</strong>: "ver listado completo artículos" → Mostrar esta FAQ.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 2: Filtrar artículos -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="listadoCompleto2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseListadoCompleto2" aria-expanded="false" aria-controls="collapseListadoCompleto2">
                                <i class="fas fa-filter me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Cómo filtro mis artículos por título o SKU en MLDataTrends?</span>
                            </button>
                        </h3>
                        <div id="collapseListadoCompleto2" class="accordion-collapse collapse" data-bs-parent="#listadoCompletoAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>Filtrar artículos en <strong>Listado completo</strong> te permite encontrar rápidamente productos específicos, ya sea por título o SKU interno. El <code>ListadoArticulosService</code> soporta búsquedas dinámicas en la base de datos. Seguí estos pasos:</p>
                                    <ol>
                                        <li>Navegá a <strong>Listado completo</strong> en el panel de control.</li>
                                        <li>En el campo de búsqueda, ingresá una palabra clave del título (por ejemplo, "Zapatillas") o un SKU interno (por ejemplo, "ZAP123").</li>
                                        <li>Seleccioná un estado opcional en el filtro (por ejemplo, "Activo" o "Pausado") para reducir los resultados.</li>
                                        <li>Hacé clic en el ícono de lupa o presioná Enter para aplicar la búsqueda.</li>
                                        <li>Los resultados mostrarán los artículos que coincidan, con detalles como precio, stock y enlace a MercadoLibre.</li>
                                    </ol>
                                    <p>Por ejemplo, si buscás "Zapatillas Nike" y filtrás por "Activo", verás solo las publicaciones activas de zapatillas Nike. Si no hay resultados, verificá que los datos estén sincronizados o quitá los filtros. Esta funcionalidad es ideal para gestionar inventarios grandes.</p>
                                    <p><strong>Tidio trigger sugerido</strong>: "filtrar artículos por sku" → Mostrar esta FAQ.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 3: Información disponible -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="listadoCompleto3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseListadoCompleto3" aria-expanded="false" aria-controls="collapseListadoCompleto3">
                                <i class="fas fa-info-circle me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Qué información puedo ver en el listado completo de MLDataTrends?</span>
                            </button>
                        </h3>
                        <div id="collapseListadoCompleto3" class="accordion-collapse collapse" data-bs-parent="#listadoCompletoAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>La sección <strong>Listado completo</strong> muestra todos los detalles de tus artículos sincronizados, extraídos por el <code>ListadoArticulosService</code>. Esto es útil para analizar tu inventario y tomar decisiones estratégicas. Los campos disponibles incluyen:</p>
                                    <ul>
                                        <li><strong>Título</strong>: Nombre de la publicación (por ejemplo, "Auriculares Sony Inalámbricos").</li>
                                        <li><strong>SKU interno</strong>: Código único asignado por vos (por ejemplo, "AUR456").</li>
                                        <li><strong>Precio</strong>: Precio actual en MercadoLibre (por ejemplo, $8,000).</li>
                                        <li><strong>Stock</strong>: Cantidad disponible (por ejemplo, 20 unidades).</li>
                                        <li><strong>Estado</strong>: Activo, pausado, finalizado, etc.</li>
                                        <li><strong>Condición</strong>: Nuevo, usado, reacondicionado.</li>
                                        <li><strong>Enlace</strong>: URL directa a la publicación en MercadoLibre (por ejemplo, <code>https://www.mercadolibre.com.ar/MLA-123456789</code>).</li>
                                        <li><strong>Categoría</strong>: Categoría asignada (por ejemplo, "Electrónica").</li>
                                    </ul>
                                    <p>Accedé a esta sección desde el panel de control. Podés exportar la tabla a Excel para análisis offline (si está habilitado). Asegurá que los datos estén sincronizados en <strong>Sincronización</strong> para ver información actualizada.</p>
                                    <p><strong>Tidio trigger sugerido</strong>: "información listado completo" → Mostrar esta FAQ.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 4: No veo artículos -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="listadoCompleto4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseListadoCompleto4" aria-expanded="false" aria-controls="collapseListadoCompleto4">
                                <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Por qué no veo artículos en el listado completo de MLDataTrends?</span>
                            </button>
                        </h3>
                        <div id="collapseListadoCompleto4" class="accordion-collapse collapse" data-bs-parent="#listadoCompletoAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>Si la sección <strong>Listado completo</strong> está vacía, puede haber problemas con la configuración o los datos. El <code>ListadoArticulosController</code> depende de datos sincronizados por el <code>ConsultaMercadoLibreService</code>. Revisá estos puntos:</p>
                                    <ul>
                                        <li><strong>Sin cuentas vinculadas</strong>: Asegurá que tu cuenta de MercadoLibre esté vinculada en <strong>Cuentas</strong>. Si no, seguí los pasos de vinculación.</li>
                                        <li><strong>Sincronización incompleta</strong>: Verificá en <strong>Sincronización</strong> si el proceso terminó. Si ves "En curso", esperá a que finalice.</li>
                                        <li><strong>Filtros aplicados</strong>: Quitá todos los filtros (búsqueda por título, SKU, o estado) haciendo clic en "Restablecer filtros".</li>
                                        <li><strong>Sin publicaciones</strong>: Confirmá en MercadoLibre que tenés publicaciones activas. Por ejemplo, si todas están pausadas, no aparecerán en el listado predeterminado.</li>
                                        <li><strong>Errores de sincronización</strong>: Revisá las notificaciones en <strong>Sincronización</strong> para errores como "API rate limit exceeded" o "Invalid token".</li>
                                    </ul>
                                    <p>Si el problema persiste, intentá sincronizar nuevamente o contactá a support@mldatatrends.com con el mensaje de error. También podés verificar manualmente en MercadoLibre para confirmar que los datos existen.</p>
                                    <p><strong>Tidio trigger sugerido</strong>: "no veo artículos listado completo" → Mostrar esta FAQ.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

           <div itemscope itemtype="https://schema.org/FAQPage">
    <!-- Sección: SKU -->
    <h2 class="mt-5 mb-4" id="skuAccordion">SKU</h2>
    <div class="accordion" id="skuAccordion">
        <!-- Pregunta 1: Ver SKU -->
        <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
            <h3 class="accordion-header" id="sku1">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSku1" aria-expanded="false" aria-controls="collapseSku1">
                    <i class="fas fa-barcode me-2" aria-hidden="true"></i>
                    <span itemprop="name">¿Cómo veo los SKU de mis artículos en MLDataTrends?</span>
                </button>
            </h3>
            <div id="collapseSku1" class="accordion-collapse collapse" data-bs-parent="#skuAccordion">
                <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div itemprop="text">
                        <p>La sección <strong>SKU</strong> te permite visualizar y gestionar los códigos SKU internos de tus artículos en MercadoLibre, lo que facilita el control de inventario. Basado en el <code>SkuController</code>, seguí estos pasos:</p>
                        <ol>
                            <li>Iniciá sesión en <a href="https://mldatatrends.com/login">MLDataTrends.com/login</a>.</li>
                            <li>Navegá a <strong>SKU</strong> en el panel de control (barra lateral).</li>
                            <li>Verás una tabla con tus artículos, mostrando el SKU interno (por ejemplo, "ZAP123"), título (por ejemplo, "Zapatillas Nike Air"), precio, y stock.</li>
                            <li>Podés ordenar por SKU o buscar un código específico (por ejemplo, "ZAP123") en el campo de búsqueda.</li>
                        </ol>
                        <p>Los datos se obtienen de la base de datos local tras sincronizar tu cuenta en <strong>Sincronización</strong>. Si no ves los SKU, asegurá que hayas vinculado tu cuenta en <strong>Cuentas</strong> y sincronizado los artículos. Esta funcionalidad es ideal para vendedores que usan códigos internos para identificar productos.</p>
                        <p><strong>Tidio trigger sugerido</strong>: "ver sku artículos" → Mostrar esta FAQ.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pregunta 2: Buscar por SKU -->
        <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
            <h3 class="accordion-header" id="sku2">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSku2" aria-expanded="false" aria-controls="collapseSku2">
                    <i class="fas fa-search me-2" aria-hidden="true"></i>
                    <span itemprop="name">¿Cómo busco un artículo por SKU en MLDataTrends?</span>
                </button>
            </h3>
            <div id="collapseSku2" class="accordion-collapse collapse" data-bs-parent="#skuAccordion">
                <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div itemprop="text">
                        <p>Buscar un artículo por SKU te permite localizar rápidamente un producto específico. El <code>SkuController</code> soporta búsquedas dinámicas en la base de datos. Seguí estos pasos:</p>
                        <ol>
                            <li>En el panel, hacé clic en <strong>SKU</strong>.</li>
                            <li>Ingresá el SKU interno en el campo de búsqueda (por ejemplo, "SAM456" para un smartphone Samsung).</li>
                            <li>Presioná Enter o hacé clic en el ícono de lupa.</li>
                            <li>Si el SKU existe, verás el artículo con detalles como título, precio, stock, y enlace a MercadoLibre.</li>
                        </ol>
                        <p>Asegurá que el SKU esté sincronizado (revisá <strong>Sincronización</strong>) y que coincida exactamente con el código ingresado. Si no aparece, verificá que la cuenta vinculada en <strong>Cuentas</strong> tenga artículos con ese SKU. Este método es útil para gestionar inventarios grandes o verificar discrepancias de stock.</p>
                        <p><strong>Tidio trigger sugerido</strong>: "buscar por sku" → Mostrar esta FAQ.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pregunta 3: Actualizar SKU -->
        <div class="accordion-item"mainEntity" itemtype="https://schema.org/Question">
            <h3 class="accordion-header" id="sku3">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSku3" aria-expanded="false" aria-controls="collapseSku3">
                    <i class="fas fa-edit me-2" aria-hidden="true">
                        <span itemprop="name">¿Puedo actualizar los SKU de mis productos en MLDataTrends?</span>
                    </h3>
                    <button>
                </button>
            </h3>
            <div id="collapseSku3" class="collapse accordion-collapse collapse" data-bs-parent="#skuAccordion">
                <div class="accordion-body" itemscope="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div class="itemprop" itemprop="text">
                        <p>MLDataTrends permite actualizar los SKU internos de tus productos para mantener tu inventario ordenado. Aunque el <code>SkuController</code> no detalla esta función específica en el código proporcionado, podés gestionar SKU a través de la interfaz. Seguí estos pasos:</p>
                        <ol>
                            <li>Navegá a <strong>SKU</strong> en el panel de control.</li>
                            <li>Buscá el artículo usando el campo de búsqueda (por ejemplo, "ZAP123").</li>
                            <li>Hacé clic en el artículo para abrir los detalles y seleccioná la opción de edición (si está disponible).</li>
                            <li>Ingresá el nuevo SKU (por ejemplo, cambiar "ZAP123" a "ZAP789") y guardá los cambios.</li>
                        </ol>
                        <p>Los cambios se sincronizarán con tu base de datos local, pero no modifican los SKU en MercadoLibre directamente. Asegurá que los nuevos SKU sean únicos para evitar duplicados. Si no ves la opción de edición, contacta a support@mldatatrends.com para confirmar si esta función está habilitada en tu plan.</p>
                        <p><strong>Tidio trigger sugerido</strong>: "actualizar sku producto" → Mostrar esta FAQ.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Sección: Ventas -->
    <h2 class="mt-5 mb-4" id="ventasAccordion">Ventas</h2>
    <div class="accordion" id="ventasAccordion">
        <!-- Pregunta 1: Ver ventas -->
        <div class="accordion-item" itemscope="mainEntity" itemtype="https://schema.org/Question">
            <h3 class="accordion-header" id="ventas1">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVentas1" aria-expanded="false" aria-controls="collapseVentas1">
                    <i class="fas fa-shopping-cart me-2" aria-hidden="true"></i>
                    <span itemprop="name">¿Cómo veo mis ventas de MercadoLibre en MLDataTrends?</span>
                </button>
            </h3>
            <div id="collapseVentas1" class="accordion-collapse collapse" data-bs-parent="#ventasAccordion">
                <div class="accordion-body" itemscope="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div class="itemprop" itemprop="text">
                        <p>La sección <strong>Ventas</strong> te muestra un resumen detallado de tus órdenes en MercadoLibre, sincronizadas vía el <code>SalesController</code>. Esto te ayuda a monitorear tus ingresos y rendimiento. Seguí estos pasos:</p>
                        <ol>
                            <li>Iniciá sesión en <a href="https://mldatatrends.com/login">MLDataTrends.com/login</a>.</li>
                            <li>Hacé clic en <strong>Ventas</strong> en la barra lateral.</li>
                            <li>Verás una tabla con tus órdenes, incluyendo ID de orden (por ejemplo, "2000001234567890"), fecha de venta (por ejemplo, "2025-06-20"), producto (por ejemplo, "Auriculares Sony"), cantidad, y monto total.</li>
                            <li>Podés filtrar por fecha (por ejemplo, últimos 30 días) o estado (por ejemplo, "Pagado").</li>
                        </ol>
                        <p>Los datos provienen de la tabla <code>ordenes</code> tras sincronizar en <strong>Sincronización</strong>. Si no ves ventas, asegurá que tu cuenta esté vinculada en <strong>Cuentas</strong> y que haya órdenes recientes. Esta sección es clave para analizar tu desempeño comercial.</p>
                        <p><strong>Tidio trigger sugerido</strong>: "ver ventas mercadolibre" → Mostrar esta FAQ.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pregunta 2: Filtrar ventas -->
        <div class="accordion-item" itemscope="mainEntity" itemtype="https://schema.org/Question">
            <h3 class="accordion-header" id="ventas2">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVentas2" aria-expanded="false" aria-controls="collapseVentas2">
                    <i class="fas fa-filter me-2" aria-hidden="true"></i>
                    <span itemprop="name">¿Cómo filtro mis ventas por fecha o producto en MLDataTrends?</span>
                </button>
            </h3>
            <div id="collapseVentas2" class="accordion-collapse collapse" data-bs-parent="#ventasAccordion">
                <div class="accordion-body" itemscope="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div class="itemprop" itemprop="text">
                        <p>Filtrar ventas te permite analizar órdenes específicas por fecha o producto, usando el <code>SalesController</code>. Seguí estos pasos:</p>
                        <ol>
                            <li>Navegá a <strong>Ventas</strong> en el panel.</li>
                            <li>En el panel de filtros, seleccioná un rango de fechas (por ejemplo, desde "2025-06-01" hasta "2025-06-25").</li>
                            <li>Ingresá un título de producto (por ejemplo, "Zapatillas Nike") o SKU (por ejemplo, "ZAP123") en el campo de búsqueda.</li>
                            <li>Hacé clic en <strong>Aplicar filtros</strong> para ver las órdenes que coincidan.</li>
                        </ol>
                        <p>Por ejemplo, podés ver todas las ventas de zapatillas en junio de 2025. Los datos se obtienen de la tabla <code>ordenes</code>. Si no hay resultados, verificá que los datos estén sincronizados en <strong>Sincronización</strong> o ajustá los filtros. Esta funcionalidad es útil para informes mensuales o seguimiento de productos estrella.</p>
                        <p><strong>Tidio trigger sugerido</strong>: "filtrar ventas por fecha" → Mostrar esta FAQ.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pregunta 3: Problemas con ventas -->
        <div class="accordion-item" itemscope="mainEntity" itemtype="https://schema.org/Question">
            <h3 class="accordion-header" id="ventas3">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVentas3" aria-expanded="false" aria-controls="collapseVentas3">
                    <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                    <span itemprop="name">¿Por qué no veo mis ventas recientes en MLDataTrends?</span>
                </button>
            </h3>
            <div id="collapseVentas3" class="accordion-collapse collapse" data-bs-parent="#ventasAccordion">
                <div class="accordion-body" itemscope="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div class="itemprop" itemprop="text">
                        <p>Si no ves tus ventas recientes en <strong>Ventas</strong>, puede haber problemas con la sincronización o configuración. El <code>SalesController</code> depende de datos de la tabla <code>ordenes</code>. Revisá estos puntos:</p>
                        <ul>
                            <li><strong>Sincronización pendiente</strong>: Verificá en <strong>Sincronización</strong> si el proceso está completo. Si está en curso, esperá unos minutos.</li>
                            <li><strong>Cuenta no vinculada</strong>: Asegurá que tu cuenta de MercadoLibre esté activa en <strong>Cuentas</strong>. Si ves "Token expirado", revinculá.</li>
                            <li><strong>Filtros aplicados</strong>: Quitá filtros de fecha o producto haciendo clic en "Restablecer filtros".</li>
                            <li><strong>Retraso en API</strong>: La API de MercadoLibre puede demorar en reflejar ventas recientes. Intentá sincronizar nuevamente tras 10 minutos.</li>
                        </ul>
                        <p>Si el problema persiste, revisá las notificaciones en <strong>Sincronización</strong> para errores como "API rate limit exceeded". Contactá a support@mldatatrends.com con detalles del problema.</p>
                        <p><strong>Tidio trigger sugerido</strong>: "no veo ventas recientes" → Mostrar esta FAQ.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección: Promociones -->
    <h2 class="mt-5 mb-4" id="promocionesAccordion">Promociones</h2>
    <div class="accordion" id="promocionesAccordion">
        <!-- Pregunta 1: Ver promociones -->
        <div class="accordion-item" itemscope="mainEntity" itemtype="https://schema.org/Question">
            <h3 class="accordion-header" id="promociones1">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePromociones1" aria-expanded="false" aria-controls="collapsePromociones1">
                    <i class="fas fa-tags me-2" aria-hidden="true"></i>
                    <span itemprop="name">¿Cómo veo los productos en promoción en MLDataTrends?</span>
                </button>
            </h3>
            <div id="collapsePromociones1" class="accordion-collapse collapse" data-bs-parent="#promocionesAccordion">
                <div class="accordion-body" itemscope="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div class="itemprop" itemprop="text">
                        <p>La sección <strong>Promociones</strong> te muestra los productos con descuentos activos en MercadoLibre, usando el <code>PromotionsController</code>. Esto te ayuda a monitorear tus campañas promocionales. Seguí estos pasos:</p>
                        <ol>
                            <li>Iniciá sesión en <a href="https://mldatatrends.com/login">MLDataTrends.com/login</a>.</li>
                            <li>Hacé clic en <strong>Promociones</strong> en el panel de control.</li>
                            <li>Verás una lista de productos en promoción, con detalles como título (por ejemplo, "Smart TV 50''"), porcentaje de descuento (por ejemplo, "20%"), y precio promocional.</li>
                            <li>Podés filtrar por cuenta de MercadoLibre si tenés varias vinculadas.</li>
                        </ol>
                        <p>Los datos se extraen de la tabla <code>articulos</code> (campo <code>en_promocion</code>) tras sincronizar en <strong>Sincronización</strong>. Si no ves promociones, verificá que tengas descuentos activos en MercadoLibre y que los datos estén sincronizados. Esta sección es útil para evaluar el impacto de tus descuentos.</p>
                        <p><strong>Tidio trigger sugerido</strong>: "ver productos en promoción" → Mostrar esta FAQ.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pregunta 2: Filtrar promociones -->
        <div class="accordion-item" itemscope="mainEntity" itemtype="https://schema.org/Question">
            <h3 class="accordion-header" id="promociones2">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePromociones2" aria-expanded="false" aria-controls="collapsePromociones2">
                    <i class="fas fa-filter me-2" aria-hidden="true"></i>
                    <span itemprop="name">¿Cómo filtro productos en promoción por porcentaje de descuento?</span>
                </button>
            </h3>
            <div id="collapsePromociones2" class="accordion-collapse collapse" data-bs-parent="#promocionesAccordion">
                <div class="accordion-body" itemscope="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div class="itemprop" itemprop="text">
                        <p>Filtrar productos en promoción por porcentaje de descuento te permite enfocarte en las ofertas más atractivas. El <code>PromotionsController</code> usa el campo <code>descuento_porcentaje</code> de la tabla <code>articulos</code>. Seguí estos pasos:</p>
                        <ol>
                            <li>Navegá a <strong>Promociones</strong> en el panel.</li>
                            <li>En el panel de filtros, seleccioná un rango de descuento (por ejemplo, "Mayor a 15%").</li>
                            <li>Podés combinar con una búsqueda por título (por ejemplo, "Televisor").</li>
                            <li>Hacé clic en <strong>Aplicar filtros</strong> para ver los resultados.</li>
                        </ol>
                        <p>Por ejemplo, podés listar productos con descuentos mayores al 20%, como "Smart TV 50'' (25% off)". Asegurá que los datos estén sincronizados en <strong>Sincronización</strong>. Si no hay resultados, revisá que tengas promociones activas en MercadoLibre. Esta funcionalidad es ideal para optimizar campañas promocionales.</p>
                        <p><strong>Tidio trigger sugerido</strong>: "filtrar promociones descuento" → Mostrar esta FAQ.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pregunta 3: Problemas con promociones -->
        <div class="accordion-item" itemscope="mainEntity" itemtype="https://schema.org/Question">
            <h3 class="accordion-header" id="promociones3">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePromociones3" aria-expanded="false" aria-controls="collapsePromociones3">
                    <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                    <span itemprop="name">¿Por qué no veo mis promociones en MLDataTrends?</span>
                </button>
            </h3>
            <div id="collapsePromociones3" class="accordion-collapse collapse" data-bs-parent="#promocionesAccordion">
                <div class="accordion-body" itemscope="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div class="itemprop" itemprop="text">
                        <p>Si no ves tus promociones en <strong>Promociones</strong>, puede haber problemas con la sincronización o configuración. El <code>PromotionsController</code> depende de datos de la tabla <code>articulos</code>. Revisá estos puntos:</p>
                        <ul>
                            <li><strong>Sin promociones activas</strong>: Confirmá en MercadoLibre que tenés descuentos activos en tus publicaciones.</li>
                            <li><strong>Sincronización incompleta</strong>: Verificá en <strong>Sincronización</strong> si el proceso terminó. Si está en curso, esperá.</li>
                            <li><strong>Cuenta no vinculada</strong>: Asegurá que tu cuenta esté activa en <strong>Cuentas</strong>. Revinculá si es necesario.</li>
                            <li><strong>Filtros aplicados</strong>: Quitá filtros de descuento o búsqueda haciendo clic en "Restablecer filtros".</li>
                        </ul>
                        <p>Si el problema persiste, revisá las notificaciones en <strong>Sincronización</strong> para errores específicos. Intentá sincronizar nuevamente o contactá a support@mldatatrends.com con detalles de la promoción que no aparece.</p>
                        <p><strong>Tidio trigger sugerido</strong>: "no veo promociones mldata" → Mostrar esta FAQ.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección: Catálogo -->
    <h2 class="mt-5 mb-4" id="catalogoAccordion">Catálogo</h2>
    <div class="accordion" id="catalogoAccordion">
        <!-- Pregunta 1: Ver catálogo -->
        <div class="accordion-item" itemscope="mainEntity" itemtype="https://schema.org/Question">
            <h3 class="accordion-header" id="catalogo1">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCatalogo1" aria-expanded="false" aria-controls="collapseCatalogo1">
                    <i class="fas fa-book me-2" aria-hidden="true"></i>
                    <span itemprop="name">¿Cómo accedo al catálogo de mis productos en MLDataTrends?</span>
                </button>
            </h3>
            <div id="collapseCatalogo1" class="accordion-collapse collapse" data-bs-parent="#catalogoAccordion">
                <div class="accordion-body" itemscope="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div class="itemprop" itemprop="text">
                        <p>La sección <strong>Catálogo</strong> te muestra un listado de tus productos activos en MercadoLibre, con filtros avanzados. Usa el <code>CatalogoController</code> y <code>CatalogoService::getArticulosEnCatalogo</code>. Seguí estos pasos:</p>
                        <ol>
                            <li>Iniciá sesión en <a href="https://mldatatrends.com/login">MLDataTrends.com/login</a>.</li>
                            <li>Hacé clic en <strong>Catálogo</strong> en la barra lateral.</li>
                            <li>Verás una tabla con artículos, incluyendo ID (por ejemplo, "MLA123456789"), título (por ejemplo, "Smartphone Samsung"), precio, stock, tipo de publicación, y cuenta ML (por ejemplo, "TiendaEjemplo").</li>
                            <li>Podés paginar (10 artículos por página) o ajustar el límite de resultados.</li>
                        </ol>
                        <p>Los datos provienen de la tabla <code>articulos</code> (campo <code>en_catalogo=1</code>) tras sincronizar en <strong>Sincronización</strong>. Si el catálogo está vacío, verificá que tu cuenta esté vinculada en <strong>Cuentas</strong> y sincronizada. Esta sección es ideal para gestionar tu inventario completo.</p>
                        <p><strong>Tidio trigger sugerido</strong>: "ver catálogo productos" → Mostrar esta FAQ.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pregunta 2: Filtrar catálogo -->
        <div class="accordion-item" itemscope="mainEntity" itemtype="https://schema.org/Question">
            <h3 class="accordion-header" id="catalogo2">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCatalogo2" aria-expanded="false" aria-controls="collapseCatalogo2">
                    <i class="fas fa-filter me-2" aria-hidden="true"></i>
                    <span itemprop="name">¿Cómo filtro productos en el catálogo por cuenta o búsqueda?</span>
                </button>
            </h3>
            <div id="collapseCatalogo2" class="accordion-collapse collapse" data-bs-parent="#catalogoAccordion">
                <div class="accordion-body" itemscope="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div class="itemprop" itemprop="text">
                        <p>Filtrar productos en <strong>Catálogo</strong> te permite encontrar artículos específicos por cuenta de MercadoLibre o palabra clave. El <code>CatalogoService::getArticulosEnCatalogo</code> soporta filtros dinámicos. Seguí estos pasos:</p>
                        <ol>
                            <li>Navegá a <strong>Catálogo</strong> en el panel.</li>
                            <li>Seleccioná una cuenta ML en el menú desplegable (por ejemplo, "TiendaEjemplo").</li>
                            <li>Ingresá una palabra clave en el campo de búsqueda (por ejemplo, "Zapatillas" o SKU "ZAP123").</li>
                            <li>Hacé clic en <strong>Aplicar filtros</strong> para ver los resultados.</li>
                        </ol>
                        <p>Por ejemplo, podés listar solo productos de "TiendaEjemplo" que contengan "Zapatillas" en el título. Los datos se obtienen de la tabla <code>articulos</code>. Si no hay resultados, quitá los filtros o verificá la sincronización en <strong>Sincronización</strong>. Esta funcionalidad es útil para vendedores con múltiples cuentas o catálogos grandes.</p>
                        <p><strong>Tidio trigger sugerido</strong>: "filtrar catálogo cuenta" → Mostrar esta FAQ.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pregunta 3: Analizar competencia -->
        <div class="accordion-item" itemscope="mainEntity" itemtype="https://schema.org/Question">
            <h3 class="accordion-header" id="catalogo3">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCatalogo3" aria-expanded="false" aria-controls="collapseCatalogo3">
                    <i class="fas fa-chart-line me-2" aria-hidden="true"></i>
                    <span itemprop="name">¿Cómo analizo la competencia de un producto en el catálogo?</span>
                </button>
            </h3>
            <div id="collapseCatalogo3" class="accordion-collapse collapse" data-bs-parent="#catalogoAccordion">
                <div class="accordion-body" itemscope="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div class="itemprop" itemprop="text">
                        <p>MLDataTrends te permite analizar la competencia de un producto específico usando la API de MercadoLibre (`price_to_win`), implementada en <code>CatalogoService::getCompetenciaArticulo</code>. Seguí estos pasos:</p>
                        <ol>
                            <li>En <strong>Catálogo</strong>, buscá un producto por título o ID (por ejemplo, "MLA987654321").</li>
                            <li>Hacé clic en el producto para ver detalles.</li>
                            <li>Seleccioná <strong>Ver competencia</strong> para cargar el análisis.</li>
                            <li>Verás datos como precios de competidores, stock, y estrategias de otros vendedores para el mismo producto.</li>
                        </ol>
                        <p>Esta funcionalidad usa el endpoint `https://api.mercadolibre.com/items/{id}/price_to_win`. Asegurá que tu cuenta esté vinculada en <strong>Cuentas</strong> y que el producto esté sincronizado. Si no carga la competencia, revisá las notificaciones en <strong>Sincronización</strong> para errores de API. Es ideal para ajustar precios y mejorar tu posicionamiento.</p>
                        <p><strong>Tidio trigger sugerido</strong>: "analizar competencia catálogo" → Mostrar esta FAQ.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección: Estadísticas -->
    <h2 class="mt-5 mb-4" id="estadisticasAccordion">Estadísticas</h2>
    <div class="accordion" id="estadisticasAccordion">
        <!-- Pregunta 1: Ver estadísticas -->
        <div class="accordion-item" itemscope="mainEntity" itemtype="https://schema.org/Question">
            <h3 class="accordion-header" id="estadisticas1">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstadisticas1" aria-expanded="false" aria-controls="collapseEstadisticas1">
                    <i class="fas fa-chart-bar me-2" aria-hidden="true"></i>
                    <span itemprop="name">¿Cómo accedo a las estadísticas de mi cuenta en MLDataTrends?</span>
                </button>
            </h3>
            <div id="collapseEstadisticas1" class="accordion-collapse collapse" data-bs-parent="#estadisticasAccordion">
                <div class="accordion-body" itemscope="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div class="itemprop" itemprop="text">
                        <p>La sección <strong>Estadísticas</strong> te ofrece métricas detalladas sobre tu desempeño en MercadoLibre, usando el <code>EstadisticasController</code> y <code>EstadisticasService</code>. Seguí estos pasos:</p>
                        <ol>
                            <li>Iniciá sesión en <a href="https://mldatatrends.com/login">MLDataTrends.com/login</a>.</li>
                            <li>Hacé clic en <strong>Estadísticas</strong> en la barra lateral.</li>
                            <li>Verás métricas como stock total (por ejemplo, 500 unidades), productos en promoción (por ejemplo, "TV 50'' con 20% off"), ventas por período (por ejemplo, $100,000 en junio), y tasa de conversión.</li>
                            <li>Podés ajustar el rango de fechas (por ejemplo, últimos 30 días).</li>
                        </ol>
                        <p>Los datos provienen de las tablas <code>articulos</code> y <code>ordenes</code> tras sincronizar en <strong>Sincronización</strong>. Si no ves estadísticas, asegurá que tu cuenta esté vinculada en <strong>Cuentas</strong>. Esta sección es clave para tomar decisiones estratégicas basadas en datos.</p>
                        <p><strong>Tidio trigger sugerido</strong>: "ver estadísticas cuenta" → Mostrar esta FAQ.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pregunta 2: Stock crítico -->
        <div class="accordion-item" itemscope="mainEntity" itemtype="https://schema.org/Question">
            <h3 class="accordion-header" id="estadisticas2">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstadisticas2" aria-expanded="false" aria-controls="collapseEstadisticas2">
                    <i class="fas fa-exclamation-circle me-2" aria-hidden="true"></i>
                    <span itemprop="name">¿Cómo identifico productos con stock crítico en MLDataTrends?</span>
                </button>
            </h3>
            <div id="collapseEstadisticas2" class="accordion-collapse collapse" data-bs-parent="#estadisticasAccordion">
                <div class="accordion-body" itemscope="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div class="itemprop" itemprop="text">
                        <p>Identificar productos con stock crítico te ayuda a evitar perder ventas. El <code>EstadisticasService::getStockCritico</code> detecta artículos con menos de 5 unidades en fulfillment o depósito. Seguí estos pasos:</p>
                        <ol>
                            <li>Navegá a <strong>Estadísticas</strong> en el panel.</li>
                            <li>Buscá la sección <strong>Stock Crítico</strong> en el dashboard.</li>
                            <li>Verás una lista de productos con stock bajo, como "Auriculares Sony" con 3 unidades en fulfillment.</li>
                            <li>Hacé clic en un producto para ver detalles y reabastecer si es necesario.</li>
                        </ol>
                        <p>Los datos se obtienen de la tabla <code>articulos</code>. Si no aparecen productos, puede que no tengas stock crítico o que los datos no estén sincronizados (revisá <strong>Sincronización</strong>). Esta funcionalidad es esencial para gestionar inventarios y mantener tus publicaciones activas.</p>
                        <p><strong>Tidio trigger sugerido</strong>: "stock crítico productos" → Mostrar esta FAQ.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pregunta 3: Tasa de conversión -->
        <div class="accordion-item" itemscope="mainEntity" itemtype="https://schema.org/Question">
            <h3 class="accordion-header" id="estadisticas3">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstadisticas3" aria-expanded="false" aria-controls="collapseEstadisticas3">
                    <i class="fas fa-percentage me-2" aria-hidden="true"></i>
                    <span itemprop="name">¿Cómo veo la tasa de conversión de mis productos en MLDataTrends?</span>
                </button>
            </h3>
            <div id="collapseEstadisticas3" class="accordion-collapse collapse" data-bs-parent="#estadisticasAccordion">
                <div class="accordion-body" itemscope="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div class="itemprop" itemprop="text">
                        <p>La tasa de conversión mide cuántas visitas se convierten en ventas, calculada por <code>EstadisticasService::getTasaConversionPorProducto</code>. Seguí estos pasos:</p>
                        <ol>
                            <li>En <strong>Estadísticas</strong>, buscá la sección <strong>Tasa de Conversión</strong>.</li>
                            <li>Seleccioná un rango de fechas (por ejemplo, "2025-06-01 a 2025-06-25").</li>
                            <li>Verás una lista de productos con su tasa de conversión (por ejemplo, "Zapatillas Nike: 5%").</li>
                            <li>Analizá productos con tasas bajas para mejorar descripciones o precios.</li>
                        </ol>
                        <p>La tasa se calcula dividiendo ventas entre visitas (obtenidas vía API de MercadoLibre). Asegurá que los datos estén sincronizados en <strong>Sincronización</strong>. Si no ves tasas, verificá que tengas ventas recientes o que la API esté activa. Esta métrica es clave para optimizar tu estrategia de ventas.</p>
                        <p><strong>Tidio trigger sugerido</strong>: "tasa conversión productos" → Mostrar esta FAQ.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección: Sincronización -->
    <h2 class="mt-5 mb-4" id="sincronizacionAccordion">Sincronización</h2>
    <div class="accordion" id="sincronizacionAccordion">
        <!-- Pregunta 1: Iniciar sincronización -->
        <div class="accordion-item" itemscope="mainEntity" itemtype="https://schema.org/Question">
            <h3 class="accordion-header" id="sincronizacion1">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSincronizacion1" aria-expanded="false" aria-controls="collapseSincronizacion1">
                    <i class="fas fa-sync me-2" aria-hidden="true"></i>
                    <span itemprop="name">¿Cómo inicio la sincronización de datos con MercadoLibre en MLDataTrends?</span>
                </button>
            </h3>
            <div id="collapseSincronizacion1" class="accordion-collapse collapse" data-bs-parent="#sincronizacionAccordion">
                <div class="accordion-body" itemscope="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div class="itemprop" itemprop="text">
                        <p>Sincronizar datos con MercadoLibre actualiza tus publicaciones, ventas, y estadísticas en MLDataTrends, usando el <code>ConsultaMercadoLibreService</code>. Seguí estos pasos:</p>
                        <ol>
                            <li>Iniciá sesión en <a href="https://mldatatrends.com/login">MLDataTrends.com/login</a>.</li>
                            <li>Navegá a <strong>Sincronización</strong> en el panel.</li>
                            <li>Seleccioná la cuenta de MercadoLibre vinculada (por ejemplo, "TiendaEjemplo").</li>
                            <li>Hacé clic en <strong>Iniciar sincronización</strong> y esperá a que termine (puede tomar minutos si tenés muchas publicaciones).</li>
                        </ol>
                        <p>El proceso descarga datos de publicaciones, órdenes, y promociones a las tablas <code>articulos</code> y <code>ordenes</code>. Verificá el estado en la sección <strong>Sincronización</strong>. Si no inicia, asegurá que tu cuenta esté vinculada en <strong>Cuentas</strong> y que el token esté activo. La sincronización es esencial para todas las funcionalidades de MLDataTrends.</p>
                        <p><strong>Tidio trigger sugerido</strong>: "iniciar sincronización mercadolibre" → Mostrar esta FAQ.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pregunta 2: Ver estado -->
        <div class="accordion-item" itemscope="mainEntity" itemtype="https://schema.org/Question">
            <h3 class="accordion-header" id="sincronizacion2">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSincronizacion2" aria-expanded="false" aria-controls="collapseSincronizacion2">
                    <i class="fas fa-info-circle me-2" aria-hidden="true"></i>
                    <span itemprop="name">¿Cómo verifico el estado de la sincronización en MLDataTrends?</span>
                </button>
            </h3>
            <div id="collapseSincronizacion2" class="accordion-collapse collapse" data-bs-parent="#sincronizacionAccordion">
                <div class="accordion-body" itemscope="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div class="itemprop" itemprop="text">
                        <p>Verificar el estado de la sincronización te permite saber si tus datos están actualizados. El <code>ConsultaMercadoLibreService</code> registra el progreso. Seguí estos pasos:</p>
                        <ol>
                            <li>Navegá a <strong>Sincronización</strong> en el panel de control.</li>
                            <li>Buscá el resumen de la última sincronización, que muestra la fecha (por ejemplo, "2025-06-25 10:30"), estado ("Completada" o "En curso"), y cantidad de artículos sincronizados.</li>
                            <li>Si hay errores, verás mensajes como "API rate limit exceeded".</li>
                        </ol>
                        <p>Si la sincronización está en curso, esperá a que termine antes de usar otras secciones como <strong>Ventas</strong> o <strong>Catálogo</strong>. Si ves errores, intentá sincronizar nuevamente tras 10 minutos o revinculá tu cuenta en <strong>Cuentas</strong>. Esta funcionalidad asegura que tus datos sean precisos y actuales.</p>
                        <p><strong>Tidio trigger sugerido</strong>: "estado sincronización mldata" → Mostrar esta FAQ.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pregunta 3: Problemas con sincronización -->
        <div class="accordion-item" itemscope="mainEntity" itemtype="https://schema.org/Question">
            <h3 class="accordion-header" id="sincronizacion3">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSincronizacion3" aria-expanded="false" aria-controls="collapseSincronizacion3">
                    <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                    <span itemprop="name">¿Por qué falla la sincronización de datos en MLDataTrends?</span>
                </button>
            </h3>
            <div id="collapseSincronizacion3" class="accordion-collapse collapse" data-bs-parent="#sincronizacionAccordion">
                <div class="accordion-body" itemscope="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div class="itemprop" itemprop="text">
                        <p>Si la sincronización falla, puede haber problemas con la API de MercadoLibre o la configuración. El <code>ConsultaMercadoLibreService</code> registra errores. Revisá estos puntos:</p>
                        <ul>
                            <li><strong>Token expirado</strong>: Revinculá tu cuenta en <strong>Cuentas</strong> si ves "Token expirado".</li>
                            <li><strong>Límite de API</strong>: La API de MercadoLibre tiene límites de consultas. Esperá 5-10 minutos y reintentá.</li>
                            <li><strong>Cuenta sin datos</strong>: Asegurá que tu cuenta de MercadoLibre tenga publicaciones o ventas activas.</li>
                            <li><strong>Errores de servidor</strong>: Revisá las notificaciones en <strong>Sincronización</strong> para mensajes específicos.</li>
                        </ul>
                        <p>Si el problema persiste, contactá a support@mldatatrends.com con el mensaje de error. Intentá sincronizar en horarios de menor tráfico (por ejemplo, madrugada). Una sincronización exitosa es crucial para que todas las secciones muestren datos actualizados.</p>
                        <p><strong>Tidio trigger sugerido</strong>: "sincronización falla mldata" → Mostrar esta FAQ.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

            <div class="text-center mt-5">
                <p><a href="{{ url('/') }}" class="btn btn-primary">Volver al home</a></p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4" role="contentinfo">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Propósito</h5>
                    <p>Potenciamos a los vendedores de MercadoLibre con herramientas avanzadas para gestionar cuentas, publicaciones y más.</p>
                </div>
                <div class="col-md-4">
                    <h5>Enlaces Rápidos</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('login') }}">Iniciar Sesión</a></li>
                        <li><a href="{{ route('register') }}">Registrarse</a></li>
                        <li><a href="{{ route('faq.index') }}">Preguntas Frecuentes</a></li>
                        <li><a href="{{ url('/terms') }}" target="_blank">Términos y Condiciones</a></li>
                        <li><a href="{{ url('/privacy') }}" target="_blank">Política de Privacidad</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Correo</h5>
                    <p><i class="fas fa-envelope me-2" aria-hidden="true"></i>support@mldatatrends.com</p>
                </div>
            </div>
            <hr class="bg-light">
            <p class="text-center mb-0">© 2025 MLDataTrends. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="//code.tidio.co/jo26sf9h8f1m3xswvswb1eh3i7r1z3k9.js" async></script>
</body>
</html>
