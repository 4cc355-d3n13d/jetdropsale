{{--<div class="page__sidebar_item hide-desktop">--}}
    {{--<h3 class="page__sidebar_title">Menuu</h3>--}}
    {{--<span class="page__sidebar_close"><i class="fas fa-times-circle"></i></span>--}}
{{--</div>--}}
<div class="page__sidebar_item hide-desktop">
    {{--<div class="content-menu">--}}
        {{--<div class="content-menu__item is-active">--}}
            {{--<a href="#" title="" class="content-menu__link">--}}
                {{--<span class="content-menu__link_text">Catalog</span>--}}
                {{--<i class="fas fa-chevron-right"></i>--}}
            {{--</a>--}}
        {{--</div>--}}
        {{--<div class="content-menu__item">--}}
            {{--<a href="#" title="" class="content-menu__link">--}}
                {{--<span class="content-menu__link_text">Resources</span>--}}
                {{--<i class="fas fa-chevron-right"></i>--}}
            {{--</a>--}}
        {{--</div><div class="content-menu__item">--}}
            {{--<a href="#" title="" class="content-menu__link">--}}
                {{--<span class="content-menu__link_text">Help</span>--}}
                {{--<i class="fas fa-chevron-right"></i>--}}
            {{--</a>--}}
        {{--</div><div class="content-menu__item">--}}
            {{--<a href="#" title="" class="content-menu__link">--}}
                {{--<span class="content-menu__link_text">Contact us</span>--}}
                {{--<i class="fas fa-chevron-right"></i>--}}
            {{--</a>--}}
        {{--</div>--}}
    {{--</div>--}}
</div>
<div class="page__sidebar_item is-priority">
    <div class="sidebar-menu">
        <a href="/my/products" title="" class="sidebar-menu__item is-active">
            <span class="sidebar-menu__item_icon"><i class="fas fa-cart-arrow-down"></i></span>My products
            <a href="/my/products/connected" title="" class="sidebar-menu__item-inner">Connected</a>
            <a href="/my/products/non_connected" title="" class="sidebar-menu__item-inner">Non connected</a>
        </a>
        <a href="/my/orders" title="" class="sidebar-menu__item">
            <span class="sidebar-menu__item_icon"><i class="fas fa-dolly"></i></span>Orders
        </a>
        {{--<a href="/my/alerts" title="" class="sidebar-menu__item">--}}
            {{--<span class="sidebar-menu__item_icon"><i class="fas fa-bell"></i></span>Alerts--}}
        {{--</a>--}}
        <a href="/my/billing" title="" class="sidebar-menu__item">
            <span class="sidebar-menu__item_icon"><i class="fas fa-coins"></i></span>Billing
        </a>
        <a href="/my/settings" title="" class="sidebar-menu__item">
            <span class="sidebar-menu__item_icon"><i class="fas fa-cog"></i></span>Settings
        </a>
        @can('viewNova')
            <a href="/nova" title="" class="sidebar-menu__item">
                <span class="sidebar-menu__item_icon"><i class="fas fa-sign-in-alt"></i></span>Admin panel
            </a>
        @endif
        @if(auth()->user()->isImpersonating())
            <a href="{{ route('impersonate.stop') }}" title="" class="sidebar-menu__item">
                <span class="sidebar-menu__item_icon"><i class="fas fa-cog"></i></span>Back to admin
            </a>
        @endif
    </div>
</div>
@include("components._login")