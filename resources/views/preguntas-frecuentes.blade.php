<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Resuelve tus dudas sobre cómo usar MLDataTrends para gestionar cuentas, publicaciones, SKUs, ventas, promociones, catálogo, estadísticas y sincronización en MercadoLibre. ¡Consulta nuestras Preguntas Frecuentes!">
    <meta name="keywords" content="preguntas frecuentes mldatatrends, vincular cuenta mercadolibre, sincronizar artículos mercadolibre, ver publicaciones mercadolibre, gestionar skus mercadolibre, reporte ventas mercadolibre, gestionar promociones mercadolibre, catálogo mercadolibre, estadísticas ventas mercadolibre, sincronización mldata, tutorial mldatatrends">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/preguntas-frecuentes') }}">
    <title>Preguntas Frecuentes - MLDataTrends: Gestión de Ventas en MercadoLibre</title>

    <!-- Open Graph -->
    <meta property="og:title" content="Preguntas Frecuentes - MLDataTrends">
    <meta property="og:description" content="Aprendé cómo vincular tu cuenta, sincronizar artículos, gestionar publicaciones, SKUs, ventas, promociones, catálogo, estadísticas y más en MercadoLibre con nuestras Preguntas Frecuentes.">
    <meta property="og:url" content="{{ url('/preguntas-frecuentes') }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('images/faq.webp') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Preguntas Frecuentes - MLDataTrends">
    <meta name="twitter:description" content="Aprendé cómo vincular tu cuenta, sincronizar artículos, gestionar publicaciones, SKUs, ventas, promociones, catálogo, estadísticas y más en MercadoLibre con nuestras Preguntas Frecuentes.">
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
            <p class="lead text-center mb-5">¿No sabés cómo empezar a usar MLDataTrends para gestionar tus ventas en MercadoLibre? Acá te explicamos todo paso a paso, desde vincular tu cuenta hasta analizar estadísticas y optimizar tu catálogo.</p>
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
                                    <p>Para vincular tu cuenta de MercadoLibre a MLDataTrends:</p>
                                    <ol>
                                        <li>Iniciá sesión en tu cuenta de MLDataTrends.</li>
                                        <li>Dirigite a la sección <strong>Cuentas</strong> en el panel de control.</li>
                                        <li>Hacé clic en <strong>Vincular cuenta de MercadoLibre</strong>.</li>
                                        <li>Serás redirigido a MercadoLibre para autorizar la conexión.</li>
                                        <li>Iniciá sesión en MercadoLibre y aceptá los permisos solicitados.</li>
                                        <li>Una vez autorizado, volverás a MLDataTrends y tu cuenta estará vinculada.</li>
                                    </ol>
                                    <p>Si tenés problemas, asegurá que tu cuenta de MercadoLibre esté activa y que el navegador permita redirecciones.</p>
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
                                    <p>En la sección <strong>Cuentas</strong>, podés ver:</p>
                                    <ul>
                                        <li><strong>Seller ID</strong>: Identificador único de tu cuenta en MercadoLibre.</li>
                                        <li><strong>Nombre de la cuenta</strong>: Nombre asociado a tu perfil.</li>
                                        <li><strong>Estado de vinculación</strong>: Si la cuenta está activa o necesita revinculación.</li>
                                        <li><strong>Token de acceso</strong>: Estado del token (activo o expirado).</li>
                                    </ul>
                                    <p>Accedé a esta información desde el panel de control en <strong>Cuentas</strong>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 3: Sincronizar artículos -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="cuentas3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCuentas3" aria-expanded="false" aria-controls="collapseCuentas3">
                                <i class="fas fa-sync-alt me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Cómo sincronizo mis artículos de MercadoLibre?</span>
                            </button>
                        </h3>
                        <div id="collapseCuentas3" class="accordion-collapse collapse" data-bs-parent="#cuentasAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>Para sincronizar tus artículos:</p>
                                    <ol>
                                        <li>Iniciá sesión y navegá a <strong>Sincronización</strong>.</li>
                                        <li>Seleccioná la cuenta de MercadoLibre vinculada.</li>
                                        <li>Hacé clic en <strong>Iniciar sincronización</strong>.</li>
                                        <li>Esperá a que se descarguen tus publicaciones (título, precio, stock, estado, etc.).</li>
                                    </ol>
                                    <p>Los artículos sincronizados aparecerán en <strong>Publicaciones</strong> o <strong>Listado completo</strong>.</p>
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
                                    <p>Si no ves tus publicaciones, verificá:</p>
                                    <ul>
                                        <li><strong>Token expirado</strong>: Revinculá tu cuenta en <strong>Cuentas</strong>.</li>
                                        <li><strong>Sin publicaciones</strong>: Asegurá que tu cuenta de MercadoLibre tenga publicaciones activas.</li>
                                        <li><strong>Errores</strong>: Revisá las notificaciones en <strong>Sincronización</strong>.</li>
                                        <li><strong>Filtros</strong>: Quitá filtros en <strong>Publicaciones</strong> o <strong>Listado completo</strong>.</li>
                                    </ul>
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
                                <span itemprop="name">¿Cómo veo mis publicaciones de MercadoLibre?</span>
                            </button>
                        </h3>
                        <div id="collapsePublicaciones1" class="accordion-collapse collapse" data-bs-parent="#publicacionesAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>Para ver tus publicaciones:</p>
                                    <ol>
                                        <li>Iniciá sesión en MLDataTrends.</li>
                                        <li>Navegá a <strong>Publicaciones</strong> en el panel de control.</li>
                                        <li>Verás una lista de tus publicaciones activas con título, precio, stock y estado.</li>
                                    </ol>
                                    <p>Asegurá que tu cuenta esté vinculada y sincronizada en <strong>Cuentas</strong> y <strong>Sincronización</strong>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 2: Buscar por ID -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="publicaciones2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePublicaciones2" aria-expanded="false" aria-controls="collapsePublicaciones2">
                                <i class="fas fa-search me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Cómo busco una publicación por ID?</span>
                            </button>
                        </h3>
                        <div id="collapsePublicaciones2" class="accordion-collapse collapse" data-bs-parent="#publicacionesAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div itemprop="text">
                                    <p>Para buscar por ID (MLA ID):</p>
                                    <ol>
                                        <li>En <strong>Publicaciones</strong>, ingresá el ID (por ejemplo, MLA123456789) en el campo de búsqueda.</li>
                                        <li>Hacé clic en <strong>Buscar</strong>.</li>
                                        <li>La publicación aparecerá si está vinculada a tu cuenta.</li>
                                    </ol>
                                    <p>Verificá que el ID sea correcto y que la cuenta esté sincronizada.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 3: Filtrar por estado -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="publicaciones3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePublicaciones3" aria-expanded="false" aria-controls="collapsePublicaciones3">
                                <i class="fas fa-filter me-2" aria-hidden="true"></i>
                                <span itemprop="name">¿Cómo filtro mis publicaciones por estado?</span>
                            </button>
                        </h3>
                        <div id="collapsePublicaciones3" class="accordion-collapse collapse" data-bs-parent="#publicacionesAccordion">
                            <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Para filtrar por estado:</p>
                                    <ol>
                                        <li>En <strong>Publicaciones</strong>, buscá el menú de filtros.</li>
                                        <li>Seleccioná un estado ("Activas", "Pausadas", etc.).</li>
                                        <li>Podés combinar con una búsqueda por título.</li>
                                        <li>Hacé clic en <strong>Aplicar</strong>.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 4: No veo publicaciones -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="publicaciones4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePublicaciones4" aria-expanded="false" aria-controls="publicaciones4">
                                <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                                <span>¿Por qué no veo todas mis publicaciones?</span>
                            </button>
                        </h3>
                        <div id="collapsePublicaciones4" class="accordion-collapse collapse" data-bs-parent="#publicacionesAccordion">
                            <div class="accordion-body" itemscope="acceptedAnswer">
                                <div>
                                    <p>Si no vesás todas tus publicaciones, revisá:</p>
                                    <ul>
                                        <li><strong>Cuenta no vinculada</strong>: Vinculá tu cuenta en <strong>Cuentas</strong>.</li>
                                        <li><strong>Sincronización incompleta</strong>: Verificá en <strong>Sincronización</strong>.</li>
                                        <li><strong>Filtros aplicados</strong>: Quitá-los filtros de estado o búsqueda.</li>
                                        <li><strong>Token expirado</strong>: Revinculá tu cuenta.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Listado completo -->
                <h2 class="mt-5 mb-4">Listado completo</h2>
                <div class="accordion" id="listadoCompletoAccordion">
                    <!-- Pregunta 1: Acceder al listado -->
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="listadoCompleto1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseListadoCompleto1" aria-expanded="false" aria-controls="listadoCompleto1">
                                <i class="fas fa-table me-2" aria-hidden="true"></i>
                                <span id="listadoCompleto1" name="¿Cómo accedo al listado completo de mis artículos?">¿Cómo accedo al listado completo?</span>
                            </button>
                        </h3>
                        <div id="collapseListadoCompleto1" class="accordion-collapse collapse" data-bs-parent="#listadoCompletoAccordion">
                            <div class="accordion-body" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Para acceder al listado completo:</p>
                                    <ol>
                                        <li>Iniciá sesión en MLDataTrends.</li>
                                        <li>Navegá a <strong>Listado completo</strong> en el panel de control.</li>
                                        <li>Verás una tabla con todos tus artículos sincronizados.</li>
                                    </ol>
                                    <p>Asegurá que hayas sincronizado tus publicaciones en <strong>Sincronización</strong>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 2: Filtrar artículos -->
                    <div class="accordion-item" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="listadoCompleto2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseListadoCompleto2" aria-expanded="false" aria-controls="listadoCompleto2">
                                <i class="fas fa-filter me-2" aria-hidden="true"></i>
                                <span id="listadoCompleto2" name="¿Cómo filtro mis artículos por título o SKU?">¿Cómo filtro mis artículos por título o SKU?</span>
                            </button>
                        </h3>
                        <div id="collapseListadoCompleto2" class="accordion-collapse collapse" data-bs-parent="#listadoCompletoAccordion">
                            <div class="accordion-body" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Para filtrar artículos:</p>
                                    <ol>
                                        <li>En <strong>Listado completo</strong>, usá el campo de búsqueda.</li>
                                        <li>Ingresá una palabra clave para título o SKU interno.</li>
                                        <li>Seleccioná un estado (por ejemplo, "Activo").</li>
                                        <li>Hacé clic en <strong>Aplicar</strong>.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 3: Información disponible -->
                    <div class="accordion-item" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="listadoCompleto3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseListadoCompleto3" aria-expanded="false" aria-controls="listadoCompleto3">
                                <i class="fas fa-info-circle me-2" aria-hidden="true"></i>
                                <span id="listadoCompleto3" name="¿Qué información puedo ver en el listado completo?">¿Qué información puedo ver en el listado completo?</span>
                            </button>
                        </h3>
                        <div id="collapseListadoCompleto3" class="accordion-collapse collapse" data-bs-parent="#listadoCompletoAccordion">
                            <div class="accordion-body" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>El listado completo muestra:</p>
                                    <ul>
                                        <li><strong>Título</strong>: Nombre del artículo.</li>
                                        <li><strong>SKU interno</strong>: Código interno.</li>
                                        <li><strong>Precio</strong>: Precio actual.</li>
                                        <li><strong>Stock</strong>: Cantidad disponible.</li>
                                        <li><strong>Estado</strong>: Activo, pausado, etc.</li>
                                        <li><strong>Condición</strong>: Nuevo, usado.</li>
                                        <li><strong>Enlace</strong>: URL a la publicación.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 4: No veo artículos -->
                    <div class="accordion-item" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="listadoCompleto4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseListadoCompleto4" aria-expanded="false" aria-controls="listadoCompleto4">
                                <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                                <span id="listadoCompleto4" name="¿Por qué no veo artículos en el listado completo?">¿Por qué no veo artículos en la lista?</span>
                            </button>
                        </h3>
                        <div id="collapseListadoCompleto4" class="accordion-collapse collapse" data-bs-parent="#listadoCompletoAccordion">
                            <div class="accordion-body" id="collapseListadoCompleto4" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Si no ves artículos, verificá:</p>
                                    <ul>
                                        <li><strong>Sin cuentas vinculadas</strong>: Vinculá en <strong>Cuentas</strong>.</li>
                                        <li><strong>Sincronización incompleta</strong>: Revisá <strong>Sincronización</strong>.</li>
                                        <li><strong>Filtros</strong>: Quitá filtros de búsqueda o estado.</li>
                                        <li><strong>Sin publicaciones</strong>: Asegurá que haya publicaciones activas.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: SKU -->
                <h2 class="mt-5 mb-4">SKU</h2>
                <div class="accordion" id="skuAccordion">
                    <!-- Pregunta 1: Gestionar SKUs -->
                    <div class="accordion-item" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="sku1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSku1" aria-expanded="false" aria-controls="sku1">
                                <i class="fas fa-barcode me-2" aria-hidden="true"></i>
                                <span id="sku1" name="¿Cómo gestiono mis SKUs en MLDataTrends?">¿Cómo gestiono mis SKUs?</span>
                            </button>
                        </h3>
                        <div id="collapseSku1" class="accordion-collapse collapse" data-bs-parent="#skuAccordion">
                            <div class="accordion-body" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Para gestionar tus SKUs:</p>
                                    <ol>
                                        <li>Iniciá sesión y navegá a <strong>SKU</strong>.</li>
                                        <li>Verás una lista de artículos con sus SKUs internos.</li>
                                        <li>Podés buscar o filtrar para encontrar artículos específicos.</li>
                                    </ol>
                                    <p>Asegurá que tus publicaciones estén sincronizadas en <strong>Sincronización</strong>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 2: Buscar por SKU -->
                    <div class="accordion-item" itemscope="itemscope" id="sku2" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="sku2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSku2" aria-expanded="false" aria-controls="sku2">
                                <i class="fas fa-search me-2" aria-hidden="true"></i>
                                <span name="¿Cómo busco un artículo por SKU?">¿Cómo busco un artículo por SKU?</span>
                            </button>
                        </h3>
                        <div id="collapseSku2" class="accordion-collapse collapse" data-bs-parent="#skuAccordion">
                            <div class="accordion-body" itemscope="itemscope" id="collapseSku2" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Para buscar por SKU:</p>
                                    <ol>
                                        <li>En <strong>SKU</strong>, ingresá el SKU interno en el campo de búsqueda.</li>
                                        <li>Hacé clic en <strong>Buscar</strong>.</li>
                                        <li>El artículo aparecerá si está sincronizado.</li>
                                    </ol>
                                    <p>Verificá que el SKU sea correcto y que la sincronización esté completa.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 3: Información disponible -->
                    <div class="accordion-item" itemscope="itemscope" id="sku3" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="sku3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSku3" aria-expanded="false" aria-controls="sku3">
                                <i class="fas fa-info-circle me-2" aria-hidden="true"></i>
                                <span name="¿Qué información puedo ver en la sección SKU?">¿Qué información veo en SKU?</span>
                            </button>
                        </h3>
                        <div id="collapseSku3" class="accordion-collapse collapse" data-bs-parent="#skuAccordion">
                            <div class="accordion-body" id="collapseSku3" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>La sección <strong>SKU</strong> muestra:</p>
                                    <ul>
                                        <li><strong>SKU interno</strong>: Código único del artículo.</li>
                                        <li><strong>Título</strong>: Nombre de la publicación.</li>
                                        <li><strong>Precio</strong>: Precio actual.</li>
                                        <li><strong>Stock</strong>: Cantidad disponible.</li>
                                        <li><strong>Estado</strong>: Activo, pausado, etc.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 4: No veo SKUs -->
                    <div class="accordion-item" id="sku4" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="sku4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSku4" aria-expanded="false" aria-controls="sku4">
                                <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                                <span name="¿Por qué no veo mis SKUs en MLDataTrends?">¿Por qué no veo mis SKUs?</span>
                            </button>
                        </h3>
                        <div id="collapseSku4" class="accordion-collapse collapse" data-bs-parent="#skuAccordion">
                            <div class="accordion-body" id="collapseSku4" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Si no ves SKUsos:</p>
                                    <ul>
                                        <li><strong>Sin cuentas vinculadas:</strong> Vinculá en <strong>Cuentas</strong>.</li>
                                        <li><strong>Sincronización incompleta:</strong> Revisá en <strong>Sincronización</strong>.</p>
                                        <li><strong>Filtros:</strong> Quitá los filtros de búsqueda.</li>
                                        <li><strong>Sin SKUsos:</strong> Asegurá que tus publicaciones tengan SKUs internos.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Ventas -->
                <h2 class="mt-5 mb-4">Ventas</h2>
                <div class="accordion" id="ventasAccordion">
                    <!-- Pregunta 1: Acceder a ventas -->
                    <div class="accordion-item" id="ventas1" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="ventas1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVentas1" aria-expanded="false" aria-controls="ventas1">
                                <i class="fas fa-shopping-cart me-2" aria-hidden="true"></i>
                                <span name="¿Cómo accedo al reporte de ventas en MLDataTrends?">¿Cómo accedo al reporte de ventas?</span>
                            </button>
                        </h3>
                        <div id="collapseVentas1" class="accordion-collapse collapse" data-bs-parent="#ventasAccordion">
                            <div class="accordion-body" id="collapseVentas1" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Para ver tus ventas:</p>
                                    <ol>
                                        <li>Iniciá sesión en MLDataTrends.</li>
                                        <li>Navegá a <strong>Ventas</strong> en el panel de control.</li>
                                        <li>Verás un reporte con tus ventas sincronizadas.</li>
                                    </ol>
                                    <p>Asegurá que tus datos estén sincronizados en <strong>Sincronización</strong>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 2: Filtrar ventas -->
                    <div class="accordion-item" id="ventas2" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="ventas2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVentas2" aria-expanded="false" aria-controls="ventas2">
                                <i class="fas fa-filter me-2" aria-hidden="true"></i>
                                <span name="¿Cómo filtro mis ventas por fecha o producto?">¿Cómo filtrar ventas por fecha o producto?</span>
                            </button>
                        </h3>
                        <div id="collapseVentas2" class="accordion-collapse collapse" data-bs-parent="#ventasAccordion">
                            <div class="accordion-body" id="collapseVentas2" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Para filtrar ventas:</p>
                                    <ol>
                                        <li>En <strong>Ventas</strong>, usá los filtros disponibles.</li>
                                        <li>Ingresá un rango de fechas o un título de producto.</li>
                                        <li>Hacé clic en <strong>Aplicar</strong>.</li>
                                    </ol>
                                    <p>Esto te permite analizar ventas específicas.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 3: Información de ventas -->
                    <div class="accordion-item" id="ventas3" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="ventas3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVentas3" aria-expanded="false" aria-controls="ventas3">
                                <i class="fas fa-info-circle me-2" aria-hidden="true"></i>
                                <span name="¿Qué información incluye el reporte de ventas?">¿Qué información incluye el reporte?</span>
                            </button>
                        </h3>
                        <div id="collapseVentas3" class="accordion-collapse collapse" data-bs-parent="#ventasAccordion">
                            <div class="accordion-body" id="collapseVentas3" itemscope="itemscope" item="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>El reporte de ventas incluye:</p>
                                    <ul>
                                        <li><strong>Fecha</strong>: Cuándo se realizó la venta.</li>
                                        <li><strong>Título</strong>: Nombre del artículo vendido.</li>
                                        <li><strong>SKU</strong>: Código interno del artículo.</li>
                                        <li><strong>Cantidad</strong>: Unidades vendidas.</li>
                                        <li><strong>Precio unitario</strong>: Precio por unidad.</li>
                                        <li><strong>Total</strong>: Monto total de la venta.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 4: No veo ventas -->
                    <div id="ventas4" class="accordion-item" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="ventas4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVentas4" aria-expanded="false" aria-controls="ventas4">
                                <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                                <span name="¿Por qué no veo mis ventas en MLDataTrends?">¿Por qué no veo mis ventas?</span>
                            </button>
                        </h3>
                        <div id="collapseVentas4" class="accordion-collapse collapse" data-bs-parent="#ventasAccordion">
                            <div class="accordion-body" id="collapseVentas4" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Si no ves ventas, verificá:</p>
                                    <ul>
                                        <li><strong>Sin cuentas vinculadas:</strong>: Vincula en <strong>Cuentas</strong>.</li>
                                        <li><strong>Sincronización:</strong>: Revisá <strong>Sincronización</strong>.</li>
                                        <li><strong>Filtros:</strong>: Quitá-los filtros de fecha o producto.</li>
                                        <li><strong>Sin ventas:</strong>: Asegúrate de que tengas ventas recientes.</li>
                                    </div>
                                </ul>
                            </div>
                        </div>
                    </div>

                <!-- Sección: Promociones -->
                <h2 class="mt-5 mb-4">Promociones</h2>
                <div class="accordion" id="promocionesAccordion">
                    <!-- Pregunta 1: Gestionar promociones -->
                    <div id="promociones1" class="accordion-item" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 id="accordion-header" id="promociones1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePromociones1" aria-expanded="false" aria-controls="promociones1">
                                <i class="fas fa-tag me-2" aria-hidden="true"></i>
                                <span name="¿Cómo gestiono mis promociones en MLDataTrends?">¿Cómo gestiono mis promociones?</span>
                            </button>
                        </h3>
                        <div id="collapsePromociones1" class="accordion-collapse collapse" data-bs-parent="#promocionesAccordion">
                            <div class="accordion-body" id="collapsePromociones1" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Para p gestionar promociones:</p>
                                    <ol>
                                        <li><li>Iniciá sesión y navegá a <strong>Promociones</strong>.</li>
                                        <li><li>Verás una lista de tus promociones activas y pasadas.</li>
                                        <li><li>Podés filtrar para ver detalles específicos.</li>
                                    </ol>
                                    <p>Asegúrate de que tus publicaciones estén sincronizadas en <strong>Sincronización</strong>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 2: Filtrar promociones -->
                    <div id="promociones2" class="accordion-item" id="promociones2" itemscope="mainEntity" scope="itemscope" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="promociones2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePromociones2" aria-expanded="false" aria-controls="promociones2">
                                <i class="fas fa-filter me-2" aria-hidden="true"></i>
                                <span name="¿Cómo filtro mis promociones por tipo o estado?">¿Cómo filtro promociones?</span>
                            </button>
                        </h3>
                        <div id="collapsePromociones2" class="accordion-collapse collapse" data-bs-parent="#promocionesAccordion">
                            <div id="accordion-body" class="accordion-body" id="collapsePromociones2" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Para filtrar promociones:</p>
                                    <ol>
                                        <li><li>En <strong>Promociones</strong>, usá los filtros disponibles.</li>
                                        <li><li>Seleccioná un tipo (por ejemplo, descuento) o estado (activo, finalizado).</li>
                                        <li><li>Hacé clic en <strong>Aplicar</strong>.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 3: Información de promociones -->
                    <div id="promociones3" class="accordion-item" id="promociones3" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 id="accordion-header" class="accordion-header" id="promociones3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePromociones3" aria-expanded="false" aria-controls="promociones3">
                                <i class="fas fa-info-circle me-2" aria-hidden="true"></i>
                                <span name="¿Qué información incluye la sección Promociones?">¿Qué información veo en Promociones?</span>
                            </button>
                        </h3>
                        <div id="collapsePromociones3" class="accordion-collapse collapse" data-bs-parent="#promocionesAccordion">
                            <div class="accordion-body" id="collapsePromociones3" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>La sección <strong>Promociones</strong> muestra:</p>
                                    <ul>
                                        <li><strong>Título</strong>: Nombre del artículo en promoción.</li>
                                        <li><strong>Descuento</strong>: Porcentaje o monto descontado.</li>
                                        <li><strong>Precio original</strong>: Precio antes del descuento.</li>
                                        <li><strong>Precio promocional</strong>: Precio con descuento.</li>
                                        <li><strong>Estado</strong>: Activa, finalizada.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 4: No veo promociones -->
                    <div id="promociones4" class="accordion-item" id="promociones4" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="promociones4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePromociones4" aria-expanded="false" aria-controls="promociones4">
                                <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                                <span name="¿Por qué no veo mis promociones en MLDataTrends?">¿Por qué no veo mis promociones?</span>
                            </button>
                        </h3>
                        <div id="collapsePromociones4" class="accordion-collapse collapse" data-bs-parent="#promocionesAccordion">
                            <div class="accordion-body" id="collapsePromociones4" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Si no ves promociones, verificá:</p>
                                    <ul>
                                        <li><strong>Sin cuentas vinculadas</strong>: Vinculá en <strong>Cuentas</strong>.</li>
                                        <li><strong>Sincronización incompleta</strong>: Revisá <strong>Sincronización</strong>.</li>
                                        <li><strong>Filtros</strong>: Quitá filtros aplicados.</li>
                                        <li><strong>Sin promociones</strong>: Asegurá que tengas promociones activas.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Catálogo -->
                <h2 class="mt-5 mb-4">Catálogo</h2>
                <div class="accordion" id="catalogoAccordion">
                    <!-- Pregunta 1: Acceder a catálogo -->
                    <div class="accordion-item" id="catalogo1" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="catalogo1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCatalogo1" aria-expanded="false" aria-controls="catalogo1">
                                <i class="fas fa-book me-2" aria-hidden="true"></i>
                                <span name="¿Cómo accedo al catálogo de MercadoLibre en MLDataTrends?">¿Cómo accedo al catálogo?</span>
                            </button>
                        </h3>
                        <div id="collapseCatalogo1" class="accordion-collapse collapse" data-bs-parent="#catalogoAccordion">
                            <div class="accordion-body" id="collapseCatalogo1" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Para ver el catálogo:</p>
                                    <ol>
                                        <li>Iniciá sesión en MLDataTrends.</li>
                                        <li>Navegá a <strong>Catálogo</strong> en el panel de control.</li>
                                        <li>Verás los artículos incluidos en el catálogo de MercadoLibre.</li>
                                    </ol>
                                    <p>Asegurá que tus datos estén sincronizados en <strong>Sincronización</strong>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 2: Filtrar catálogo -->
                    <div class="accordion-item" id="catalogo2" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="catalogo2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCatalogo2" aria-expanded="false" aria-controls="catalogo2">
                                <i class="fas fa-filter me-2" aria-hidden="true"></i>
                                <span name="¿Cómo filtro los artículos en el catálogo?">¿Cómo filtro el catálogo?</span>
                            </button>
                        </h3>
                        <div id="collapseCatalogo2" class="accordion-collapse collapse" data-bs-parent="#catalogoAccordion">
                            <div class="accordion-body" id="collapseCatalogo2" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Para filtrar el catálogo:</p>
                                    <ol>
                                        <li>En <strong>Catálogo</strong>, usá los filtros disponibles.</li>
                                        <li>Ingresá un título o categoría.</li>
                                        <li>Hacé clic en <strong>Aplicar</strong>.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 3: Información del catálogo -->
                    <div class="accordion-item" id="catalogo3" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="catalogo3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCatalogo3" aria-expanded="false" aria-controls="catalogo3">
                                <i class="fas fa-info-circle me-2" aria-hidden="true"></i>
                                <span name="¿Qué información incluye el catálogo?">¿Qué información del catálogo?</span>
                            </button>
                        </h3>
                        <div id="collapseCatalogo3" class="accordion-collapse collapse" data-bs-parent="#catalogoAccordion">
                            <div class="accordion-body" id="collapseCatalogo3" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>El catálogo muestra:</p>
                                    <ul>
                                        <li><strong>Título</strong>: Nombre del artículo.</li>
                                        <li><strong>Categoría</strong>: Categoría en MercadoLibre.</li>
                                        <li><strong>SKU</strong> Código del artículo:.</li>
                                        <li><strong>Estado</strong>: Incluido en el catálogo.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 4: No veo artículos en catálogo -->
                    <div class="accordion-item" id="catalogo4" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="catalogo4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCatalogo4" aria-expanded="false" aria-controls="catalogo4">
                                <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                                <span name="¿Por qué no veo artículos en el catálogo?">¿Por qué no veo el catálogo?</span>
                            </button>
                        </h3>
                        <div id="collapseCatalogo4" class="accordion-collapse collapse" data-bs-parent="#catalogoAccordion">
                            <div class="accordion-body" id="collapseCatalogo4" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Si no ves artículos, verificá:</p>
                                    <ul>
                                        <li><strong>Sin cuentas vinculadas</strong>: Vinculá en <strong>Cuentas</strong>.</li>
                                        <li><strong>Sincronización incompleta</strong>: Revisá <strong>Sincronización</strong>.</li>
                                        <li><strong>Filtros</strong>: Quitá filtros aplicados.</li>
                                        <li><strong>Sin catálogo</strong>: Asegurá que tus artículos estén en el catálogo de MercadoLibre.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Estadísticas -->
                <h2 class="mt-5 mb-4">Estadísticas</h2>
                <div class="accordion" id="estadisticasAccordion">
                    <!-- Pregunta 1: Acceder a estadísticas -->
                    <div class="accordion-item" id="estadisticas1" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="estadisticas1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstadisticas1" aria-expanded="false" aria-controls="estadisticas1">
                                <i class="fas fa-chart-bar me-2" aria-hidden="true"></i>
                                <span name="¿Cómo accedo a las estadísticas de ventas en MLDataTrends?">¿Cómo accedo a estadísticas?</span>
                            </button>
                        </h3>
                        <div id="collapseEstadisticas1" class="accordion-collapse collapse" data-bs-parent="#estadisticasAccordion">
                            <div class="accordion-body" id="collapseEstadisticas1" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Para ver estadísticas:</p>
                                    <ol>
                                        <li>Iniciá sesión en MLDataTrends.</li>
                                        <li>Navegá a <strong>Estadísticas</strong> en el panel de control.</li>
                                        <li>Verás reportes de ventas y desempeño.</li>
                                    </ol>
                                    <p>Asegurá que tus datos estén sincronizados en <strong>Sincronización</strong>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 2: Filtrar estadísticas -->
                    <div class="accordion-item" id="estadisticas2" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="estadisticas2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstadisticas2" aria-expanded="false" aria-controls="estadisticas2">
                                <i class="fas fa-filter me-2" aria-hidden="true"></i>
                                <span name="¿Cómo filtro las estadísticas por período?">¿Cómo filtro estadísticas?</span>
                            </button>
                        </h3>
                        <div id="collapseEstadisticas2" class="accordion-collapse collapse" data-bs-parent="#estadisticasAccordion">
                            <div class="accordion-body" id="collapseEstadisticas2" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Para filtrar estadísticas:</p>
                                    <ol>
                                        <li>En <strong>Estadísticas</strong>, seleccioná un rango de fechas.</li>
                                        <li>Podés filtrar por producto o categoría.</li>
                                        <li>Hacé clic en <strong>Aplicar</strong>.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 3: Información de estadísticas -->
                    <div class="accordion-item" id="estadisticas3" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="estadisticas3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstadísticas3" aria-expanded="false" aria-controls="estadisticas3">
                                <i class="fas fa-info-circle me-2" aria-hidden="true"></i>
                                <span name="¿Qué información incluyen las estadísticas?">¿Qué incluyen estadísticas?</span>
                            </button>
                        </h3>
                        <div id="collapseEstadísticas3" class="accordion-collapse collapse" data-bs-parent="#estadisticasAccordion">
                            <div class="accordion-body" id="collapseEstadísticas3" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Las estadísticas incluyen:</p>
                                    <ul>
                                        <li><strong>Ventas totales</strong>: Monto total por período.</li>
                                        <li><strong>Unidades vendidas</strong>: Cantidad de productos.</li>
                                        <li><strong>Productos más vendidos</strong>: Lista de artículos destacados.</li>
                                        <li><strong>Tendencias</strong>: Gráficos de desempeño.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 4: No veo estadísticas -->
                    <div class="accordion-item" id="estadisticas4" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="estadisticas4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstadísticas4" aria-expanded="false" aria-controls="estadisticas4">
                                <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                                <span name="¿Por qué no veo estadísticas en MLDataTrends?">¿Por qué no veo estadísticas?</span>
                            </button>
                        </h3>
                        <div id="collapseEstadísticas4" class="accordion-collapse collapse" data-bs-parent="#estadisticasAccordion">
                            <div class="accordion-body" id="collapseEstadísticas4" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Si no ves estadísticas, verificá:</p>
                                    <ul>
                                        <li><strong>Sin cuentas vinculadas</strong>: Vinculá en <strong>Cuentas</strong>.</li>
                                        <li><strong>Sincronización incompleta</strong>: Revisá <strong>Sincronización</strong>.</li>
                                        <li><strong>Filtros</strong>: Quitá filtros aplicados.</li>
                                        <li><strong>Sin datos</strong>: Asegurá que tengas ventas recientes.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Sincronización -->
                <h2 class="mt-5 mb-4">Sincronización</h2>
                <div class="accordion" id="sincronizaciónAccordion">
                    <!-- Pregunta 1: Iniciar sincronización -->
                    <div class="accordion-item" id="sincronización1" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="sincronización1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSincronización1" aria-expanded="false" aria-controls="sincronización1">
                                <i class="fas fa-sync-alt me-2" aria-hidden="true"></i>
                                <span name="¿Cómo inicio la sincronización de datos?">Inicio de sincronización</span>
                            </button>
                        </h3>
                        <div id="collapseSincronización1" class="accordion-collapse collapse" data-bs-parent="#sincronizaciónAccordion">
                            <div class="accordion-body" id="collapseSincronización1" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Para sincronizar datos:</p>
                                    <ol>
                                        <li>Iniciá sesión y navegá a <strong>Sincronización</strong>.</li>
                                        <li>Seleccioná la cuenta de MercadoLibre.</li>
                                        <li>Hacé clic en <strong>Iniciar sincronización</strong>.</li>
                                    </ol>
                                    <p>El proceso puede tomar varios minutos según la cantidad de datos.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 2: Tipos de sincronización -->
                    <div class="accordion-item" id="sincronización2" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="sincronización2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSincronización2" aria-expanded="false" aria-controls="sincronización2">
                                <i class="fas fa-cogs me-2" aria-hidden="true"></i>
                                <span name="¿Qué datos se sincronizan en MLDataTrends?">Datos sincronizados</span>
                            </button>
                        </h3>
                        <div id="collapseSincronización2" class="accordion-collapse collapse" data-bs-parent="#sincronizaciónAccordion">
                            <div class="accordion-body" id="collapseSincronización2" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>La sincronización incluye:</p>
                                    <ul>
                                        <li><strong>Publicaciones</strong>: Títulos, precios, stock, estado.</li>
                                        <li><strong>Ventas</strong>: Fechas, productos, montos.</li>
                                        <li><strong>Promociones</strong>: Descuentos y precios promocionales.</li>
                                        <li><strong>SKUs</strong>: Códigos internos.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 3: Estado de sincronización -->
                    <div class="accordion-item" id="sincronización3" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="sincronización3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSincronización3" aria-expanded="false" aria-controls="sincronización3">
                                <i class="fas fa-info-circle me-2" aria-hidden="true"></i>
                                <span name="¿Cómo verifico el estado de la sincronización?">Estado de sincronización</span>
                            </button>
                        </h3>
                        <div id="collapseSincronización3" class="accordion-collapse collapse" data-bs-parent="#sincronizaciónAccordion">
                            <div class="accordion-body" id="collapseSincronización3" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Para verificar:</p>
                                    <ol>
                                    <li>En <strong>Sincronización</strong>, buscá el estado de la cuenta.</li>
                                        <li>Verás si la sincronización está en curso, completada o con errores.</li>
                                    </ol>
                                    <p>Si hay errores, revisá las notificaciones para detalles.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 4: Problemas de sincronización -->
                    <div class="accordion-item" id="sincronización4" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="sincronización4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSincronización4" aria-expanded="false" aria-controls="sincronización4">
                                <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                                <span name="¿Por qué falla la sincronización en MLDataTrends?">Falla en sincronización</span>
                            </button>
                        </h3>
                        <div id="collapseSincronización4" class="accordion-collapse collapse" data-bs-parent="#sincronizaciónAccordion">
                            <div class="accordion-body" id="collapseSincronización4" itemscope="itemscope" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                <div>
                                    <p>Si la sincronización falla, verificá:</p>
                                    <ul>
                                        <li><strong>Token expirado</strong>: Revinculá en <strong>Cuentas</strong>.</li>
                                        <li><strong>Conexión a internet</strong>: Asegurá una conexión estable.</li>
                                        <li><strong>Límites de API</strong>: Esperá si MercadoLibre restringe el acceso.</li>
                                        <li><strong>Cuenta activa</strong>: Confirmá que tu cuenta de MercadoLibre esté activa.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Competencia -->
                <h2 class="mt-5 mb-4">Competencia</h2>
                <div class="accordion" id="competenciaAccordion">
                    <!-- Pregunta 1 -->
                    <div class="accordion-item" id="competencia1" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="competencia1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCompetencia1" aria-expanded="false" aria-controls="competencia1">
                                <i class="fas fa-search me-2" aria-hidden="true"></i>
                                <span name="¿Cómo sigo publicaciones de competidores?">Seguir competidores</span>
                            </button>
                        </h3>
                        <div id="collapseCompetencia1" class="accordion-collapse collapse" data-bs-parent="#competenciaAccordion">
                            <div class="accordion-body" id="collapseCompetencia1" itemscope="itemscope" itemprop="acceptedAnswer">
                                <div>
                                    <p>Para seguir competidores:</p>
<ol>
                                        <li>Iniciá sesión y navegá a <strong>Gestión de Competidores</strong>.</li>
                                        <li>Ingresá el <em>Seller ID</em> o <em>Nickname</em> del competidor.</li>
                                        <li>Marcá las publicaciones que querés seguir.</li>
                                        <li>Hacé clic en <strong>Seguir</strong>.</li>
                                    </ol>
                                    <p>Las publicaciones seguirán marcadas para monitoreo.</p>
</div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 2 -->
                    <div class="accordion-item" id="competencia2" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="competencia2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCompetencia2" aria-expanded="false" aria-controls="competencia2">
                                <i class="fas fa-user me-2" aria-hidden="true"></i>
                                <span name="¿Qué es el Seller ID y cómo lo encuentro?">¿Qué es el Seller ID?</span>
                            </button>
                        </h3>
                        <div id="collapseCompetencia2" class="accordion-collapse collapse" data-bs-parent="#competenciaAccordion">
                            <div class="accordion-body" id="collapseCompetencia2" itemscope="itemscope" itemprop="acceptedAnswer">
                                <div>
                                    <p>El <em>Seller ID</em> identifica a un vendedor:</p>
                                    <ol>
                                        <li>Ingresá a una publicación del vendedor.</li>
                                        <li>Hacé clic en su nombre para ver su perfil.</li>
                                        <li>En la URL, buscá el número después de <code>/perfil/</code>.</li>
                                    </ol>
                                    <p>También podés usar el <em>Nickname</em> en MLDataTrends.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 3 -->
                    <div class="accordion-item" id="competencia3" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="competencia3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCompetencia3" aria-expanded="false" aria-controls="competencia3">
                                <i class="fas fa-sync me-2" aria-hidden="true"></i>
                                <span name="¿Cómo actualizo datos de competidores?">Actualizar competidores</span>
                            </button>
                        </h3>
                        <div id="collapseCompetencia3" class="accordion-collapse collapse" data-bs-parent="#competenciaAccordion">
                            <div class="accordion-body" id="collapseCompetencia3" itemscope="itemscope" itemprop="acceptedAnswer">
                                <div>
                                    <p>Para actualizar datos:</p>
                                    <ol>
                                        <li>En <strong>Gestión de Competidores</strong>, buscá el competidor.</li>
                                        <li>Hacé clic en <strong>Actualizar</strong>.</li>
                                        <li>Los datos (precios, descuentos) se refrescarán.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pregunta 4 -->
                    <div class="accordion-item" id="competencia4" itemscope="itemscope" itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="accordion-header" id="competencia4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCompetencia4" aria-expanded="false" aria-controls="competencia4">
                                <i class="fas fa-file-excel me-2" aria-hidden="true"></i>
                                <span name="¿Cómo exporto datos de competidores a Excel?">Exportar a Excel</span>
                            </button>
                        </h3>
                        <div id="collapseCompetencia4" class="accordion-collapse collapse" data-bs-parent="#competenciaAccordion">
                            <div class="accordion-body" id="collapseCompetencia4" itemscope="itemscope" itemprop="acceptedAnswer">
                                <div>
                                    <p>Para exportar:</p>
                                    <ol>
                                        <li>En <strong>Gestión de Competidores</strong>, seleccioná las publicaciones.</li>
                                        <li>Hacé clic en <strong>Exportar a Excel</strong>.</li>
                                        <li>Se descargará un archivo con los datos.</li>
                                    </ol>
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
                    <p>Potenciamos a los vendedores de MercadoLibre con herramientas avanzadas.</p>
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
    <script src="//code.tidio.co/jo26sf9xwxm54yytrbdsaeflv2b1timh.js" async></script>
</body>
</html>
