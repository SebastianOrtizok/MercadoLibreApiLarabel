<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar">
            <h3>Menu</h3>
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
                        <span class="text">Mis Publicaciones</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.ventas') }}">
                    <span class="icon"><i class="fas fa-dollar-sign"></i></span>
                        <span class="text">Ventas</span>
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.inventory') }}">
                        <span class="icon"><i class="fas fa-boxes"></i></span>
                        <span class="text">Inventario</span>
                    </a>
                </li> -->
                <!-- <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.itemVenta') }}">
                        <span class="icon"><i class="fas fa-boxes"></i></span>
                        <span class="text">Item Venta</span>
                    </a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('sincronizacion.index') }}">
                        <span class="icon"><i class="fas fa-database"></i></span>
                        <span class="text">Sincronización</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="logout-button">
                        <i class="fas fa-sign-out-alt"></i> <!-- Ícono -->
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
