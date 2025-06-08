<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.11/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/colreorder/1.7.0/js/dataTables.colReorder.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.11/css/jquery.dataTables.min.css">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script
        src="https://www.paypal.com/sdk/js?client-id=BAALYLUmqvjd-Wzz1IJHFFalfEM-MjIeCdSPEhNYTdQqKeiQF6JC4ml2XVNFFJDNFS-NvHTFQjyvkWTdN4&components=hosted-buttons&disable-funding=venmo¤cy=USD">
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Agregar meta tag para CSRF -->
    <title>Dashboard</title>
     @yield('head') <!-- Agregar esto para renderizar la sección head de las vistas hijas -->
</head>
<body>
    <div class="container-fluid p-0">
        <!-- Navbar para móviles (arriba, botón a la izquierda) -->
        <nav class="navbar navbar-light bg-light sticky-top d-lg-none">
            <div class="container-fluid">
                <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNav" aria-controls="mobileNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- Mostrar nombre de usuario y días restantes -->
                <div class="navbar-text ms-auto">
                    @if (Auth::check())
                        Hola, {{ Auth::user()->name }}!
                        @if (!Auth::user()->suscripcion)
                        <p><span class="text">No hay suscripción asociada.</span></p>
                        @elseif (in_array(Auth::user()->suscripcion->plan, ['test']))
                        <p><span class="text">Plan: {{ Auth::user()->suscripcion->plan }} (sin vencimiento).</span></p>
                        @elseif (Auth::user()->suscripcion->estado !== 'activo')
                        <p><span class="text">Estado: {{ Auth::user()->suscripcion->estado }} (no activo).</span></p>
                        @else
                            @php
                                $expirationDate = Auth::user()->suscripcion->fecha_fin;
                                $daysLeft = ceil(\Carbon\Carbon::now()->diffInDays($expirationDate, false));
                            @endphp
                            @if ($daysLeft >= 0)
                            <p><span class="text">Te quedan {{ $daysLeft }} {{ $daysLeft == 1 ? 'día' : 'días' }}.</span></p>
                            @else
                            <p><span class="text">Suscripción vencida.</span></p>
                            @endif
                        @endif
                    @endif
                </div>
                <div class="collapse navbar-collapse" id="mobileNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard.account') }}">
                                <i class="fas fa-user-tag me-2"></i> Cuentas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard.publications') }}">
                                <i class="fas fa-list me-2"></i> Publicaciones
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard.listado_articulos') }}">
                                <i class="fas fa-list me-2"></i> Listado completo
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard.sku') }}">
                                <span class="icon"><i class="fas fa-barcode"></i></span>
                                <span class="text">SKU</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard.ventas') }}">
                                <i class="fas fa-dollar-sign me-2"></i> Ventas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard.ventasconsolidadasdb') }}">
                                <i class="fas fa-chart-line me-2"></i> Ventas ConsolidadasDB
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard.promociones') }}">
                                <i class="fas fa-boxes me-2"></i> Promociones de ML
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard.item_promotions') }}">
                                <i class="fas fa-ticket-alt"></i> Items en Promoción
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard.catalogo') }}">
                                <span class="icon"><i class="fas fa-tags"></i></span>
                                <span class="text">Catálogo</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard.estadisticas') }}">
                                <span class="icon"><i class="fas fa-chart-line"></i></span>
                                <span class="text">Estadísticas</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('competidores.index') }}">
                                <span class="icon"><i class="fas fa-user-secret"></i></span>
                                <span class="text">Competencia</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('sincronizacion.index') }}">
                                <i class="fas fa-database me-2"></i> Sincronización
                            </a>
                        </li>
                        @if (Auth::check() && Auth::user()->is_admin)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-user-shield me-2"></i> Admin
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Sidebar para escritorio -->
        <div id="sidebar" class="sidebar d-none d-lg-block">
            <!-- Mostrar nombre de usuario y días restantes -->

            <h3 class="sidebar-title">Menu</h3>
            <div class="sidebar-header">
                @if (Auth::check())
                    <h5><span class="userlog">Hola, {{ Auth::user()->name }}!</span></h5>
                    @if (!Auth::user()->suscripcion)
                    <p><span class="userlog">No hay suscripción asociada.</span></p>
                    @elseif (in_array(Auth::user()->suscripcion->plan, ['test']))
                    <p><span class="userlog">Plan: {{ Auth::user()->suscripcion->plan }} (sin vencimiento).</span></p>
                    @elseif (Auth::user()->suscripcion->estado !== 'activo')
                    <p><span class="userlog">Estado: {{ Auth::user()->suscripcion->estado }} (no activo).</span></p>
                    @else
                        @php
                            $expirationDate = Auth::user()->suscripcion->fecha_fin;
                            $daysLeft = ceil(\Carbon\Carbon::now()->diffInDays($expirationDate, false));
                        @endphp
                        @if ($daysLeft >= 0)
                        <p><span class="userlog">Te quedan {{ $daysLeft }} {{ $daysLeft == 1 ? 'día' : 'días' }}.</span></p>
                        @else
                        <p><span class="userlog">Suscripción vencida.</span></p>
                        @endif
                    @endif
                @endif
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.account') }}">
                        <span class="icon"><i class="fas fa-user-tag"></i></span>
                        <span class="text">Cuentas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.publications') }}">
                        <span class="icon"><i class="fas fa-list"></i></span>
                        <span class="text">Publicaciones</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.listado_articulos') }}">
                        <i class="fas fa-list me-2"></i><span class="text">Listado completo</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.sku') }}">
                        <span class="icon"><i class="fas fa-barcode"></i></span>
                        <span class="text">SKU</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.ventas') }}">
                        <span class="icon"><i class="fas fa-dollar-sign"></i></span>
                        <span class="text">Ventas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.ventasconsolidadasdb') }}">
                        <span class="icon"><i class="fas fa-chart-line"></i></span>
                        <span class="text">Ventas Consolidadas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.promociones') }}">
                        <span class="icon"><i class="fas fa-boxes"></i></span>
                        <span class="text">Promociones ML</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.item_promotions') }}">
                        <span class="icon"><i class="fas fa-ticket-alt"></i></span>
                        <span class="text">Items Promo</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.catalogo') }}">
                        <span class="icon"><i class="fas fa-tags"></i></span>
                        <span class="text">Catálogo</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.estadisticas') }}">
                        <span class="icon"><i class="fas fa-chart-line"></i></span>
                        <span class="text">Estadísticas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('competidores.index') }}">
                        <span class="icon"><i class="fas fa-user-secret"></i></span>
                        <span class="text">Competencia</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('sincronizacion.index') }}">
                        <span class="icon"><i class="fas fa-database"></i></span>
                        <span class="text">Sincronización</span>
                    </a>
                </li>
                @if (Auth::check() && Auth::user()->is_admin)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-user-shield me-2"></i> Admin
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link logout-button">
                        <span class="icon"><i class="fas fa-sign-out-alt"></i></span>
                        <span class="text">Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
            <div class="toggle-btn"></div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div>
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        // Script para manejar la barra lateral
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = sidebar.querySelector('.toggle-btn');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
        });
    </script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <!-- ColReorder para mover columnas -->
    <script src="https://cdn.datatables.net/colreorder/1.5.0/js/dataTables.colReorder.min.js"></script>

    @yield('scripts') <!-- Agregar esto para renderizar scripts de las vistas hijas -->
</body>
</html>
