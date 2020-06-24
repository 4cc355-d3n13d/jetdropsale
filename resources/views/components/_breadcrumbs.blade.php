@if (count($breadcrumbs))

    <div class="breadcrumbs">
        @foreach ($breadcrumbs as $breadcrumb)

            @if ($breadcrumb->url && !$loop->last)
                <div class="breadcrumbs__item">
                    <a href="{{ $breadcrumb->url }}" title="{{ $breadcrumb->title }}" class="breadcrumbs__link">
                        {{ $breadcrumb->title }}
                    </a>
                </div>
            @else
                <div class="breadcrumbs__item is-active">
                    <a href="#" title="{{ $breadcrumb->title }}">
                        {{ $breadcrumb->title }}
                    </a>
                </div>
            @endif

        @endforeach
    </div>

@endif
