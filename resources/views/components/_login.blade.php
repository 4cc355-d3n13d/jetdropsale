<div class="page__sidebar_item">
    <div class="login">
        <div class="login__item">
            @auth
                <div class="login__avatar"><i class="fas fa-user"></i></div>
            @endauth
        </div>
        <div class="login__item" style="cursor: pointer">
            @guest
                <div class="login__action">
                    <a href="{{ route('login') }}" title="" class="sidebar-menu__item">
                        <span class="login__action_text">Login</span>
                    </a>
                </div>
            @endguest
            @auth
                <div class="login__name"  onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">{{ Auth::user()->name }}</div>
                <div class="login__action"  onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                    <span class="login__action_icon"><i class="fas fa-sign-in-alt"></i></span>
                    <span class="login__action_text">Logout</span>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            @endauth
        </div>
    </div>
</div>