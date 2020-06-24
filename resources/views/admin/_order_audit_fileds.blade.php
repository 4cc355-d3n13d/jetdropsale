@switch($title)
    @case('status')
        <i>
            @switch($audit->auditable_type)
                @case('App\Models\Product\MyProduct')
                    {{ \App\Enums\MyProductStatusType::getKey($value) }}
                    @break
                @case('App\Models\Order')
                    {{ \App\Enums\OrderStatusType::getKey($value) }}
                    @break
                @case('App\Models\Invoice')
                    {{ \App\Enums\InvoiceStatusType::getKey($value) }}
                    @break
            @endswitch
        </i>
        @break
    @case('billing_address')
        @break
    @case('shipping_address')
        @break
    @php($value && ksort($value))
    @include('admin.array', ['array'=> is_array($value) ? $value : [$value], 'table' => true])
    @case('product_variants')
        @break
    @case('notes')
    <pre>{!! $value !!}</pre>
        @break
    @default
    <i>{{ $value }}</i>
        @break
@endswitch
