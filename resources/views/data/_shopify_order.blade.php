<div class="jumbotron">
<div class="row">
    <div class="col-md-8"><h2>{{ $order['id'] }} {{ $order['name'] }} <a href="{{ $order['order_status_url'] }}" target="_blank">Full info</a> </h2></div>
</div>
<div class="row">
    <div class="col-md-2">
        <a href="{{route('data.shopify.order-sync', ['shop'=>request('shop'), 'id'=>$order['id']])}}" target="_blank" type="button" class="btn btn-primary btn-xs">Sync</a>
        <a href="{{route('data.shopify.order-sync', ['shop'=>request('shop'), 'id'=>$order['id'], 'force' => 1])}}"  target="_blank" type="button" class="btn btn-danger btn-xs">Sync force</a>
    </div>
</div>
    <br>
@php( $dwOrder = \App\Models\Order::firstOrNew(['origin_id'=> $order['id']]))
<table class="table
    @if($dwOrder->id)
        bg-primary
    @endif
    "
>
    <tbody>
    <tr>
        <th>Статус </th>
        <th>Сумма </th>
        <th>Валюта </th>
        <th>Shopify Status </th>
        <th>DW id</th>
        <th>DW price</th>
        <th>DW status</th>
        <th>Date</th>
    </tr>
    <tr>
        <td>{{ $order['financial_status'] }}</td>
        <td>{{ $order['total_price'] }}</td>
        <td>{{ $order['currency'] }}</td>
        <td>{{ strtoupper($order['financial_status']) }}</td>
        <td><a href="/nova/resources/orders/{{ $dwOrder->id }}" class="btn btn-primary">{{ $dwOrder->id }}</a></td>
        <td><p class="badge badge-warning">{{ $dwOrder->price }}</p></td>
        <td><p class="badge badge-danger">{{ \App\Enums\OrderStatusType::getKey($dwOrder->status) }}</p></td>
        <td>{{ \Illuminate\Support\Carbon::createFromTimeString($order['created_at'])->format('Y-m-d H:i:s') }}</td>
    </tr>
    </tbody>
</table>
<div class="row">
    <div class="col-md-10"><h2>Товары</h2></div>
</div>
<table class="table">
    <tbody>
    <tr>
        <th>id</th>
        <th>variant id</th>
        <th>title</th>
        <th>quantity</th>
        <th>price</th>
        <th>sku</th>
        <th>variant_title</th>
    </tr>
    </tbody>
    @foreach($order['line_items'] as $item )
        @php($product = \App\Models\Shopify\ShopifyProduct::firstOrNew(['shopify_id'=>$item['id']]))
        @php($productVariant = \App\Models\Shopify\ProductVariant::firstOrNew(['shopify_variant_id'=>$item['variant_id']]))
        <tr @if($product->id || $productVariant->id)
                class="bg-success"
            @endif
        >
            <td>{{ $item['id'] }}
            @if($product->id)
                <a href="{{ route('product.show', ['id'=>$product->myProduct->product_id]) }}" class="btn btn-primary">{{ $product->myProduct->product_id }}</a>
            @endif
            </td>
            <td>{{ $item['variant_id'] }}
            @if($productVariant->id)
                    <a href="{{ route('product.show', ['id'=>$productVariant->product_id]) }}" class="btn btn-primary">{{ $productVariant->product_id }}</a>
            @endif
            </td>
            <td>{{ $item['title'] }}</td>
            <td>{{ $item['quantity'] }}</td>
            <td>{{ $item['price'] }}</td>
            <td>{{ $item['sku'] }}</td>
            <td>{{ $item['variant_title'] }}</td>
        </tr>
    @endforeach
</table>
</div>