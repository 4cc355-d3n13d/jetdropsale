<style>
    #customButton {
        height: 35px;
        width: 90px;
    }
</style>

<script src="https://checkout.stripe.com/checkout.js"></script>

<button id="customButton">Add card</button>

<script>
    const handler = StripeCheckout.configure({
        key: '{{ env('STRIPE_PUBLIC_KEY') }}',
        image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
        locale: 'auto',
        source: function(source) {
            // You can access the token ID with `token.id`.
            // Get the token ID to your server-side code for use.

            let body =
                'source_id=' + encodeURIComponent(source.id) +
                '&brand=' + encodeURIComponent(source.card.brand) +
                '&last4=' + encodeURIComponent(source.card.last4) +
                '&exp_month=' + encodeURIComponent(source.card.exp_month) +
                '&exp_year=' + encodeURIComponent(source.card.exp_year) +
                '&_token={{ csrf_token() }}'
            ;

            let xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route('api_save_card') }}');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
            xhr.onreadystatechange = function() {
                // Process the server response here.
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        console.log(xhr.responseText);
                        alert(xhr.responseText);
                    } else {
                        alert('There was a problem with the request.');
                    }
                }
            };
            xhr.send(body);
        }
    });

    document.getElementById('customButton').addEventListener('click', function(e) {
        // Open Checkout with further options:
        handler.open({
            name: 'Dropwow Ltd.',
            description: 'Add card to the account',
            panelLabel: 'Save card',
            allowRememberMe: false,
            email: '{{ $user->email }}'
        });
        e.preventDefault();
    });

    // Close Checkout on page navigation:
    window.addEventListener('popstate', function() {
        handler.close();
    });
</script>
