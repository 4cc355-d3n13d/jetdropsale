
    @if ($cards->count())
        <p>You have the following cards:</p>
        @foreach($cards as $card)

            <div style="border: 1px dashed #000; height: 150px; width: 200px;">
                <p>{{ $card->brand }}:</p>
                <p>**** **** **** {{ $card->last4 }}</p>
                <p>Exp: {{ $card->exp_month }} / {{ $card->exp_year }}</p>
            </div>

        @endforeach
    @else
        You have any cards yet :(
    @endif