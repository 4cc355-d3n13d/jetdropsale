<div class="header">
    <div class="header__item hide-desktop">
        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3.16642 29H28.8336C29.4768 29 30 28.4944 30 27.8697V23.3479C30 22.7232 29.4776 22.2176 28.8336 22.2176H3.16642C2.52242 22.2176 2 22.7232 2 23.3479V27.869C2 28.4944 2.52242 29 3.16642 29ZM4.33358 24.4789H27.6664V26.7394H4.33358V24.4789V24.4789Z" fill="#127C56"/>
            <path d="M19.4999 15.4345H26.4999V22.2176H19.4999V15.4345ZM26.4999 24.4782C27.7879 24.4782 28.8335 23.4656 28.8335 22.2176V15.4345C28.8335 14.1865 27.7879 13.1739 26.4999 13.1739H18.3335C17.6887 13.1739 17.1663 13.6795 17.1663 14.3042V23.3479C17.1663 23.9719 17.6895 24.4782 18.3335 24.4782H26.4999V24.4782Z" fill="#EAB64D"/>
            <path d="M11.9807 14.4942C11.7884 14.3703 11.5634 14.3043 11.3334 14.3043C11.1033 14.3043 10.8783 14.3703 10.686 14.4942L9.00014 15.5833V12.0437H13.6666V15.5833L11.9807 14.4942ZM11.3337 16.7937L14.186 18.6361C14.9619 19.1373 16.0001 18.5986 16.0001 17.6958V10.9134C16.0001 10.2887 15.4777 9.78239 14.8337 9.78239H7.83372C7.18899 9.78239 6.66656 10.2887 6.66656 10.9134V17.6951C6.66656 18.5986 7.70551 19.1373 8.48067 18.6361L11.3337 16.7937V16.7937Z" fill="#127C56"/>
            <path d="M11.9805 14.4942C11.7883 14.3703 11.5633 14.3043 11.3332 14.3043C11.1032 14.3043 10.8781 14.3703 10.6859 14.4942L9.00002 15.5833V12.0437C9.00002 11.4189 8.4776 10.9134 7.8336 10.9134H5.50002C4.21202 10.9134 3.16644 11.9259 3.16644 13.1739V22.2176C3.16644 23.4656 4.21202 24.4782 5.50002 24.4782H18.3336C18.9776 24.4782 19.5 23.9726 19.5 23.3479V13.1739C19.5 11.9259 18.4552 10.9134 17.1664 10.9134H14.8336C14.1889 10.9134 13.6664 11.4189 13.6664 12.0437V15.5833L11.9805 14.4942V14.4942ZM16 13.1739H17.1664V22.2176H5.50002V13.1739H6.66644V17.6951C6.66644 18.5986 7.70539 19.1373 8.48055 18.6361L11.3336 16.7937L14.1859 18.6361C14.9618 19.1373 16 18.5986 16 17.6958V13.1739V13.1739Z" fill="#127C56"/>
            <path d="M13.6664 12.0437C13.6664 12.6677 14.1896 13.1739 14.8336 13.1739H17.1664V14.3042C17.1664 14.9289 17.6896 15.4345 18.3336 15.4345H20.6664C21.9551 15.4345 23 14.4219 23 13.1739V5.26056C23 4.01256 21.9551 3 20.6664 3H13.6664C12.3784 3 11.3336 4.01256 11.3336 5.26056V10.9134C11.3336 11.5374 11.856 12.0437 12.5 12.0437H13.6664V12.0437ZM19.5 13.1739C19.5 11.9259 18.4551 10.9134 17.1664 10.9134H16C16 10.2887 15.4776 9.78239 14.8336 9.78239H13.6664V5.26128H20.6664V13.1739H19.5Z" fill="#DE3B35"/>
        </svg>
    </div>
    <div class="header__item hide-mobile">
        <div class="content-menu">
            <div class="content-menu__item is-active">
                <a href="{{ route("catalog") }}" title="" class="content-menu__link">Catalog</a>
            </div>
            <div class="content-menu__item">
                <a href="https://blog.dropwow.com/" title="" class="content-menu__link">Blog</a>
            </div><div class="content-menu__item">
                <a href="https://help.dropwow.com/" title="" class="content-menu__link">Help</a>
            </div><div class="content-menu__item">
                <a href="https://support.dropwow.com/contact-us" title="" class="content-menu__link">Contact us</a>
            </div>
        </div>
    </div>
    <div class="header__item header__item-search">
        <form action="{{ route('catalog.search') }}" name="searchForm" method="GET" class="search">
            <input type="text" class="search__field" name="query" placeholder="Search products"
                @if( app('request')->input('query') )
                    value="{{app('request')->input('query')}}"
                @endif
            >
            <span onclick="searchForm.submit();" class="search__trigger"><i class="fas fa-search"></i></span>
        </form>
    </div>
    <div @click="toggleDropdownMenu" class="header__item hide-desktop">
        <i class="fas fa-bars"></i>
    </div>
</div>
