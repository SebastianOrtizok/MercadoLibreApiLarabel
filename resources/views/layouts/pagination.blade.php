@if ($totalPages > 1)
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <!-- Botón Anterior -->
            <li class="page-item {{ $currentPage == 1 ? 'disabled' : '' }}">
                <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}" aria-label="Anterior">«</a>
            </li>

            <!-- Primera página -->
            <li class="page-item {{ $currentPage == 1 ? 'active' : '' }}">
                <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => 1]) }}">1</a>
            </li>

            <!-- Elipsis si hay más de 5 páginas antes -->
            @if ($currentPage > 4)
                <li class="page-item disabled"><span class="page-link">...</span></li>
            @endif

            <!-- Páginas cercanas -->
            @for ($i = max(2, $currentPage - 2); $i <= min($totalPages - 1, $currentPage + 2); $i++)
                <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                </li>
            @endfor

            <!-- Elipsis si hay más de 5 páginas después -->
            @if ($currentPage < $totalPages - 3)
                <li class="page-item disabled"><span class="page-link">...</span></li>
            @endif

            <!-- Última página -->
            @if ($totalPages > 1)
                <li class="page-item {{ $currentPage == $totalPages ? 'active' : '' }}">
                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $totalPages]) }}">{{ $totalPages }}</a>
                </li>
            @endif

            <!-- Botón Siguiente -->
            <li class="page-item {{ $currentPage == $totalPages ? 'disabled' : '' }}">
                <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}" aria-label="Siguiente">»</a>
            </li>
        </ul>
    </nav>
@endif
