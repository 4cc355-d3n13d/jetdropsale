@if($array)
    @if($table)
        <table class="">
            @foreach($array as $key => $value)
                <tr>
                    <td><i> {{ $key }}: </i></td>
                    <td>
                    @if(is_string($value))
                        {{ $value }}
                    @else
                        {{ print_r($value, 1) }}
                    @endif
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        @foreach($array as $key => $value)
            @if ($value)
            <span><nobr/>
                <i> {{ $key }}:</i>
                <b> {{ $value }}</b>
            </span><br/>
            @endif
        @endforeach
    @endif
@endif
