@if(app()->environment() == 'production')
    @auth
        <script>
            window.intercomSettings = window.intercomSettings || {
                app_id: "n33wds1p",
                name: "{{ auth()->user()->name }} (id{{ auth()->id() }})", // Full name
                email: "{{ auth()->user()->email }}", // Email address
                created_at: "{{ auth()->user()->created_at }}", // Signup Date
                dropwow_user_id: "{{ auth()->id() }}",
                Link: "{{ \Illuminate\Support\Facades\URL::to('/nova/resources/users/' . auth()->id()) }}"
            };
        </script>
    @endauth
    <!-- Google Tag Manager -->
    <script>(function (w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start':
                    new Date().getTime(), event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-T4B6XMJ');</script>
    <!-- End Google Tag Manager -->
@endif