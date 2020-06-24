@if($options)
    @foreach($options as $option)
    <div style="display: -webkit-box;">
            <span>
                <i> {{ $option['name'] }}: </i>
                <b> {{ $option['value'] }} </b>
            </span>
            <br/>
            <div data-v-154ed076="" class="mr-4" style="max-width: {{ $rem }}rem">
                <img src="{{$option['image'] }}" class="border-lg border-50 border" draggable="false">
            </div>
    </div>
    @endforeach

@endif
