<?php
use App\Models\Order;
?>

@if($items)
    @foreach ($items as $number)
        @if($number)
            <span><nobr/>
                - <i> {{ trim($number) }} </i>
            </span><br/>
        @endif
    @endforeach
@endif
