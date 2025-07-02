<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Preguntas frecuentes sobre MLDATATRENDS. Aprende a vincular tu cuenta de Mercado Libre, sincronizar artículos, ver publicaciones activas o en catálogo, gestionar promociones, SKUs y stock en depósito y en Mercado Libre. Explorá estadísticas de ventas y controlá el stock crítico.">
    <meta name="keywords" content="MLDATATRENDS, preguntas frecuentes, vincular cuenta Mercado Libre, sincronizar artículos, ver publicaciones, publicaciones en catálogo, gestionar SKUs, promociones, estadísticas de ventas, stock en depósito, stock en Mercado Libre, stock crítico, tutorial MLDATATRENDS">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://mldatatrends.com/preguntas-frecuentes">
    <title>Preguntas Frecuentes - MLDataTrends: Gestión de Ventas en MercadoLibre</title>

    <!-- Open Graph -->
    <meta property="og:title" content="Preguntas Frecuentes - MLDataTrends">
    <meta property="og:description" content="Aprendé cómo gestionar tu cuenta de MercadoLibre, sincronizar datos, analizar ventas, SKU, promociones, catálogo, estadísticas y sincronización con nuestras Preguntas Frecuentes detalladas en MLDataTrends.">
    <meta property="og:url" content="{{ url('/preguntas-frecuentes') }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('images/faq.webp') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Preguntas Frecuentes - MLDataTrends">
    <meta name="twitter:description" content="Aprendé cómo gestionar tu cuenta de MercadoLibre, sincronizar datos, analizar ventas, SKU, promociones, catálogo, estadísticas y sincronización con nuestras Preguntas Frecuentes detalladas.">
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
<section class="faq-section py-5 bg-light" role="region" aria-label="Preguntas Frecuentes">
    <div class="container">
        <h1 class="text-center mb-5">Preguntas Frecuentes sobre MLDataTrends</h1>
        <p class="lead text-center mb-5">¿No sabés cómo empezar a usar MLDataTrends para gestionar tus ventas en MercadoLibre? Acá te explicamos paso a paso cómo vincular tu cuenta, sincronizar artículos, gestionar publicaciones, SKU, ventas, promociones, catálogo, estadísticas y sincronización, con ejemplos prácticos y soluciones a problemas comunes.</p>

<!-- Índice de secciones -->
<div class="faq-index mb-5">
    <h3 class="h5 mb-3">Índice</h3>
    <div class="card shadow-sm">
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><a href="#cuentasAccordion" class="text-decoration-none text-primary fw-medium">Cuentas</a></li>
            <li class="list-group-item"><a href="#publicacionesAccordion" class="text-decoration-none text-primary fw-medium">Publicaciones</a></li>
            <li class="list-group-item"><a href="#listadoCompletoAccordion" class="text-decoration-none text-primary fw-medium">Listado completo</a></li>
            <li class="list-group-item"><a href="#skuAccordion" class="text-decoration-none text-primary fw-medium">SKU</a></li>
            <li class="list-group-item"><a href="#ventasAccordion" class="text-decoration-none text-primary fw-medium">Ventas</a></li>
            <li class="list-group-item"><a href="#promocionesAccordion" class="text-decoration-none text-primary fw-medium">Promociones</a></li>
            <li class="list-group-item"><a href="#catalogoAccordion" class="text-decoration-none text-primary fw-medium">Catálogo</a></li>
            <li class="list-group-item"><a href="#estadisticasAccordion" class="text-decoration-none text-primary fw-medium">Estadísticas</a></li>
            <li class="list-group-item"><a href="#sincronizacionAccordion" class="text-decoration-none text-primary fw-medium">Sincronización</a></li>
        </ul>
    </div>
</div>

        <div>
            <!-- Sección: Cuentas -->
            <h2 class="mb-4" id="cuentasAccordion">Cuentas</h2>
            <div class="custom-accordion" id="cuentasAccordion">
                <!-- Pregunta 1: Vincular cuenta -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="cuentas1">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCuentas1" aria-expanded="false" aria-controls="collapseCuentas1">
                            <i class="fas fa-link icon" aria-hidden="true"></i>
                            ¿Cómo vinculo mi cuenta de MercadoLibre a MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapseCuentas1" class="custom-accordion-collapse collapse" data-bs-parent="#cuentasAccordion">
                        <div class="custom-accordion-body">
                            <p>Vincular tu cuenta de MercadoLibre a MLDataTrends es el primer paso para gestionar tus ventas. Este proceso conecta tu cuenta de manera segura. Seguí estos pasos:</p>
                            <ol>
                                <li>Iniciá sesión en <a href="https://mldatatrends.com/login">MLDataTrends.com/login</a>.</li>
                                <li>Navegá a <strong>Cuentas</strong> en el panel de control.</li>
                                <li>Hacé clic en <strong>Vincular cuenta de MercadoLibre</strong>.</li>
                                <li>Iniciá sesión en MercadoLibre y aceptá los permisos solicitados.</li>
                                <li>Una vez autorizado, verás tu cuenta listada con el <em>Seller ID</em> (por ejemplo, 123456789) y el estado "Vinculada".</li>
                            </ol>
                            <p>Si la vinculación falla, asegurá que tu navegador permita pop-ups y que tu cuenta de MercadoLibre esté activa. Verificá que usaste la cuenta correcta si tenés varias (por ejemplo, "TiendaEjemplo"). Contactá a soporte@mldatatrends.com si el problema persiste.</p>
                        </div>
                    </div>
                </div>
                <!-- Pregunta 2: Información de la cuenta -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="cuentas2">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCuentas2" aria-expanded="false" aria-controls="collapseCuentas2">
                            <i class="fas fa-info-circle icon" aria-hidden="true"></i>
                            ¿Qué información puedo ver de mi cuenta de MercadoLibre en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapseCuentas2" class="custom-accordion-collapse collapse" data-bs-parent="#cuentasAccordion">
                        <div class="custom-accordion-body">
                            <p>La sección <strong>Cuentas</strong> te muestra un resumen de tu cuenta vinculada, útil para verificar su estado. Incluye:</p>
                            <ul>
                                <li><strong>Seller ID</strong>: Identificador único (por ejemplo, 123456789).</li>
                                <li><strong>Nombre de la cuenta</strong>: Apodo de tu perfil (por ejemplo, "TiendaEjemplo").</li>
                                <li><strong>Estado</strong>: Vinculada, pendiente, o necesita revinculación.</li>
                                <li><strong>Última sincronización</strong>: Fecha y hora (por ejemplo, "2025-06-25 10:30").</li>
                            </ul>
                            <p>Para acceder, iniciá sesión y hacé clic en <strong>Cuentas</strong>. Si no ves datos, asegurá que tu cuenta esté vinculada correctamente. Revisá si aparece "Token expirado" y revinculá si es necesario. Esta sección te ayuda a confirmar que tu cuenta está lista para sincronizar datos.</p>
                        </div>
                    </div>
                </div>
                <!-- Pregunta 3: Sincronizar artículos -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="cuentas3">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCuentas3" aria-expanded="false" aria-controls="collapseCuentas3">
                            <i class="fas fa-sync-alt icon" aria-hidden="true"></i>
                            ¿Cómo sincronizo mis artículos de MercadoLibre con MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapseCuentas3" class="custom-accordion-collapse collapse" data-bs-parent="#cuentasAccordion">
                        <div class="custom-accordion-body">
                            <p>Sincronizar tus artículos actualiza tus publicaciones y ventas en MLDataTrends. Seguí estos pasos:</p>
                            <ol>
                                <li>Iniciá sesión y navegá a <strong>Cuentas</strong>.</li>
                                <li>Seleccioná la cuenta vinculada (por ejemplo, "TiendaEjemplo").</li>
                                <li>Hacé clic en <strong>Iniciar sincronización</strong>.</li>
                                <li>Esperá a que termine (puede tomar minutos si tenés muchas publicaciones, como 500 artículos).</li>
                            </ol>
                            <p>Los datos aparecerán en <strong>Publicaciones</strong> y <strong>Listado completo</strong>. Verificá el progreso en <strong>Sincronización</strong>. Si no inicia, asegurá que tu cuenta esté vinculada y el token esté activo. Por ejemplo, si tenés publicaciones como "Zapatillas Nike", estas se listarán tras sincronizar. Contactá a soporte@mldatatrends.com si hay errores.</p>
                        </div>
                    </div>
                </div>
                <!-- Pregunta 4: Problemas con sincronización -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="cuentas4">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCuentas4" aria-expanded="false" aria-controls="collapseCuentas4">
                            <i class="fas fa-exclamation-triangle icon" aria-hidden="true"></i>
                            ¿Por qué no veo mis publicaciones después de sincronizar?
                        </button>
                    </h3>
                    <div id="collapseCuentas4" class="custom-accordion-collapse collapse" data-bs-parent="#cuentasAccordion">
                        <div class="custom-accordion-body">
                            <p>Si no ves tus publicaciones tras sincronizar, revisá estos puntos:</p>
                            <ul>
                                <li><strong>Token expirado</strong>: Revinculá tu cuenta en <strong>Cuentas</strong>.</li>
                                <li><strong>Sin publicaciones activas</strong>: Verificá en MercadoLibre que tenés publicaciones activas.</li>
                                <li><strong>Errores de sincronización</strong>: Revisá <strong>Sincronización</strong> para mensajes como "Límite de consultas alcanzado".</li>
                                <li><strong>Filtros aplicados</strong>: Quitá filtros en <strong>Publicaciones</strong> o <strong>Listado completo</strong>.</li>
                            </ul>
                            <p>Por ejemplo, si sincronizaste "Zapatillas Nike" pero no aparece, asegurá que esté activa en MercadoLibre. Intentá sincronizar nuevamente tras 10 minutos. Si el problema persiste, contactá a soporte@mldatatrends.com con detalles del error.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección: Publicaciones -->
            <h2 class="mt-5 mb-4" id="publicacionesAccordion">Publicaciones</h2>
            <div class="custom-accordion" id="publicacionesAccordion">
                <!-- Pregunta 1: Ver publicaciones -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="publicaciones1">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePublicaciones1" aria-expanded="false" aria-controls="collapsePublicaciones1">
                            <i class="fas fa-list icon" aria-hidden="true"></i>
                            ¿Cómo veo mis publicaciones de MercadoLibre en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapsePublicaciones1" class="custom-accordion-collapse collapse" data-bs-parent="#publicacionesAccordion">
                        <div class="custom-accordion-body">
                            <p>La sección <strong>Publicaciones</strong> te muestra tus publicaciones activas en MercadoLibre. Seguí estos pasos:</p>
                            <ol>
                                <li>Iniciá sesión en <a href="https://mldatatrends.com/login">MLDataTrends.com/login</a>.</li>
                                <li>Hacé clic en <strong>Publicaciones</strong> en el panel.</li>
                                <li>Verás una lista con detalles como título (por ejemplo, "Zapatillas Nike Air"), precio ($10,000), stock (15 unidades), y estado ("Activa").</li>
                                <li>Ordená por columna (por ejemplo, precio) haciendo clic en el encabezado.</li>
                            </ol>
                            <p>Asegurá que tu cuenta esté vinculada en <strong>Cuentas</strong> y sincronizada en <strong>Sincronización</strong>. Si no ves publicaciones, revisá si están activas en MercadoLibre o quitá filtros aplicados. Esta sección es ideal para monitorear tu inventario.</p>
                        </div>
                    </div>
                </div>
                <!-- Pregunta 2: Buscar por ID -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="publicaciones2">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePublicaciones2" aria-expanded="false" aria-controls="collapsePublicaciones2">
                            <i class="fas fa-search icon" aria-hidden="true"></i>
                            ¿Cómo busco una publicación específica por ID en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapsePublicaciones2" class="custom-accordion-collapse collapse" data-bs-parent="#publicacionesAccordion">
                        <div class="custom-accordion-body">
                            <p>Para encontrar una publicación por su ID de MercadoLibre (MLA ID), seguí estos pasos:</p>
                            <ol>
                                <li>Navegá a <strong>Publicaciones</strong> en el panel.</li>
                                <li>Ingresá el MLA ID (por ejemplo, MLA987654321) en el campo de búsqueda.</li>
                                <li>Hacé clic en la lupa o presioná Enter.</li>
                                <li>Verás la publicación con detalles como título, precio, y enlace a MercadoLibre.</li>
                            </ol>
                            <p>El MLA ID está en la URL de la publicación (por ejemplo, <code>https://www.mercadolibre.com.ar/MLA-987654321</code>). Asegurá que la publicación esté sincronizada en <strong>Sincronización</strong>. Si no aparece, verificá que el ID sea correcto o que la publicación esté activa. Contactá a soporte@mldatatrends.com si hay problemas.</p>
                        </div>
                    </div>
                </div>
                <!-- Pregunta 3: Filtrar por estado -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="publicaciones3">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePublicaciones3" aria-expanded="false" aria-controls="collapsePublicaciones3">
                            <i class="fas fa-filter icon" aria-hidden="true"></i>
                            ¿Cómo filtro mis publicaciones por estado o categoría en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapsePublicaciones3" class="custom-accordion-collapse collapse" data-bs-parent="#publicacionesAccordion">
                        <div class="custom-accordion-body">
                            <p>Filtrar publicaciones te ayuda a enfocarte en un grupo específico. Seguí estos pasos:</p>
                            <ol>
                                <li>En <strong>Publicaciones</strong>, buscá el panel de filtros.</li>
                                <li>Seleccioná un estado (por ejemplo, "Activas" o "Pausadas").</li>
                                <li>Elegí una categoría (por ejemplo, "Electrónica") si está disponible.</li>
                                <li>Hacé clic en <strong>Aplicar filtros</strong>.</li>
                            </ol>
                            <p>Por ejemplo, filtrá por "Activas" y "Electrónica" para ver solo esas publicaciones. Si no hay resultados, quitá los filtros o verificá la sincronización en <strong>Sincronización</strong>. Asegurá que tu cuenta esté vinculada en <strong>Cuentas</strong>. Esta funcionalidad es útil para gestionar grandes inventarios.</p>
                        </div>
                    </div>
                </div>
                <!-- Pregunta 4: No veo publicaciones -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="publicaciones4">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePublicaciones4" aria-expanded="false" aria-controls="collapsePublicaciones4">
                            <i class="fas fa-exclamation-triangle icon" aria-hidden="true"></i>
                            ¿Por qué no veo todas mis publicaciones en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapsePublicaciones4" class="custom-accordion-collapse collapse" data-bs-parent="#publicacionesAccordion">
                        <div class="custom-accordion-body">
                            <p>Si no ves todas tus publicaciones, revisá estos puntos:</p>
                            <ul>
                                <li><strong>Cuenta no vinculada</strong>: Verificá en <strong>Cuentas</strong> que tu cuenta esté activa.</li>
                                <li><strong>Sincronización incompleta</strong>: Revisá <strong>Sincronización</strong> si está en curso.</li>
                                <li><strong>Filtros aplicados</strong>: Quitá filtros haciendo clic en "Restablecer filtros".</li>
                                <li><strong>Sin publicaciones creadas</strong>: Confirmá en MercadoLibre que tenés publicaciones activas.</li>
                            </ul>
                            <p>Por ejemplo, si "Zapatillas Nike" no aparece, asegurá que esté activa y sincronizada. Intentá sincronizar nuevamente tras 10 minutos. Si el problema persiste, contactá a soporte@mldatatrends.com con detalles.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección: Listado completo -->
            <h2 class="mt-5 mb-4" id="listadoCompletoAccordion">Listado completo</h2>
            <div class="custom-accordion" id="listadoCompletoAccordion">
                <!-- Pregunta 1: Acceder al listado -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="listadoCompleto1">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseListadoCompleto1" aria-expanded="false" aria-controls="collapseListadoCompleto1">
                            <i class="fas fa-table icon" aria-hidden="true"></i>
                            ¿Cómo accedo al listado completo de mis artículos en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapseListadoCompleto1" class="custom-accordion-collapse collapse" data-bs-parent="#listadoCompletoAccordion">
                        <div class="custom-accordion-body">
                            <p>La sección <strong>Listado completo</strong> muestra todos tus artículos sincronizados. Seguí estos pasos:</p>
                            <ol>
                                <li>Iniciá sesión en <a href="https://mldatatrends.com/login">MLDataTrends.com/login</a>.</li>
                                <li>Hacé clic en <strong>Listado completo</strong> en el enlace.</li>
                                <li>Verás una tabla con título (por ejemplo, "Smartphone Samsung Galaxy"), SKU (por ejemplo, "SAM123"), precio ($50,000), stock (10 unidades), por ejemplo.</li>
                                <li>Ordená por columna haciendo clic en el encabezado.</li>
                            </ol>
                            <p>Asegura que tu cuenta esté vinculada en <strong>Cuentas</strong> y sincronizada en <strong>Sincronización</strong>. Si la tabla está vacía, revisá si hay publicaciones activas en MercadoLibre. Esta sección es ideal para gestionar inventarios grandes.</p>
                        </div>
                    </div>
                </div>
                <!-- Pregunta 2: Filtrar artículos -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="listadoCompleto2">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseListadoCompleto2" aria-expanded="false" aria-controls="collapseListadoCompleto2">
                            <i class="fas fa-filter icon" aria-hidden="true"></i>
                            ¿Cómo filtro mis artículos por título o SKU en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapseListadoCompleto2" class="custom-accordion-collapse collapse" data-bs-parent="#listadoCompletoAccordion">
                        <div class="custom-accordion-body">
                            <p>Filtrar artículos te ayuda a encontrar productos específicos. Seguí estos pasos:</p>
                            <ol>
                                <li>Navegá a <strong>Listado completo</strong>.</li>
                                <li>Ingresá un título (por ejemplo, "Zapatillas") o SKU (por ejemplo, "ZAP123") en el campo de búsqueda.</li>
                                <li>Seleccioná un estado (por ejemplo, "Activo") si querés.</li>
                                <li>Hacé clic en la lupa o presioná Enter.</li>
                            </ol>
                            <p>Por ejemplo, buscá "Zapatillas Nike" y filtrá por "Activo" para ver solo esas publicaciones. Si no hay resultados, quitá filtros o verificá la sincronización en <strong>Sincronización</strong>. Esta funcionalidad es útil para gestionar inventarios extensos.</p>
                        </div>
                    </div>
                </div>
                <!-- Pregunta 3: Información disponible -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="listadoCompleto3">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseListadoCompleto3" aria-expanded="false" aria-controls="collapseListadoCompleto3">
                            <i class="fas fa-info-circle icon" aria-hidden="true"></i>
                            ¿Qué información puedo ver en el listado completo de MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapseListadoCompleto3" class="custom-accordion-collapse collapse" data-bs-parent="#listadoCompletoAccordion">
                        <div class="custom-accordion-body">
                            <p>La sección <strong>Listado completo</strong> muestra detalles de tus artículos sincronizados, incluyendo:</p>
                            <ul>
                                <li><strong>Título</strong>: Nombre de la publicación (por ejemplo, "Auriculares Sony").</li>
                                <li><strong>SKU interno</strong>: Código asignado (por ejemplo, "AUR456").</li>
                                <li><strong>Precio</strong>: Precio actual ($8,000).</li>
                                <li><strong>Stock</strong>: Cantidad disponible (20 unidades).</li>
                                <li><strong>Estado</strong>: Activo, pausado, finalizado.</li>
                                <li><strong>Condición</strong>: Nuevo, usado.</li>
                                <li><strong>Enlace</strong>: URL a MercadoLibre.</li>
                                <li><strong>Categoría</strong>: Por ejemplo, "Electrónica".</li>
                            </ul>
                            <p>Accedé desde el enlace. Asegura que los datos estén sincronizados en <strong>Sincronización</strong>. Esta sección te ayuda a gestionar tu inventario eficientemente.</p>
                        </div>
                    </div>
                </div>
                <!-- Pregunta 4: No veo artículos -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="listadoCompleto4">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseListadoCompleto4" aria-expanded="false" aria-controls="collapseListadoCompleto4">
                            <i class="fas fa-exclamation-triangle icon" aria-hidden="true"></i>
                            ¿Por qué no veo artículos en el listado completo de MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapseListadoCompleto4" class="custom-accordion-collapse collapse" data-bs-parent="#listadoCompletoAccordion">
                        <div class="custom-accordion-body">
                            <p>Si el listado está vacío, revisá estos puntos:</p>
                            <ul>
                                <li><strong>Sin cuenta vinculada</strong>: Verificá en <strong>Cuentas</strong>.</li>
                                <li><strong>Sincronización incompleta</strong>: Revisá <strong>Sincronización</strong> si está en curso.</li>
                                <li><strong>Filtros aplicados</strong>: Quitá clickeando en "Restablecer filtros".</li>
                                <li><strong>Sin publicaciones</strong>: Confirmá en MercadoLibre que tenés publicaciones activas.</li>
                            </ul>
                            <p>Por ejemplo, si "Smartphone Samsung" no aparece, asegurá que esté sincronizado. Intentá sincronizar nuevamente. Contactá a soporte@mldatatrends.com si el problema persiste.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección: SKU -->
            <h2 class="mt-5 mb-4" id="skuAccordion">SKU</h2>
            <div class="custom-accordion" id="skuAccordion">
                <!-- Pregunta 1: Ver SKU -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="sku1">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSku1" aria-expanded="false" aria-controls="collapseSku1">
                            <i class="fas fa-barcode icon" aria-hidden="true"></i>
                            ¿Cómo veo los SKU de mis artículos en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapseSku1" class="custom-accordion-collapse collapse" data-bs-parent="#skuAccordion">
                        <div class="custom-accordion-body">
                            <p>La sección <strong>SKU</strong> te muestra los códigos internos de tus artículos. Seguí estos pasos:</p>
                            <ol>
                                <li>Iniciá sesión en <a href="https://mldatatrends.com/login">MLDataTrends.com/login</a>.</li>
                                <li>Hacé clic en <strong>SKU</strong> en el enlace.</li>
                                <li>Verás una tabla con SKU (por ejemplo, "ZAP123"), título ("Zapatillas Nike Air"), precio, y stock.</li>
                                <li>Ordená o buscá un SKU específico.</li>
                            </ol>
                            <p>Asegura que tu cuenta esté vinculada en <strong>Cuentas</strong> y sincronizada en <strong>Sincronización</strong>. Si no ves SKU, revisá que tus productos tengan códigos asignados en MercadoLibre. Esta sección es ideal para controlar inventarios.</p>
                        </div>
                    </div>
                </div>
                <!-- Pregunta 2: Buscar por SKU -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="sku2">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSku2" aria-expanded="false" aria-controls="collapseSku2">
                            <i class="fas fa-search icon" aria-hidden="true"></i>
                            ¿Cómo busco un artículo por SKU en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapseSku2" class="custom-accordion-collapse collapse" data-bs-parent="#skuAccordion">
                        <div class="custom-accordion-body">
                            <p>Para localizar un artículo por SKU, seguí estos pasos:</p>
                            <ol>
                                <li>Navegá a <strong>SKU</strong> en el enlace.</li>
                                <li>Ingresá el SKU (por ejemplo, "SAM456") en el campo de búsqueda.</li>
                                <li>Hacé clic en la lupa o presioná Enter.</li>
                                <li>Verás el artículo con título, precio, y stock.</li>
                            </ol>
                            <p>Por ejemplo, buscá "SAM456" para encontrar un "Smartphone Samsung". Asegura que el SKU esté sincronizado en <strong>Sincronización</strong>. Si no aparece, verificá que el código sea correcto o que la cuenta esté vinculada. Contactá a soporte@mldatatrends.com si hay problemas.</p>
                        </div>
                    </div>
                </div>
                <!-- Pregunta 3: Actualizar SKU -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="sku3">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSku3" aria-expanded="false" aria-controls="collapseSku3">
                            <i class="fas fa-edit icon" aria-hidden="true"></i>
                            ¿Puedo actualizar los SKU de mis productos en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapseSku3" class="custom-accordion-collapse collapse" data-bs-parent="#skuAccordion">
                        <div class="custom-accordion-body">
                            <p>Podés actualizar los SKU internos para organizar tu inventario. Seguí estos pasos:</p>
                            <ol>
                                <li>Navegá a <strong>SKU</strong>.</li>
                                <li>Buscá el artículo (por ejemplo, "ZAP123").</li>
                                <li>Hacé clic en el artículo y seleccioná la opción de edición.</li>
                                <li>Ingresá el nuevo SKU (por ejemplo, "ZAP789") y guardá.</li>
                            </ol>
                            <p>Los cambios se aplican en MLDataTrends, pero no en MercadoLibre. Asegura que los SKU sean únicos. Si no ves la opción de edición, contactá a soporte@mldatatrends.com para verificar tu plan. Esta funcionalidad es útil para mantener tu inventario ordenado.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección: Ventas -->
            <h2 class="mt-5 mb-4" id="ventasAccordion">Ventas</h2>
            <div class="custom-accordion" id="ventasAccordion">
                <!-- Pregunta 1: Ver ventas -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="ventas1">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVentas1" aria-expanded="false" aria-controls="collapseVentas1">
                            <i class="fas fa-shopping-cart icon" aria-hidden="true"></i>
                            ¿Cómo veo mis ventas de MercadoLibre en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapseVentas1" class="custom-accordion-collapse collapse" data-bs-parent="#ventasAccordion">
                        <div class="custom-accordion-body">
                            <p>La sección <strong>Ventas</strong> muestra tus órdenes. Seguí estos pasos:</p>
                            <ol>
                                <li>Iniciá sesión en <a href="https://mldatatrends.com/login">MLDataTrends</a>.</li>
                                <li>Hacé clic en <strong>Ventas</strong> en el enlace.</li>
                                <li>Verás una tabla con órdenes, incluyendo ID (por ejemplo, "2000001234567890"), fecha ("2025-06-20"), producto ("Auriculares Sony"), y monto ($10,000).</li>
                                <li>Filtrá por fecha o estado ("Pagado").</li>
                            </ol>
                            <p>Asegura que tu cuenta esté vinculada y sincronizada en <strong>Sincronización</strong>. Si no ves ventas, revisá que haya compras recientes en MercadoLibre. Esta sección te ayuda a monitorear tus ingresos.</p>
                        </div>
                    </div>
                </div>
                <!-- Pregunta 2: Filtrar ventas -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="ventas2">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVentas2" aria-expanded="false" aria-controls="collapseVentas2">
                            <i class="fas fa-filter icon" aria-hidden="true"></i>
                            ¿Cómo filtro mis ventas por fecha o producto en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapseVentas2" class="custom-accordion-collapse collapse" data-bs-parent="#ventasAccordion">
                        <div class="custom-accordion-body">
                            <p>Filtrar ventas te ayuda a analizar datos específicos. Seguí estos pasos:</p>
                            <ol>
                                <li>Navegá a <strong>Ventas</strong>.</li>
                                <li>Selecciona un rango de fechas (por ejemplo, "2025-06-01 a 2025-06-25").</li>
                                <li>Ingresá un título o SKU (por ejemplo, "Zapatillas Nike" o "ZAP123").</li>
                                <li>Hacé clic en "Aplicar filtros".</li>
                            </ol>
                            <p>Por ejemplo, filtrá por "Zapatillas" en junio para ver esas ventas. Si no hay resultados, quitá filtros o verificá la sincronización en <strong>Sincronización</strong>. Asegura que tu cuenta esté vinculada. Esta funcionalidad es útil para informes mensuales.</p>
                        </div>
                    </div>
                </div>
                <!-- Pregunta 3: Problemas con ventas -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="ventas3">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVentas3" aria-expanded="false" aria-controls="collapseVentas3">
                            <i class="fas fa-exclamation-triangle icon" aria-hidden="true"></i>
                            ¿Por qué no veo mis ventas recientes en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapseVentas3" class="custom-accordion-collapse collapse" data-bs-parent="#ventasAccordion">
                        <div class="custom-accordion-body">
                            <p>Si no ves ventas recientes, revisá estos puntos:</p>
                            <ul>
                                <li><strong>Sincronización pendiente</strong>: Verificá en <strong>Sincronización</strong> si está completa.</li>
                                <li><strong>Cuenta no vinculada</strong>: Revisá el enlace <strong>Cuentas</strong> y revinculá si ves "Token expirado".</li>
                                <li><strong>Filtros aplicados</strong>: Quitá filtros clicando en "Restablecer filtros".</li>
                                <li><strong>Retraso</strong>: Intentá sincronizar tras 10 minutos.</li>
                            </ul>
                            <p>Por ejemplo, si no ves una venta de "Auriculares Sony", asegura su clic en clic. Contactá a soporte@mldatatrends.com si persiste.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección: Promociones -->
            <h2 class="mt-5 mb-4" id="promocionesAccordion">Promociones</h2>
            <div class="custom-accordion" id="promocionesAccordion">
                <!-- Pregunta 1: Ver promociones -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="promociones1">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePromociones1" aria-expanded="false" aria-controls="collapsePromociones1">
                            <i class="fas fa-tags icon" aria-hidden="true"></i>
                            ¿Cómo veo los productos en promoción en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapsePromociones1" class="custom-accordion-collapse collapse" data-bs-parent="#promocionesAccordion">
                        <div class="custom-accordion-body">
                            <p>La sección <strong>Promociones</strong> lista tus productos con descuentos. Seguí estos pasos:</p>
                            <ol>
                                <li>Iniciá sesión en <a href="https://mldatatrends.com/login">MLDataTrends</a>.</li>
                                <li>Hacé clic en <strong>Promociones</strong> en el enlace.</li>
                                <li>Verás productos como "Smart TV 50''" con descuentos (por ejemplo, 20% off) y precio promocional.</li>
                                <li>Filtrá por cuenta si tenés varias.</li>
                            </ol>
                            <p>Asegura que tu cuenta esté sincronizada en <strong>Sincronización</strong>. Si no ves promociones, verificá que tengas descuentos activos en MercadoLibre. Esta sección te ayuda a monitorear tus campañas.</p>
                        </div>
                    </div>
                </div>
                <!-- Pregunta 2: Filtrar promociones -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="promociones2">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePromociones2" aria-expanded="false" aria-controls="collapsePromociones2">
                            <i class="fas fa-filter icon" aria-hidden="true"></i>
                            ¿Cómo filtro productos en promoción por porcentaje de descuento?
                        </button>
                    </h3>
                    <div id="collapsePromociones2" class="custom-accordion-collapse collapse" data-bs-parent="#promocionesAccordion">
                        <div class="custom-accordion-body">
                            <p>Filtrar por porcentaje de descuento te enfoca en ofertas clave. Seguí estos pasos:</p>
                            <ol>
                                <li>Navegá a <strong>Promociones</strong>.</li>
                                <li>Selecciona un rango de descuento (por ejemplo, "Mayor a 15%").</li>
                                <li>Ingresá un título (por ejemplo, "Televisor") si querés.</li>
                                <li>Hacé clic en "Aplicar filtros".</li>
                            </ol>
                            <p>Por ejemplo, filtrá por "20% o más" para ver "Smart TV 25% off". Si no hay resultados, verificá la sincronización en <strong>Sincronización</strong>. Esta funcionalidad optimiza tus campañas.</p>
                        </div>
                    </div>
                </div>
                <!-- Pregunta 3: Problemas con promociones -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="promociones3">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePromociones3" aria-expanded="false" aria-controls="collapsePromociones3">
                            <i class="fas fa-exclamation-triangle icon" aria-hidden="true"></i>
                            ¿Por qué no veo mis promociones en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapsePromociones3" class="custom-accordion-collapse collapse" data-bs-parent="#promocionesAccordion">
                        <div class="custom-accordion-body">
                            <p>Si no ves promociones, revisá estos puntos:</p>
                            <ul>
                                <li><strong>Sin promociones activas</strong>: Confirmá en MercadoLibre que tenés descuentos.</li>
                                <li><strong>Sincronización incompleta</strong>: Verificá <strong>Sincronización</strong>.</li>
                                <li><strong>Cuenta no vinculada</strong>: Revisá <strong>Cuentas</strong>.</li>
                                <li><strong>Filtros aplicados</strong>: Quitá filtros clicando en "Restablecer filtros".</li>
                            </ul>
                            <p>Por ejemplo, si no ves un descuento en "Smart TV", asegura que esté activo. Intentá sincronizar nuevamente. Contactá a soporte@mldatatrends.com si persiste.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección: Catálogo -->
            <h2 class="mt-5 mb-4" id="catalogoAccordion">Catálogo</h2>
            <div class="custom-accordion" id="catalogoAccordion">
                <!-- Pregunta 1: Ver catálogo -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="catalogo1">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCatalogo1" aria-expanded="false" aria-controls="collapseCatalogo1">
                            <i class="fas fa-book icon" aria-hidden="true"></i>
                            ¿Cómo accedo al catálogo de mis productos en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapseCatalogo1" class="custom-accordion-collapse collapse" data-bs-parent="#catalogoAccordion">
                        <div class="custom-accordion-body">
                            <p>La sección <strong>Catálogo</strong> lista tus productos activos. Seguí estos pasos:</p>
                            <ol>
                                <li>Iniciá sesión en <a href="https://mldatatrends.com/login">MLDataTrends</a>.</li>
                                <li>Hacé clic en <strong>Catálogo</strong>.</li>
                                <li>Verás una tabla con ID (por ejemplo, "MLA123456789"), título ("Smartphone Samsung"), precio, stock, y cuenta.</li>
                                <li>Pagina o ajustá los resultados.</li>
                            </ol>
                            <p>Asegura que tu cuenta esté sincronizada en <strong>Sincronización</strong>. Si está vacía, revisá <strong>Cuentas</strong>. Esta sección es ideal para gestionar tu inventario.</p>
                        </div>
                    </div>
                </div>
                <!-- Pregunta 2: Filtrar catálogo -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="catalogo2">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCatalogo2" aria-expanded="false" aria-controls="collapseCatalogo2">
                            <i class="fas fa-filter icon" aria-hidden="true"></i>
                            ¿Cómo filtro productos en el catálogo por cuenta o búsqueda?
                        </button>
                    </h3>
                    <div id="collapseCatalogo2" class="custom-accordion-collapse collapse" data-bs-parent="#catalogoAccordion">
                        <div class="custom-accordion-body">
                            <p>Filtrar el catálogo te ayuda a encontrar productos. Seguí estos pasos:</p>
                            <ol>
                                <li>Navegá a <strong>Catálogo</strong>.</li>
                                <li>Selecciona una cuenta (por ejemplo, "TiendaEjemplo").</li>
                                <li>Ingresá una palabra clave (por ejemplo, "Zapatillas" o SKU "ZAP123").</li>
                                <li>Hacé clic en "Aplicar filtros".</li>
                            </ol>
                            <p>Por ejemplo, filtrá por "TiendaEjemplo" y "Zapatillas". Si no hay datos, quitá filtros o verificá <strong>Sincronización</strong>. Útil para múltiples cuentas.</p>
                        </div>
                    </div>
                </div>
                <!-- Pregunta 3: Analizar competencia -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="catalogo3">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCatalogo3" aria-expanded="false" aria-controls="collapseCatalogo3">
                            <i class="fas fa-chart-line icon" aria-hidden="true"></i>
                            ¿Cómo analizo la competencia de un producto en el catálogo?
                        </button>
                    </h3>
                    <div id="collapseCatalogo3" class="custom-accordion-collapse collapse" data-bs-parent="#catalogoAccordion">
                        <div class="custom-accordion-body">
                            <p>Analizar la competencia te da datos de otros vendedores. Seguí estos pasos:</p>
                            <ol>
                                <li>En <strong>Catálogo</strong>, buscá un producto (por ejemplo, "MLA987654321").</li>
                                <li>Hacé clic en el producto.</li>
                                <li>Selecciona una <strong>Ver competencia</strong>.</li>
                                <li>Verás precios y stock de competidores.</li>
                            </ol>
                            <p>Por ejemplo, analizá un "Smartphone Samsung" para comparar precios. Asegura que el producto esté sincronizado en el <strong>Sincronización</strong>. Si no carga, revisá los <strong>Cuentas</strong>. Contactá a soporte@mldatatrends.com si hay errores.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección: Estadísticas -->
            <h2 class="mt-5 mb-4" id="estadisticasAccordion">Estadísticas</h2>
            <div class="custom-accordion" id="estadisticasAccordion">
                <!-- Pregunta 1: Ver estadísticas -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="estadisticas1">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstadisticas1" aria-expanded="false" aria-controls="collapseEstadisticas1">
                            <i class="fas fa-chart-bar icon" aria-hidden="true"></i>
                            ¿Cómo accedo a las estadísticas de mi cuenta en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapseEstadisticas1" class="custom-accordion-collapse collapse" data-bs-parent="#estadisticasAccordion">
                        <div class="custom-accordion-body">
                            <p>La sección <strong>Estadísticas</strong> muestra métricas de tu desempeño. Seguí estos pasos:</p>
                            <ol>
                                <li>Iniciá sesión en <a href="https://mldatatrends.com/login">MLDataTrends</a>.</li>
                                <li>Hacé clic en <strong>Estadísticas</strong>.</li>
                                <li>Verás datos de stock total (por ejemplo, 500 unidades), ventas (por ejemplo, $100,000 en junio), y tasa de conversión.</li>
                                <li>Ajustá el rango de fechas.</li>
                            </ol>
                            <p>Asegura que tu cuenta esté sincronizada en <strong>Sincronización</strong>. Si no ves datos, revisá <strong>Cuentas</strong>. Esta sección te ayuda a tomar decisiones estratégicas.</p>
                        </div>
                    </div>
                </div>
                <!-- Pregunta 2: Stock crítico -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="estadisticas2">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEst␣estados2" aria-expanded="false" aria-controls="collapseEstadisticas2">
                            <i class="fas fa-exclamation-circle icon" aria-hidden="true"></i>
                            ¿Cómo identifico productos con stock crítico en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapseEstadisticas2" class="custom-accordion-collapse collapse" data-bs-parent="#estadisticasAccordion">
                        <div class="custom-accordion-body">
                            <p>Identificar stock crítico evita perder ventas. Seguí estos pasos:</p>
                            <ol>
                                <li>Navegá a <strong>Estadísticas</strong>.</li>
                                <li>Buscá <strong>Stock Crítico</strong>.</li>
                                <li>Verás productos con menos de 5 unidades (por ejemplo, "Auriculares Sony" con 3).</li>
                                <li>Hacé clic para reabastecer.</li>
                            </ol>
                            <p>Si no hay nada, puede que no tengas stock crítico o que no esté sincronizado. Verifica <strong>Sincronización</strong>. Esta funcionalidad mantiene tus publicaciones activas.</p>
                        </div>
                    </div>
                </div>
                <!-- Pregunta 3: Tasa de conversión -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="estadisticas3">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstadisticas3" aria-expanded="false" aria-controls="collapseEstadisticas3">
                            <i class="fas fa-list icon" aria-hidden="true"></i>
                            ¿Cómo veo la tasa de conversión de mis productos en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapseEstadisticas3" class="custom-accordion-collapse collapse" data-bs-parent="#estadisticasAccordion">
                        <div class="custom-accordion-body">
                            <p>La tasa de conversión mide visitas que se convierten en ventas. Seguí estos pasos:</p>
                            <ol>
                                <li>En <strong>Estadísticas</strong>, buscá <strong>Tasa de Conversión</strong>.</li>
                                <li>Selecciona un rango de fechas (por ejemplo, "2025-06-01 a 2025-06-25").</li>
                                <li>Verás tasas por producto (por ejemplo, "Zapatillas Nike: 5%").</li>
                                <li>Analizá tasas bajas para mejorar.</li>
                            </ol>
                            <p>Asegura que esté sincronizado en <strong>Sincronización</strong>. Si no ves tasas, revisá si hay ventas recientes. Contactá a soporte@mldatatrends.com si hay problemas.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección: Sincronización -->
            <h2 class="mt-5 mb-4" id="sincronizacionAccordion">Sincronización</h2>
            <div class="custom-accordion" id="sincronizacionAccordion">
                <!-- Pregunta 1: Iniciar sincronización -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="sincronizacion1">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSincronizacion1" aria-expanded="false" aria-controls="collapseSincronizacion1">
                            <i class="fas fa-sync icon" aria-hidden="true"></i>
                            ¿Cómo inicio la sincronización de datos con MercadoLibre en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapseSincronizacion1" class="custom-accordion-collapse collapse" data-bs-parent="#sincronizacionAccordion">
                        <div class="custom-accordion-body">
                            <p>Sincronizar datos actualiza tus publicaciones y ventas. Seguí estos pasos:</p>
                            <ol>
                                <li>Iniciá sesión en <a href="https://mldatatrends.com/login">MLDataTrends</a>.</li>
                                <li>Navegá a <strong>Sincronización</strong>.</li>
                                <li>Selecciona la cuenta (por ejemplo, "TiendaEjemplo").</li>
                                <li>Hacé clic en <strong>Iniciar sincronización</strong>.</li>
                            </ol>
                            <p>Espera a que termine (minutos si tenés muchas publicaciones). Verifica el estado en <strong>Sincronización</strong>. Si no inicia, revisá <strong>Cuentas</strong>. Esta funcionalidad es esencial para datos precisos.</p>
                        </div>
                    </div>
                </div>
                <!-- Pregunta 2: Ver estado -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="sincronizacion2">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSincronizacion2" aria-expanded="false" aria-controls="collapseSincronizacion2">
                            <i class="fas fa-info-circle icon" aria-hidden="true"></i>
                            ¿Cómo verifico el estado de la sincronización en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapseSincronizacion2" class="custom-accordion-collapse collapse" data-bs-parent="#sincronizacionAccordion">
                        <div class="custom-accordion-body">
                            <p>Verificar el estado asegura datos actualizados. Seguí estos pasos:</p>
                            <ol>
                                <li>Navegá a <strong>Sincronización</strong>.</li>
                                <li>Revisa la última sincronización: fecha (por ejemplo, "2025-06-25 10:30"), estado ("Completada"), y artículos sincronizados.</li>
                                <li>Buscá errores como "Límite de consultas alcanzado".</li>
                            </ol>
                            <p>Si está en curso, espera antes de usar otras secciones. Si hay errores, intenta sincronizar tras 10 minutos o revisá <strong>Cuentas</strong>. Contactá a soporte@mldatatrends.com si persiste.</p>
                        </div>
                    </div>
                </div>
                <!-- Pregunta 3: Problemas con sincronización -->
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="sincronizacion3">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSincronizacion3" aria-expanded="false" aria-controls="collapseSincronizacion3">
                            <i class="fas fa-exclamation-triangle icon" aria-hidden="true"></i>
                            ¿Por qué falla la sincronización de datos en MLDataTrends?
                        </button>
                    </h3>
                    <div id="collapseSincronizacion3" class="custom-accordion-collapse collapse" data-bs-parent="#sincronizacionAccordion">
                        <div class="custom-accordion-body">
                            <p>Si la sincronización falla, revisá estos puntos:</p>
                            <ul>
                                <li><strong>Token expirado</strong>: Revinculá en <strong>Cuentas</strong>.</li>
                                <li><strong>Límite de consultas</strong>: Espera 5-10 minutos y reintentá.</li>
                                <li><strong>Sin datos</strong>: Asegura que tu cuenta tenga publicaciones o ventas.</li>
                                <li><strong>Errores</strong>: Revisa <strong>Sincronización</strong> para mensajes.</li>
                            </ul>
                            <p>Por ejemplo, si ves "Error 429", intenta sincronizar más tarde. Contactá a soporte@mldatatrends.com con detalles si persiste. Una sincronización exitosa es clave.</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Comparaciones -->
            <h2 class="mt-5 mb-4" id="comparacionesAccordion">Comparaciones</h2>
            <div class="custom-accordion" id="comparacionesAccordion">
                <div class="custom-accordion-item">
                    <h3 class="custom-accordion-header" id="comparaciones1">
                        <button class="custom-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseComparaciones1" aria-expanded="false" aria-controls="collapseComparaciones1">
                            <i class="fas fa-balance-scale icon" aria-hidden="true"></i> ¿Cómo se compara MLDataTrends con herramientas como Nubimetrics?
                        </button>
                    </h3>
                    <div id="collapseComparaciones1" class="custom-accordion-collapse collapse" data-bs-parent="#comparacionesAccordion">
                        <div class="custom-accordion-body">
                            <p>MLDataTrends es una herramienta para MercadoLibre que simplifica la gestión de multicuentas, sincronización de artículos y análisis de ventas. A diferencia de plataformas como Nubimetrics o DataTrends, ofrecemos personalización de SKU local, análisis de competencia en tiempo real y soporte dedicado. <a href="{{ url('/plans') }}">Explorá nuestros planes</a> para potenciar tu estrategia.</p>
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
                    <p><i class="fas fa-envelope me-2" aria-hidden="true"></i>soporte@mldatatrends.com</p>
                </div>
            </div>
            <hr class="bg-light">
            <p class="text-center mb-0">© 2025 MLDataTrends. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script type="application/ld+json">
        {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [
            {
            "@type": "Question",
            "name": "¿Cómo vinculo mi cuenta de MercadoLibre a MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Vincular tu cuenta de MercadoLibre a MLDataTrends es el primer paso para gestionar tus ventas. Seguí estos pasos: 1) Iniciá sesión en MLDataTrends.com/login. 2) Navegá a Cuentas en el panel de control. 3) Hacé clic en Vincular cuenta de MercadoLibre. 4) Iniciá sesión en MercadoLibre y aceptá los permisos. 5) Una vez autorizado, verás tu cuenta listada."
            }
            },
            {
            "@type": "Question",
            "name": "¿Qué información puedo ver de mi cuenta de MercadoLibre en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "La sección Cuentas te muestra un resumen con Seller ID, nombre de la cuenta, estado, y última sincronización. Accedé desde el panel y verificá que esté vinculada correctamente."
            }
            },
            {
            "@type": "Question",
            "name": "¿Cómo sincronizo mis artículos de MercadoLibre con MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Sincroniza tus artículos así: 1) Iniciá sesión. 2) Selecciona la cuenta en Cuentas. 3) Hacé clic en Iniciar sincronización. 4) Esperá a que termine. Los datos aparecerán en Publicaciones y Listado completo."
            }
            },
            {
            "@type": "Question",
            "name": "¿Por qué no veo mis publicaciones después de sincronizar?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Si no ves publicaciones, revisá: Token expirado (revinculá), sin publicaciones activas, errores en Sincronización, o filtros aplicados. Intentá sincronizar nuevamente."
            }
            },
            {
            "@type": "Question",
            "name": "¿Cómo veo mis publicaciones de MercadoLibre en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "En Publicaciones, iniciá sesión, hacé clic en la sección, y verás una lista con título, precio, stock y estado. Asegurá que esté sincronizada."
            }
            },
            {
            "@type": "Question",
            "name": "¿Cómo busco una publicación específica por ID en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Ingresá el MLA ID en el campo de búsqueda en Publicaciones, hacé clic en la lupa, y verás los detalles. Asegura que esté sincronizada."
            }
            },
            {
            "@type": "Question",
            "name": "¿Cómo filtro mis publicaciones por estado o categoría en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Usá el panel de filtros en Publicaciones para seleccionar estado o categoría, y hacé clic en Aplicar filtros. Quitá si no hay resultados."
            }
            },
            {
            "@type": "Question",
            "name": "¿Por qué no veo todas mis publicaciones en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Revisá: cuenta no vinculada, sincronización incompleta, filtros aplicados, o sin publicaciones activas. Sincroniza nuevamente."
            }
            },
            {
            "@type": "Question",
            "name": "¿Cómo accedo al listado completo de mis artículos en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "En Listado completo, iniciá sesión, hacé clic en el enlace, y verás una tabla con título, SKU, precio y stock. Asegurá que esté sincronizado."
            }
            },
            {
            "@type": "Question",
            "name": "¿Cómo filtro mis artículos por título o SKU en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Ingresá título o SKU en el campo de búsqueda en Listado completo, seleccioná estado si querés, y aplicá filtros."
            }
            },
            {
            "@type": "Question",
            "name": "¿Qué información puedo ver en el listado completo de MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Incluye título, SKU, precio, stock, estado, condición, enlace y categoría. Accedé desde el enlace y verificá sincronización."
            }
            },
            {
            "@type": "Question",
            "name": "¿Por qué no veo artículos en el listado completo de MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Revisá: sin cuenta vinculada, sincronización incompleta, filtros aplicados, o sin publicaciones. Sincroniza nuevamente."
            }
            },
            {
            "@type": "Question",
            "name": "¿Cómo veo los SKU de mis artículos en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "En SKU, iniciá sesión, hacé clic en el enlace, y verás una tabla con SKU, título, precio y stock. Asegurá sincronización."
            }
            },
            {
            "@type": "Question",
            "name": "¿Cómo busco un artículo por SKU en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Ingresá el SKU en el campo de búsqueda en SKU, hacé clic en la lupa, y verás el artículo. Asegura que esté sincronizado."
            }
            },
            {
            "@type": "Question",
            "name": "¿Puedo actualizar los SKU de mis productos en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Sí, buscá el artículo en SKU, hacé clic en editar, ingresá el nuevo SKU y guardá. Los cambios son locales a MLDataTrends."
            }
            },
            {
            "@type": "Question",
            "name": "¿Cómo veo mis ventas de MercadoLibre en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "En Ventas, iniciá sesión, hacé clic en el enlace, y verás una tabla con ID, fecha, producto y monto. Filtrá si querés."
            }
            },
            {
            "@type": "Question",
            "name": "¿Cómo filtro mis ventas por fecha o producto en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Seleccioná un rango de fechas o ingresá un título/SKU en Ventas, y aplicá filtros para ver resultados específicos."
            }
            },
            {
            "@type": "Question",
            "name": "¿Por qué no veo mis ventas recientes en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Revisá: sincronización pendiente, cuenta no vinculada, filtros aplicados, o retraso. Sincroniza tras 10 minutos."
            }
            },
            {
            "@type": "Question",
            "name": "¿Cómo veo los productos en promoción en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "En Promociones, iniciá sesión, hacé clic en el enlace, y verás productos con descuentos. Filtrá por cuenta si querés."
            }
            },
            {
            "@type": "Question",
            "name": "¿Cómo filtro productos en promoción por porcentaje de descuento?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Seleccioná un rango de descuento en Promociones, ingresá un título si querés, y aplicá filtros."
            }
            },
            {
            "@type": "Question",
            "name": "¿Por qué no veo mis promociones en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Revisá: sin promociones activas, sincronización incompleta, cuenta no vinculada, o filtros aplicados. Sincroniza nuevamente."
            }
            },
            {
            "@type": "Question",
            "name": "¿Cómo accedo al catálogo de mis productos en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "En Catálogo, iniciá sesión, hacé clic en el enlace, y verás una tabla con ID, título, precio y stock."
            }
            },
            {
            "@type": "Question",
            "name": "¿Cómo filtro productos en el catálogo por cuenta o búsqueda?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Seleccioná una cuenta o ingresá una palabra clave en Catálogo, y aplicá filtros para ver resultados."
            }
            },
            {
            "@type": "Question",
            "name": "¿Cómo analizo la competencia de un producto en el catálogo?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Buscá un producto en Catálogo, hacé clic en Ver competencia, y verás precios y stock de competidores."
            }
            },
            {
            "@type": "Question",
            "name": "¿Cómo accedo a las estadísticas de mi cuenta en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "En Estadísticas, iniciá sesión, hacé clic en el enlace, y verás stock total, ventas y tasa de conversión. Ajustá fechas."
            }
            },
            {
            "@type": "Question",
            "name": "¿Cómo identifico productos con stock crítico en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "En Estadísticas, buscá Stock Crítico para ver productos con menos de 5 unidades y reabastecelos."
            }
            },
            {
            "@type": "Question",
            "name": "¿Cómo veo la tasa de conversión de mis productos en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "En Estadísticas, seleccioná un rango de fechas y verás tasas por producto. Analizá tasas bajas."
            }
            },
            {
            "@type": "Question",
            "name": "¿Cómo inicio la sincronización de datos con MercadoLibre en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "En Sincronización, seleccioná la cuenta y hacé clic en Iniciar sincronización. Esperá a que termine."
            }
            },
            {
            "@type": "Question",
            "name": "¿Cómo verifico el estado de la sincronización en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "En Sincronización, revisá la fecha, estado y errores de la última sincronización."
            }
            },
            {
            "@type": "Question",
            "name": "¿Por qué falla la sincronización de datos en MLDataTrends?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Revisá: token expirado, límite de consultas, sin datos, o errores. Reintentá tras 10 minutos."
            }
            },
             {
                "@type": "Question",
                "name": "¿Cómo se compara MLDataTrends con herramientas como Nubimetrics?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "MLDataTrends es una herramienta para MercadoLibre que simplifica la gestión de multicuentas, sincronización de artículos y análisis de ventas. A diferencia de plataformas como Nubimetrics o DataTrends, ofrecemos personalización de SKU local, análisis de competencia en tiempo real y soporte dedicado. Explorá nuestros planes para potenciar tu estrategia."
            }
            }
        ]
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="//code.tidio.co/jo26sf9h8f1m3xswvswb1eh3i7r1z3k9.js" async></script>
</body>
</html>
