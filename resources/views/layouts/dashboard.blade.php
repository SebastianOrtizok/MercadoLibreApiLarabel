<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
        /* Layout */
        .container-fluid {
            --bs-gutter-x: 0;
    --bs-gutter-y: 0;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
    width: auto;
    height: 100vh;
    background-color: #dee2e69c;
    transition: width 0.3s;
    position: relative;
}

        .sidebar.collapsed {
            width: 50px;
        }

        .sidebar h3 {
            text-align: center;
            padding: 20px;
            font-size: 20px;
            margin: 0;
            transition: opacity 0.3s;
        }

        .sidebar.collapsed h3 {
            opacity: 0;
        }

        .sidebar .toggle-btn {
            position: absolute;
            top: 10px;
            right: 5px;
            width: 20px;
            height: 20px;
            background-color: #ddd;
            border: 1px solid #ccc;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: transform 0.3s;
            /* transform: translateY(-50%); */
        }

        .sidebar .toggle-btn::after {
            content: "«";
            font-weight: bold;
            transform: translateY(-10%);
        }

        .sidebar.collapsed .toggle-btn::after {
            content: "»";
        }

        .sidebar ul {
            padding-left: 0;
            list-style: none;
            margin-top: 20px;
        }

        .sidebar ul li {
            display: flex;
            align-items: center;
            padding: 0px 10px 0px 10px;
        }


        .sidebar.collapsed ul li {
            padding: 0;
        }


        .sidebar ul li a {
            text-decoration: none;
            color: #333;
            font-size: 16px;
            transition: opacity 0.3s;
        }

        .sidebar ul li a .icon {
            font-size: 20px;
            margin-right: 10px;
        }

        .sidebar.collapsed ul li a .text {
            display: none;
        }

        .sidebar.collapsed ul li a .icon {
            margin-right: 0;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 20px;
        }
    </style>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container-fluid">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar">
            <h3>Admin Panel</h3>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.account') }}">
                        <span class="icon"><i class="fas fa-user"></i></span>
                        <span class="text">Mi cuenta</span>
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
                    <span class="icon"><i class="fas fa-clipboard-list"></i></span>
                        <span class="text">Ventas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.inventory') }}">
                        <span class="icon"><i class="fas fa-boxes"></i></span>
                        <span class="text">Inventario</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.analyze.low_conversion') }}">
                        <span class="icon"><i class="fas fa-chart-line"></i></span>
                        <span class="text">Bajas Ventas</span>
                    </a>
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
