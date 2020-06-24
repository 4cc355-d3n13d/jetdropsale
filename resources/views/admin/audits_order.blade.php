<?php
/**
 * @var \OwenIt\Auditing\Models\Audit $audit
 * */
?>
@if($audit && $audit->id)
    <table class="table">
        @foreach($audit->getModified() as $title => $values)
            @php
                if(array_key_exists('old', $values) && is_array($values['old'])){
                    ksort($values['old']);
                }
                if(array_key_exists('new', $values) && is_array($values['new'])){
                    ksort($values['new']);
                }
            @endphp
            @if(array_key_exists('new', $values) && array_key_exists('old', $values) && $values['old'] != $values['new'])
                <tr>
                    <th>
                        {{ $title }}
                    </th>
                    <td>
                        @php($value = $values['old'])
                        @include('admin._order_audit_fileds')
                    </td>
                    <td>=></td>
                    <td>
                        @php($value = $values['new'])
                        @include('admin._order_audit_fileds')
                    </td>
                </tr>
            @endif
        @endforeach
    </table>
@endif
