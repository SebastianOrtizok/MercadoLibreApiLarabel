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
