@extends('layouts.dashboard')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-primary fw-bold">Gestión de Competidores</h2>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-4">
        <button class="btn btn-outline-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#formCollapse" aria-expanded="false" aria-controls="formCollapse">
            <i class="fas fa-plus me-2"></i> Agregar Nuevo Competidor
        </button>
        <div class="collapse mt-3" id="formCollapse">
            <div class="card shadow-sm p-4 bg-light rounded">
                <form action="{{ route('competidores.store') }}" method="POST" id="competidor-form">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="official_store_id">Official Store ID (opcional)</label>
                                <input type="number" name="official_store_id" id="official_store_id" class="form-control" value="{{ old('official_store_id') }}">
                            </div>
                            <label for="seller_id" class="form-label fw-semibold">Seller ID</label>
                            <input type="text" name="seller_id" id="seller_id" class="form-control" placeholder="Ej: 179571326" required readonly>
                            @error('seller_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="nickname" class="form-label fw-semibold">Nickname</label>
                            <div class="input-group">
                                <input type="text" name="nickname" id="nickname" class="form-control" placeholder="Ej: TESTACCOUNT" required>
                                <button type="button" class="btn btn-outline-secondary" id="find-seller-id">
                                    <i class="fas fa-search me-2"></i> Buscar Seller ID
                                </button>
                            </div>
                            @error('nickname')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <div id="seller-id-error" class="text-danger mt-1" style="display: none;"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="nombre" class="form-label fw-semibold">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ej: Competidor de Prueba" required>
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="categoria" class="form-label fw-semibold">Categoría (opcional)</label>
<select name="categoria" id="categoria" class="form-control">
                                <option value="">Seleccionar categoría a escrapear</option>
                                <optgroup label="Vehículos">
                                    <option value="accesorios-para-vehiculos">Accesorios para Vehículos</option>
                                    <option value="repuestos-autos-camionetas">Repuestos para Autos y Camionetas</option>
                                    <option value="motos">Motos</option>
                                    <option value="neumaticos">Neumáticos</option>
                                    <option value="llantas">Llantas</option>
                                    <option value="otros-vehiculos">Otros Vehículos</option>
                                </optgroup>
                                <optgroup label="Inmuebles">
                                    <option value="alquiler">Alquiler</option>
                                    <option value="venta">Venta</option>
                                    <option value="temporarios">Alquiler Temporario</option>
                                    <option value="locales">Locales Comerciales</option>
                                    <option value="departamentos">Departamentos</option>
                                    <option value="casas">Casas</option>
                                    <option value="terrenos">Terrenos</option>
                                </optgroup>
                                <optgroup label="Supermercado">
                                    <option value="almacen">Almacén</option>
                                    <option value="bebidas">Bebidas</option>
                                    <option value="cuidado-personal">Cuidado Personal</option>
                                    <option value="limpieza">Limpieza</option>
                                    <option value="frescos">Frescos</option>
                                    <option value="congelados">Congelados</option>
                                </optgroup>
                                <optgroup label="Tecnología">
                                    <option value="celulares-y-telefonos">Celulares y Teléfonos</option>
                                    <option value="computacion">Computación</option>
                                    <option value="electronica-audio-video">Electrónica, Audio y Video</option>
                                    <option value="consolas-videojuegos">Consolas y Videojuegos</option>
                                    <option value="impresion">Impresión</option>
                                    <option value="conectividad-redes">Conectividad y Redes</option>
                                </optgroup>
                                <optgroup label="Hogar y Muebles">
                                    <option value="hogar-muebles">Hogar y Muebles</option>
                                    <option value="cocina">Cocina</option>
                                    <option value="decoracion">Decoración</option>
                                    <option value="iluminacion">Iluminación</option>
                                    <option value="textiles">Textiles</option>
                                    <option value="bazar">Bazar</option>
                                </optgroup>
                                <optgroup label="Electrodomésticos y Aires Ac.">
                                    <option value="electrodomesticos">Electrodomésticos</option>
                                    <option value="aires-acondicionados">Aires Acondicionados</option>
                                    <option value="cocinas">Cocinas</option>
                                    <option value="lavado">Lavado</option>
                                    <option value="climatizacion">Climatización</option>
                                </optgroup>
                                <optgroup label="Deportes y Fitness">
                                    <option value="deportes-fitness">Deportes y Fitness</option>
                                    <option value="bicicletas">Bicicletas</option>
                                    <option value="fitness">Fitness</option>
                                    <option value="futbol">Fútbol</option>
                                    <option value="tenis">Tenis</option>
                                    <option value="camping">Camping</option>
                                </optgroup>
                                <optgroup label="Belleza y Cuidado Personal">
                                    <option value="belleza-cuidado-personal">Belleza y Cuidado Personal</option>
                                    <option value="maquillaje">Maquillaje</option>
                                    <option value="cuidado-del-cabello">Cuidado del Cabello</option>
                                    <option value="cuidado-de-la-piel">Cuidado de la Piel</option>
                                    <option value="fragancias">Fragancias</option>
                                </optgroup>
                                <optgroup label="Herramientas">
                                    <option value="herramientas">Herramientas</option>
                                    <option value="herramientas-electricas">Herramientas Eléctricas</option>
                                    <option value="herramientas-manuales">Herramientas Manuales</option>
                                    <option value="soldadura">Soldadura</option>
                                    <option value="medicion">Medición</option>
                                </optgroup>
                                <optgroup label="Construcción">
                                    <option value="construccion">Construcción</option>
                                    <option value="electricidad">Electricidad</option>
                                    <option value="pintureria">Pinturería</option>
                                    <option value="plomería">Plomería</option>
                                    <option value="ferreteria">Ferretería</option>
                                </optgroup>
                                <optgroup label="Industrias y Oficinas">
                                    <option value="industrias-oficinas">Industrias y Oficinas</option>
                                    <option value="equipamiento-comercial">Equipamiento Comercial</option>
                                    <option value="gastronomia">Gastronomía</option>
                                    <option value="oficina">Oficina</option>
                                </optgroup>
                                <optgroup label="Accesorios para Vehículos">
                                    <option value="accesorios-para-vehiculos">Accesorios para Vehículos</option>
                                    <option value="audio-para-vehiculos">Audio para Vehículos</option>
                                    <option value="cuidado-vehiculo">Cuidado del Vehículo</option>
                                    <option value="accesorios-interiores">Accesorios Interiores</option>
                                    <option value="accesorios-exteriores">Accesorios Exteriores</option>
                                </optgroup>
                                <optgroup label="Agro">
                                    <option value="agro">Agro</option>
                                    <option value="maquinaria-agricola">Maquinaria Agrícola</option>
                                    <option value="insumos-agricolas">Insumos Agrícolas</option>
                                    <option value="ganaderia">Ganadería</option>
                                </optgroup>
                                <optgroup label="Animales y Mascotas">
                                    <option value="animales-mascotas">Animales y Mascotas</option>
                                    <option value="perros">Perros</option>
                                    <option value="gatos">Gatos</option>
                                    <option value="peces">Peces</option>
                                    <option value="aves">Aves</option>
                                    <option value="roedores">Roedores</option>
                                </optgroup>
                                <optgroup label="Antiguedades y Colecciones">
                                    <option value="antiguedades-colecciones">Antigüedades y Colecciones</option>
                                    <option value="antiguedades">Antigüedades</option>
                                    <option value="monedas-billetes">Monedas y Billetes</option>
                                    <option value="sellos">Sellos</option>
                                    <option value="arte-coleccionable">Arte Coleccionable</option>
                                </optgroup>
                                <optgroup label="Arte, Librería y Mercería">
                                    <option value="arte-libreria-merceria">Arte, Librería y Mercería</option>
                                    <option value="arte">Arte</option>
                                    <option value="libreria">Librería</option>
                                    <option value="merceria">Mercería</option>
                                </optgroup>
                                <optgroup label="Autos, Motos y Otros">
                                    <option value="autos-motos-otros">Autos, Motos y Otros</option>
                                    <option value="autos-usados">Autos Usados</option>
                                    <option value="motos-usadas">Motos Usadas</option>
                                    <option value="otros-vehiculos">Otros Vehículos</option>
                                </optgroup>
                                <optgroup label="Bebés">
                                    <option value="bebes">Bebés</option>
                                    <option value="coches-bebe">Coches de Bebé</option>
                                    <option value="cunas-corralitos">Cunas y Corralitos</option>
                                    <option value="juguetes-bebe">Juguetes para Bebé</option>
                                    <option value="ropa-bebe">Ropa para Bebé</option>
                                </optgroup>
                                <optgroup label="Cámaras y Accesorios">
                                    <option value="camaras-accesorios">Cámaras y Accesorios</option>
                                    <option value="camaras-digitales">Cámaras Digitales</option>
                                    <option value="drones-accesorios">Drones y Accesorios</option>
                                    <option value="lentes">Lentes</option>
                                    <option value="tripodes">Trípodes</option>
                                </optgroup>
                                <optgroup label="Celulares y Teléfonos">
                                    <option value="celulares-telefonos">Celulares y Teléfonos</option>
                                    <option value="celulares">Celulares</option>
                                    <option value="telefonos-fijos">Teléfonos Fijos</option>
                                    <option value="repuestos-celulares">Repuestos para Celulares</option>
                                </optgroup>
                                <optgroup label="Coleccionables y Hobbies">
                                    <option value="coleccionables-hobbies">Coleccionables y Hobbies</option>
                                    <option value="hobbies">Hobbies</option>
                                    <option value="figuras-accion">Figuras de Acción</option>
                                    <option value="modelismo">Modelismo</option>
                                </optgroup>
                                <optgroup label="Consolas y Videojuegos">
                                    <option value="consolas-videojuegos">Consolas y Videojuegos</option>
                                    <option value="consolas">Consolas</option>
                                    <option value="videojuegos">Videojuegos</option>
                                    <option value="accesorios-consolas">Accesorios para Consolas</option>
                                </optgroup>
                                <optgroup label="Deportes y Fitness">
                                    <option value="deportes-fitness">Deportes y Fitness</option>
                                    <option value="bicicletas">Bicicletas</option>
                                    <option value="fitness-gimnasia">Fitness y Gimnasia</option>
                                    <option value="futbol">Fútbol</option>
                                    <option value="tenis">Tenis</option>
                                    <option value="camping">Camping</option>
                                </optgroup>
                                <optgroup label="Electrodomésticos y Aires Ac.">
                                    <option value="electrodomesticos-aires-ac">Electrodomésticos y Aires Ac.</option>
                                    <option value="aires-acondicionados">Aires Acondicionados</option>
                                    <option value="cocinas">Cocinas</option>
                                    <option value="lavado">Lavado</option>
                                    <option value="climatizacion">Climatización</option>
                                </optgroup>
                                <optgroup label="Electrónica, Audio y Video">
                                    <option value="electronica-audio-video">Electrónica, Audio y Video</option>
                                    <option value="audio">Audio</option>
                                    <option value="audifonos">Audífonos</option>
                                    <option value="componentes-electronicos">Componentes Electrónicos</option>
                                    <option value="drones-accesorios">Drones y Accesorios</option>
                                    <option value="equipamiento-djs">Equipamiento para DJs</option>
                                    <option value="home-theater">Home Theater</option>
                                    <option value="instrumentos-musicales-electronicos">Instrumentos Musicales Electrónicos</option>
                                    <option value="parlantes">Parlantes</option>
                                    <option value="radios">Radios</option>
                                    <option value="televisores">Televisores</option>
                                    <option value="video">Video</option>
                                </optgroup>
                                <optgroup label="Hogar, Muebles y Jardín">
                                    <option value="hogar-muebles-jardin">Hogar, Muebles y Jardín</option>
                                    <option value="cocina">Cocina</option>
                                    <option value="decoracion">Decoración</option>
                                    <option value="iluminacion">Iluminación</option>
                                    <option value="textiles">Textiles</option>
                                    <option value="bazar">Bazar</option>
                                </optgroup>
                                <optgroup label="Industrias y Oficinas">
                                    <option value="industrias-oficinas">Industrias y Oficinas</option>
                                    <option value="equipamiento-comercial">Equipamiento Comercial</option>
                                    <option value="gastronomia">Gastronomía</option>
                                    <option value="oficina">Oficina</option>
                                </optgroup>
                                <optgroup label="Inmuebles">
                                    <option value="inmuebles">Inmuebles</option>
                                    <option value="alquiler">Alquiler</option>
                                    <option value="venta">Venta</option>
                                    <option value="temporarios">Alquiler Temporario</option>
                                    <option value="locales">Locales Comerciales</option>
                                    <option value="departamentos">Departamentos</option>
                                    <option value="casas">Casas</option>
                                    <option value="terrenos">Terrenos</option>
                                </optgroup>
                                <optgroup label="Instrumentos Musicales">
                                    <option value="instrumentos-musicales">Instrumentos Musicales</option>
                                    <option value="guitarras">Guitarras</option>
                                    <option value="teclados-pianos">Teclados y Pianos</option>
                                    <option value="baterias">Baterías</option>
                                    <option value="viento">Instrumentos de Viento</option>
                                    <option value="cuerdas">Instrumentos de Cuerdas</option>
                                </optgroup>
                                <optgroup label="Joyas y Relojes">
                                    <option value="joyas-relojes">Joyas y Relojes</option>
                                    <option value="relojes">Relojes</option>
                                    <option value="joyas">Joyas</option>
                                </optgroup>
                                <optgroup label="Juegos y Juguetes">
                                    <option value="juegos-juguetes">Juegos y Juguetes</option>
                                    <option value="juguetes">Juguetes</option>
                                    <option value="juegos-mesa">Juegos de Mesa</option>
                                    <option value="figuras-accion">Figuras de Acción</option>
                                </optgroup>
                                <optgroup label="Libros, Revistas y Comics">
                                    <option value="libros-revistas-comics">Libros, Revistas y Comics</option>
                                    <option value="libros">Libros</option>
                                    <option value="revistas">Revistas</option>
                                    <option value="comics">Comics</option>
                                </optgroup>
                                <optgroup label="Música, Películas y Series">
                                    <option value="musica-peliculas-series">Música, Películas y Series</option>
                                    <option value="musica">Música</option>
                                    <option value="peliculas">Películas</option>
                                    <option value="series">Series</option>
                                </optgroup>
                                <optgroup label="Ropa y Accesorios">
                                    <option value="ropa-accesorios">Ropa y Accesorios</option>
                                    <option value="ropa-mujer">Ropa Mujer</option>
                                    <option value="ropa-hombre">Ropa Hombre</option>
                                    <option value="zapatillas">Zapatillas</option>
                                    <option value="accesorios-moda">Accesorios de Moda</option>
                                </optgroup>
                                <optgroup label="Salud y Equipamiento Médico">
                                    <option value="salud-equipamiento-medico">Salud y Equipamiento Médico</option>
                                    <option value="cuidado-salud">Cuidado de la Salud</option>
                                    <option value="equipamiento-medico">Equipamiento Médico</option>
                                </optgroup>
                                <optgroup label="Souvenirs, Cotillón y Fiestas">
                                    <option value="souvenirs-cotillon-fiestas">Souvenirs, Cotillón y Fiestas</option>
                                    <option value="cotillon">Cotillón</option>
                                    <option value="souvenirs">Souvenirs</option>
                                    <option value="fiestas">Fiestas</option>
                                </optgroup>
                                <optgroup label="Otras categorías">
                                    <option value="otras-categorias">Otras Categorías</option>
                                </optgroup>
                            </select>
                            @error('categoria')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i> Guardar Competidor
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="table-responsive mb-5">
        <table class="table table-hover modern-table shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Seller ID</th>
                    <th scope="col">Nickname</th>
                    <th scope="col">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($competidores as $competidor)
                    <tr>
                        <td>{{ $competidor->nombre }}</td>
                        <td>{{ $competidor->seller_id }}</td>
                        <td>{{ $competidor->nickname }}</td>
                        <td>
                            <form action="{{ route('competidores.actualizar') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="competidor_id" value="{{ $competidor->id }}">
                                <select name="categoria" class="form-control form-control-sm mb-2" style="width: auto; display: inline-block;">
                                    <option value="">Seleccionar categoría a escrapear</option>
                                    <option value="vehiculos" {{ $competidor->categoria == 'vehiculos' ? 'selected' : '' }}>Vehículos</option>
                                    <option value="inmuebles" {{ $competidor->categoria == 'inmuebles' ? 'selected' : '' }}>Inmuebles</option>
                                    <option value="supermercado" {{ $competidor->categoria == 'supermercado' ? 'selected' : '' }}>Supermercado</option>
                                    <option value="tecnologia" {{ $competidor->categoria == 'tecnologia' ? 'selected' : '' }}>Tecnología</option>
                                    <option value="hogar-muebles-jardin" {{ $competidor->categoria == 'hogar-muebles-jardin' ? 'selected' : '' }}>Hogar, Muebles y Jardín</option>
                                    <option value="electrodomesticos-aires-ac" {{ $competidor->categoria == 'electrodomesticos-aires-ac' ? 'selected' : '' }}>Electrodomésticos y Aires Ac.</option>
                                    <option value="deportes-fitness" {{ $competidor->categoria == 'deportes-fitness' ? 'selected' : '' }}>Deportes y Fitness</option>
                                    <option value="belleza-cuidado-personal" {{ $competidor->categoria == 'belleza-cuidado-personal' ? 'selected' : '' }}>Belleza y Cuidado Personal</option>
                                    <option value="herramientas" {{ $competidor->categoria == 'herramientas' ? 'selected' : '' }}>Herramientas</option>
                                    <option value="construccion" {{ $competidor->categoria == 'construccion' ? 'selected' : '' }}>Construcción</option>
                                    <option value="industrias-oficinas" {{ $competidor->categoria == 'industrias-oficinas' ? 'selected' : '' }}>Industrias y Oficinas</option>
                                    <option value="accesorios-para-vehiculos" {{ $competidor->categoria == 'accesorios-para-vehiculos' ? 'selected' : '' }}>Accesorios para Vehículos</option>
                                    <option value="agro" {{ $competidor->categoria == 'agro' ? 'selected' : '' }}>Agro</option>
                                    <option value="animales-mascotas" {{ $competidor->categoria == 'animales-mascotas' ? 'selected' : '' }}>Animales y Mascotas</option>
                                    <option value="antiguedades-colecciones" {{ $competidor->categoria == 'antiguedades-colecciones' ? 'selected' : '' }}>Antigüedades y Colecciones</option>
                                    <option value="arte-libreria-merceria" {{ $competidor->categoria == 'arte-libreria-merceria' ? 'selected' : '' }}>Arte, Librería y Mercería</option>
                                    <option value="autos-motos-otros" {{ $competidor->categoria == 'autos-motos-otros' ? 'selected' : '' }}>Autos, Motos y Otros</option>
                                    <option value="bebes" {{ $competidor->categoria == 'bebes' ? 'selected' : '' }}>Bebés</option>
                                    <option value="camaras-accesorios" {{ $competidor->categoria == 'camaras-accesorios' ? 'selected' : '' }}>Cámaras y Accesorios</option>
                                    <option value="celulares-telefonos" {{ $competidor->categoria == 'celulares-telefonos' ? 'selected' : '' }}>Celulares y Teléfonos</option>
                                    <option value="coleccionables-hobbies" {{ $competidor->categoria == 'coleccionables-hobbies' ? 'selected' : '' }}>Coleccionables y Hobbies</option>
                                    <option value="consolas-videojuegos" {{ $competidor->categoria == 'consolas-videojuegos' ? 'selected' : '' }}>Consolas y Videojuegos</option>
                                    <option value="deportes-fitness" {{ $competidor->categoria == 'deportes-fitness' ? 'selected' : '' }}>Deportes y Fitness</option>
                                    <option value="electrodomesticos-aires-ac" {{ $competidor->categoria == 'electrodomesticos-aires-ac' ? 'selected' : '' }}>Electrodomésticos y Aires Ac.</option>
                                    <option value="electronica-audio-video" {{ $competidor->categoria == 'electronica-audio-video' ? 'selected' : '' }}>Electrónica, Audio y Video</option>
                                    <option value="hogar-muebles-jardin" {{ $competidor->categoria == 'hogar-muebles-jardin' ? 'selected' : '' }}>Hogar, Muebles y Jardín</option>
                                    <option value="industrias-oficinas" {{ $competidor->categoria == 'industrias-oficinas' ? 'selected' : '' }}>Industrias y Oficinas</option>
                                    <option value="inmuebles" {{ $competidor->categoria == 'inmuebles' ? 'selected' : '' }}>Inmuebles</option>
                                    <option value="instrumentos-musicales" {{ $competidor->categoria == 'instrumentos-musicales' ? 'selected' : '' }}>Instrumentos Musicales</option>
                                    <option value="joyas-relojes" {{ $competidor->categoria == 'joyas-relojes' ? 'selected' : '' }}>Joyas y Relojes</option>
                                    <option value="juegos-juguetes" {{ $competidor->categoria == 'juegos-juguetes' ? 'selected' : '' }}>Juegos y Juguetes</option>
                                    <option value="libros-revistas-comics" {{ $competidor->categoria == 'libros-revistas-comics' ? 'selected' : '' }}>Libros, Revistas y Comics</option>
                                    <option value="musica-peliculas-series">Música, Películas y Series</option>
                                    <option value="ropa-accesorios">Ropa y Accesorios</option>
                                    <option value="salud-equipamiento-medico">Salud y Equipamiento Médico</option>
                                    <option value="souvenirs-cotillon-fiestas">Souvenirs, Cotillón y Fiestas</option>
                                    <option value="otras-categorias">Otras Categorías</option>
                                </select>
                                <button type="submit" class="btn btn-outline-success btn-sm ms-2">
                                    <i class="fas fa-sync-alt me-2"></i> Actualizar
                                </button>
                            </form>
                            <form action="{{ route('competidores.destroy') }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que querés eliminar este competidor?');">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="competidor_id" value="{{ $competidor->id }}">
                                <button type="submit" class="btn btn-outline-danger btn-sm ms-2">
                                    <i class="fas fa-trash-alt me-2"></i> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            <i class="fas fa-exclamation-circle me-2"></i> No hay competidores registrados aún.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h3 class="mb-4 text-primary fw-bold">Publicaciones Descargadas</h3>

    <!-- Formulario de filtros colapsado -->
   <div class="mb-4 mt-5">
        <button class="btn btn-outline-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosCollapse" aria-expanded="false" aria-controls="filtrosCollapse">
            <i class="fas fa-filter"></i> <span id="toggleText">Mostrar Filtros</span>
        </button>
        <div class="collapse" id="filtrosCollapse">
            <form method="GET" action="{{ route('competidores.index') }}" class="mt-3">
                <div class="filtros-container p-3 bg-light rounded shadow-sm">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label>Nickname</label>
                            <input type="text" name="nickname" class="form-control" placeholder="Buscar por nickname" value="{{ request('nickname') }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Título</label>
                            <input type="text" name="titulo" class="form-control" placeholder="Buscar por título" value="{{ request('titulo') }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Categorías</label>
                            <input type="text" name="categorias" class="form-control" placeholder="Buscar por categorías" value="{{ request('categorias') }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            <label>Es Full</label>
                            <select name="es_full" class="form-control">
                                <option value="">Todos</option>
                                <option value="1" {{ request('es_full') == '1' ? 'selected' : '' }}>Sí</option>
                                <option value="0" {{ request('es_full') == '0' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label>Following</label>
                            <select name="following" class="form-control">
                                <option value="">Todos</option>
                                <option value="1" {{ request('following') == '1' ? 'selected' : '' }}>Sí</option>
                                <option value="0" {{ request('following') == '0' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label>Ordenar por</label>
                            <select name="order_by" class="form-control">
                                <option value="">Sin orden</option>
                                <option value="precio" {{ request('order_by') == 'precio' ? 'selected' : '' }}>Precio Original</option>
                                <option value="precio_descuento" {{ request('order_by') == 'precio_descuento' ? 'selected' : '' }}>Precio con Descuento</option>
                                <option value="ultima_actualizacion" {{ request('ultima_actualizacion') == 'ultima_actualizacion' ? 'selected' : '' }}>Última Actualización</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label>Dirección</label>
                            <select name="direction" class="form-control">
                                <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                                <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descendente</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('export.items-competidores') }}" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i> Exportar a Excel
            </a>
        </div>
        <form method="POST" action="{{ route('competidores.follow') }}" id="follow-form">
            @csrf
            <table class="table table-hover modern-table shadow-sm">
                <thead class="table-primary">
                    <tr>
                        <th>Seguir</th>
                        <th>Competidor</th>
                        <th>Publicación</th>
                        <th>Título</th>
                        <th>Categorías</th>
                        <th>Precio Original</th>
                        <th>Precio con Descuento</th>
                        <th>Información de Cuotas</th>
                        <th>Precio sin Impuestos</th>
                        <th>Cantidad Disponible</th>
                        <th>Cantidad Vendida</th>
                        <th>URL</th>
                        <th>Es Full</th>
                        <th>Envío Gratis</th>
                        <th>Última Actualización</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr class="{{ $item->following ? 'highlight-followed' : '' }}">
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>
                                <input type="hidden" name="follow[{{ $item->item_id }}]" value="no">
                                <input type="checkbox" name="follow[{{ $item->item_id }}]" value="yes" {{ $item->following ? 'checked' : '' }}>
                            </td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>{{ $item->competidor->nombre ?? 'N/A' }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>{{ $item->item_id }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>{{ $item->titulo }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>{{ $item->categorias ?? '-' }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>${{ number_format($item->precio, 2) }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>${{ $item->precio_descuento ? number_format($item->precio_descuento, 2) : '-' }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>{{ $item->info_cuotas ?? '-' }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>${{ $item->precio_sin_impuestos ? number_format($item->precio_sin_impuestos, 2) : '-' }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>{{ $item->cantidad_disponible ?? '-' }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>{{ $item->cantidad_vendida ?? '-' }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif><a href="{{ $item->url }}" target="_blank">Publicación</a></td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>{{ $item->es_full ? 'Sí' : 'No' }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>{{ $item->envio_gratis ? 'Sí' : 'No' }}</td>
                            <td @if($item->following) style="background-color: #e6f3ff;" @endif>{{ $item->ultima_actualizacion ? \Carbon\Carbon::parse($item->ultima_actualizacion)->format('d/m/Y H:i') : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle me-2"></i> No hay publicaciones descargadas aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary me-2" form="follow-form">Seguir Publicación Seleccionada</button>
            </div>
        </form>

        @include('layouts.pagination', [
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'limit' => $limit
        ])
    </div>
</div>
@endsection

@section('scripts')
   <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM cargado: Preparando evento para buscar Seller ID');
            const findSellerIdButton = document.getElementById('find-seller-id');
            if (findSellerIdButton) {
                console.log('Botón "find-seller-id" encontrado');
                findSellerIdButton.addEventListener('click', function() {
                    console.log('Botón "Buscar Seller ID" clicado');
                    const nicknameInput = document.getElementById('nickname');
                    const sellerIdInput = document.getElementById('seller_id');
                    const errorDiv = document.getElementById('seller-id-error');
                    const nickname = nicknameInput.value.trim();

                    console.log('Nickname ingresado:', nickname);
                    if (!nickname) {
                        console.log('Error: Nickname vacío');
                        errorDiv.style.display = 'block';
                        errorDiv.textContent = 'Por favor, ingresá un nickname válido.';
                        return;
                    }

                    errorDiv.style.display = 'none';
                    errorDiv.textContent = '';

                    console.log('Enviando solicitud AJAX a:', '{{ route("seller-id.find") }}');
                    fetch('{{ route("seller-id.find") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type' => 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({ nickname: nickname }),
                    })
                    .then(response => {
                        console.log('Respuesta recibida:', response);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Datos recibidos:', data);
                        if (data.success) {
                            sellerIdInput.value = data.seller_id;
                            console.log('Seller ID establecido:', data.seller_id);
                        } else {
                            errorDiv.style.display = 'block';
                            errorDiv.textContent = data.message || 'Error al buscar el Seller ID.';
                            sellerIdInput.value = '';
                            console.log('Error devuelto por el servidor:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error en la solicitud AJAX:', error);
                        errorDiv.style.display = 'block';
                        errorDiv.textContent = 'Error al buscar el Seller ID. Por favor, intenta de nuevo.';
                        sellerIdInput.value = '';
                    });
                });
            } else {
                console.error('Botón "find-seller-id" no encontrado en el DOM');
            }
        });
    </script>
@endsection
