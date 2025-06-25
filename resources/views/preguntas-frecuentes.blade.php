<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Resuelve tus dudas sobre cómo usar MLDataTrends para gestionar competidores, sincronizar cuentas, ver publicaciones y optimizar ventas en MercadoLibre. ¡Consulta nuestras Preguntas Frecuentes!">
    <meta name="keywords" content="preguntas frecuentes mldatatrends, vincular cuenta mercadolibre, sincronizar artículos mercadolibre, ver publicaciones mercadolibre, buscar publicaciones por id, cómo seguir competidores mercadolibre, análisis de precios mercadolibre, gestión de competidores ml, tutorial mldatatrends">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/preguntas-frecuentes') }}">
    <title>Preguntas Frecuentes - MLDataTrends: Gestión de Ventas en MercadoLibre</title>

    <!-- Open Graph -->
    <meta property="og:title" content="Preguntas Frecuentes - MLDataTrends">
    <meta property="og:description" content="Aprendé cómo vincular tu cuenta, sincronizar artículos, ver publicaciones, seguir competidores y optimizar tus ventas en MercadoLibre con nuestras Preguntas Frecuentes.">
    <meta property="og:url" content="{{ url('/preguntas-frecuentes') }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('images/faq.webp') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Preguntas Frecuentes - MLDataTrends">
    <meta name="twitter:description" content="Aprendé cómo vincular tu cuenta, sincronizar artículos, ver publicaciones, seguir competidores y optimizar tus ventas en MercadoLibre con nuestras Preguntas Frecuentes.">
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
            <p class="lead text-center mb-5">¿No sabés cómo empezar a usar MLDataTrends para gestionar tus ventas en MercadoLibre? Acá te explicamos todo paso a paso, desde vincular tu cuenta hasta seguir competidores y optimizar tu stock.</p>
            <div itemscope itemtype="https://schema.org/FAQPage">
                <!-- Sección: Cuentas -->
                <h2 class="mb-4">Cuentas</h2>
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
                                    <p>Para vincular tu cuenta de MercadoLibre a MLDataTrends, seguí estos pasos:</p>
                                    <ol>
                                        <li>Iniciá sesión en tu cuenta de MLDataTrends.</li>
                                        <li>Dirigite a la sección <strong>Cuentas</strong> en el panel de control.</li>
                                        <li>Hacé clic en <strong>Vincular cuenta de MercadoLibre</strong>.</li>
                                        <li>Serás redirigido a MercadoLibre para autorizar la conexión.</li>
                                        <li>Iniciá sesión en MercadoLibre y aceptá los permisos solicitados.</li>
                                        <li>Una vez autorizado, volverás a MLDataTrends y tu cuenta estará vinculada.</li>
                                    </ol>
                                    <p>Si tenés problemas, asegurate de que tu cuenta de MercadoLibre esté activa y verificá que el navegador no bloquee las redirecciones.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 2: Información de la cuenta -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="cuentas2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCuentas2" aria-expanded="false" aria-controls="collapseCuentas2">
                                <i class="fas fa-info-circle me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Qué información puedo ver de mi cuenta de MercadoLibre?</span>
                            </button>
                        </h3>
                        <div id="collapseCuentas2" class="accordion-collapse collapse" data-bs-parent="#cuentasAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>En la sección <strong>Cuentas</strong> de MLDataTrends, podés ver:</p>
                                    <ul>
                                        <li>El <em>Seller ID</em> (identificador único de tu cuenta en MercadoLibre).</li>
                                        <li>Detalles de tu perfil, como el nombre de la cuenta y el estado de vinculación.</li>
                                        <li>Información sobre las publicaciones asociadas a tu cuenta.</li>
                                        <li>Estado del token de acceso (si está activo o necesita actualización).</li>
                                    </ul>
                                    <p>Para acceder, iniciá sesión y navegá a <strong>Cuentas</strong> en el panel de control.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 3: Sincronizar artículos -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="cuentas3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCuentas3" aria-expanded="false" aria-controls="collapseCuentas3">
                                <i class="fas fa-sync-alt me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Cómo sincronizo mis artículos de MercadoLibre en MLDataTrends?</span>
                            </button>
                        </h3>
                        <div id="collapseCuentas3" class="accordion-collapse collapse" data-bs-parent="#cuentasAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>Para sincronizar tus artículos de MercadoLibre en MLDataTrends:</p>
                                    <ol>
                                        <li>Iniciá sesión y dirigite a la sección <strong>Sincronización</strong>.</li>
                                        <li>Seleccioná la cuenta de MercadoLibre que querés sincronizar.</li>
                                        <li>Hacé clic en <strong>Iniciar sincronización</strong>.</li>
                                        <li>La herramienta descargará tus publicaciones, incluyendo título, precio, stock, estado, y más.</li>
                                        <li>Esperá a que el proceso termine (puede tomar unos minutos, dependiendo de la cantidad de publicaciones).</li>
                                    </ol>
                                    <p>Una vez sincronizados, los artículos aparecerán en la sección <strong>Publicaciones</strong> o <strong>Listado completo</strong>.</p>
</li>
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
                                    <p>Si no ves tus publicaciones después de sincronizar, verificá lo siguiente:</p>
                                    <ul>
                                        <li><strong>Token expirado</strong>: Asegurate de que tu cuenta de MercadoLibre esté vinculada correctamente. Podés revincularla en <strong>Cuentas</strong>.</li>
                                        <li><strong>Sin publicaciones</strong>: Confirmá que tu cuenta de MercadoLibre tenga publicaciones activas.</li>
                                        <li><strong>Errores de sincronización</strong>: Revisá las notificaciones en la sección <strong>Sincronización</strong> para ver si hubo errores.</li>
                                        <li><strong>Filtros aplicados</strong>: En la sección <strong>Publicaciones</strong>, asegurate de no tener filtros que oculten las publicaciones.</li>
                                    </ul>
                                    <p>Si el problema persiste, contactanos en <a href="{{ url('/contacto') }}">soporte</a>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Publicaciones -->
                <h2 class="mt-5 mb-4">Publicaciones</h2>
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
                                    <p>Para ver tus publicaciones de MercadoLibre en MLDataTrends:</p>
                                    <ol>
                                        <li>Iniciá sesión en tu cuenta de MLDataTrends.</li>
                                        <li>Dirigite a la sección <strong>Publicaciones</strong> en el panel de control.</li>
                                        <li>La herramienta mostrará una lista de todas tus publicaciones activas, con detalles como título, precio, stock, y estado.</li>
                                    </ol>
                                    <p>Asegurate de haber vinculado y sincronizado tu cuenta de MercadoLibre en la sección <strong>Cuentas</strong> y <strong>Sincronización</strong>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 2: Buscar por ID -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="publicaciones2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePublicaciones2" aria-expanded="false" aria-controls="collapsePublicaciones2">
                                <i class="fas fa-search me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Cómo busco una publicación específica por ID?</span>
                            </button>
                        </h3>
                        <div id="collapsePublicaciones2" class="accordion-collapse collapse" data-bs-parent="#publicacionesAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>Para buscar una publicación específica por su ID (MLA ID):</p>
                                    <ol>
                                        <li>Dirigite a la sección <strong>Publicaciones</strong>.</li>
                                        <li>Ingresá el ID de la publicación (por ejemplo, MLA123456789) en el campo de búsqueda.</li>
                                        <li>Hacé clic en <strong>Buscar</strong>.</li>
                                        <li>Si la publicación existe y está vinculada a tu cuenta, aparecerá en los resultados.</li>
                                    </ol>
                                    <p>Si no encontrás la publicación, verificá que el ID sea correcto y que la cuenta esté sincronizada.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 3: Filtrar por estado -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="publicaciones3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePublicaciones3" aria-expanded="false" aria-controls="collapsePublicaciones3">
                                <i class="fas fa-filter me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Cómo filtro mis publicaciones por estado (activas, pausadas, etc.)?</span>
                            </button>
                        </h3>
                        <div id="collapsePublicaciones3" class="accordion-collapse collapse" data-bs-parent="#publicacionesAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>Para filtrar tus publicaciones por estado:</p>
                                    <ol>
                                        <li>En la sección <strong>Publicaciones</strong>, buscá el menú de filtros.</li>
                                        <li>Seleccioná el estado deseado (por ejemplo, "Activas", "Pausadas" o "Todas").</li>
                                        <li>Podés combinar el filtro con una búsqueda por título o palabra clave.</li>
                                        <li>Hacé clic en <strong>Aplicar</strong> para ver los resultados.</li>
                                    </ol>
                                    <p>Esto te permite enfocarte en las publicaciones que necesitás analizar o gestionar.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 4: No veo publicaciones -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="publicaciones4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePublicaciones4" aria-expanded="false" aria-controls="collapsePublicaciones4">
                                <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Por qué no veo todas mis publicaciones?</span>
                            </button>
                        </h3>
                        <div id="collapsePublicaciones4" class="accordion-collapse collapse" data-bs-parent="#publicacionesAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>Si no ves todas tus publicaciones en la sección <strong>Publicaciones</strong>, revisá lo siguiente:</p>
                                    <ul>
                                        <li><strong>Cuenta no vinculada</strong>: Asegurate de que tu cuenta de MercadoLibre esté vinculada en <strong>Cuentas</strong>.</li>
                                        <li><strong>Sincronización incompleta</strong>: Verificá en <strong>Sincronización</strong> que los artículos se hayan descargado correctamente.</li>
                                        <li><strong>Filtros aplicados</strong>: Quitá cualquier filtro de estado o búsqueda para ver todas las publicaciones.</li>
                                        <li><strong>Token expirado</strong>: Revinculá tu cuenta si el token de acceso caducó.</li>
                                    </ul>
                                    <p>Si el problema continúa, contactanos en <a href="{{ url('/contacto') }}">soporte</a>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Competencia -->
                <h2 class="mt-5 mb-4">Competencia</h2>
                <div class="accordion" id="competenciaAccordion">
                    <!-- Pregunta 1 -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="faq1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false" aria-controls="collapse1">
                                <i class="fas fa-search me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Cómo seguir publicaciones de competidores en MercadoLibre?</span>
                            </button>
                        </h3>
                        <div id="collapse1" class="accordion-collapse collapse" data-bs-parent="#competenciaAccordion">
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
                        <h3 class="accordion-header" id="faq2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                <i class="fas fa-user me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Qué es el Seller ID y cómo lo encuentro?</span>
                            </button>
                        </h3>
                        <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#competenciaAccordion">
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
                        <h3 class="accordion-header" id="faq3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                <i class="fas fa-sync me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Cómo actualizo los datos de las publicaciones de un competidor?</span>
                            </button>
                        </h3>
                        <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#competenciaAccordion">
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
                        <h3 class="accordion-header" id="faq4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                <i class="fas fa-file-excel me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Cómo exporto las publicaciones de competidores a Excel?</span>
                            </button>
                        </h3>
                        <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#competenciaAccordion">
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
                        <h3 class="accordion-header" id="faq5">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                <i class="fas fa-filter me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Cómo filtro publicaciones en la sección de competidores?</span>
                            </button>
                        </h3>
                        <div id="collapse5" class="accordion-collapse collapse" data-bs-parent="#competenciaAccordion">
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
               <a href="{{ url('/') }}" class="btn btn-primary">Home</a>
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
