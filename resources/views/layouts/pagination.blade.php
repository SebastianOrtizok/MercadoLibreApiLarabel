@if ($totalPages > 1)
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <!-- Botón Anterior -->
            <li class="page-item {{ $currentPage == 1 ? 'disabled' : '' }}">
                <a class="page-link"
                   href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1, 'limit' => $limit]) }}"
                   aria-label="Anterior">&laquo;</a>
            </li>

            <!-- Primera página -->
            @if ($currentPage > 3)
                <li class="page-item">
                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => 1, 'limit' => $limit]) }}">1</a>
                </li>
                @if ($currentPage > 4)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
            @endif

            <!-- Números de página dinámicos -->
            @for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i, 'limit' => $limit]) }}">{{ $i }}</a>
                </li>
            @endfor

            <!-- Última página -->
            @if ($currentPage < $totalPages - 2)
                @if ($currentPage < $totalPages - 3)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
                <li class="page-item">
                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $totalPages, 'limit' => $limit]) }}">{{ $totalPages }}</a>
                </li>
            @endif

            <!-- Botón Siguiente -->
            <li class="page-item {{ $currentPage == $totalPages ? 'disabled' : '' }}">
                <a class="page-link"
                   href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1, 'limit' => $limit]) }}"
                   aria-label="Siguiente">&raquo;</a>
            </li>
        </ul>
    </nav>
@endif
