<?php
use App\Models\Product\Product;
use App\Models\Product\MyProduct;
?>

<div>
    <div style="display: -webkit-box;">
        @foreach($imgs as $img)
        <div data-v-154ed076="" class="mr-4" style="max-width: 16rem">
            <img src="{{$img}}" class="border-lg border-50 border" draggable="false">
        </div>
        @endforeach
    </div>
</div>
