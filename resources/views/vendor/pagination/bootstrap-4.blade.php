@if ($paginator->hasPages())
    <div class="dw-pagination pager_large_screen" role="navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <div class="dw-pages-container__page disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                <span class="dw-page-link" aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
            </div>
        @else
            <a class="dw-page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
            <div class="dw-pages-container__page">

                    <i class="fas fa-chevron-left"></i>

            </div>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <a class="dw-page-link"><div style="cursor:default" class="dw-pages-container__page three-points" aria-disabled="true"><span class="dw-page-link">{{ $element }}</span></div></a>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <div class="dw-pages-container__page dw-active_page" aria-current="page"><span class="dw-page-link">{{ $page }}</span></div>
                    @else
                        <a class="dw-page-link" href="{{ $url }}"><div class="dw-pages-container__page">{{ $page }}</div></a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a class="dw-page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
            <div class="dw-pages-container__page">

                    <i class="fas fa-chevron-right"></i>

            </div>
            </a>
        @else
            <div class="dw-pages-container__page disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                <span class="dw-page-link" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
            </div>
        @endif
    </div>
    <div class="dw-pagination pager_small_screen">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <div class="dw-pages-container__page disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                <span class="dw-page-link" aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
            </div>
        @else
            <a class="dw-page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                <div class="dw-pages-container__page">

                    <i class="fas fa-chevron-left"></i>

                </div>
            </a>
        @endif

        @if($paginator->currentPage() > 3)
            <a class="dw-page-link" href="{{ $paginator->url(1) }}"><div class="dw-pages-container__page">1</div></a>
        @endif
        @if($paginator->currentPage() > 4)
            <div class="dw-pages-container__page three-points"><span>...</span></div>
        @endif
        @foreach(range(1, $paginator->lastPage()) as $i)
            @if($i >= $paginator->currentPage() && $i <= $paginator->currentPage())
                @if ($i == $paginator->currentPage())
                    <div class="dw-pages-container__page dw-active_page" aria-current="page"><span>{{ $i }}</span></div>
                @else
                    <a class="dw-page-link"  href="{{ $paginator->url($i) }}"><div class="dw-pages-container__page">{{ $i }}</div></a>
                @endif
            @endif
        @endforeach
        @if($paginator->currentPage() < $paginator->lastPage() - 3)
            <div style="cursor:default" class="dw-pages-container__page three-points" aria-disabled="true"><span>...</span></div>
        @endif
        @if($paginator->currentPage() < $paginator->lastPage() - 2)
            <a href="{{ $paginator->url($paginator->lastPage()) }}"><div class="dw-pages-container__page">{{ $paginator->lastPage() }}</div></a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a class="dw-page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                <div class="dw-pages-container__page">

                    <i class="fas fa-chevron-right"></i>

                </div>
            </a>
        @else
            <div class="dw-pages-container__page disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                <span class="dw-page-link" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
            </div>
        @endif
    </div>
@endif

