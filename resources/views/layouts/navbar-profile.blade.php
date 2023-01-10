<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="https://ui-avatars.com/api/?rounded=true&bold=true&name= {{ Auth::user()->name }}" alt="user" class="profile-pic" /></a>
    <div class="dropdown-menu dropdown-menu-right animated flipInY">
        <ul class="dropdown-user">
            @can('view_customer_balance')
            <li>
                <div class="dw-user-box">
                    <div class="u-img"><img src="https://ui-avatars.com/api/?rounded=true&bold=true&name= {{ Auth::user()->name }}"  alt="user"></div>
                    <div class="u-text">
                        <h4> {{ Auth::user()->name }}</h4>
                        <p class="text-muted"> {{ Auth::user()->email }}</p></div>
                </div>
            </li>
            <li role="separator" class="divider"></li>
            <li><a href="{{ route('admin.setting') }}" ><i class="ti-settings"></i> Account Setting</a></li>
            <li role="separator" class="divider"></li>
            @endcan
     

            <li>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                    Logout <i class="fa fa-fw fa-sign-out pull-right  mt-1"></i>
                </a>
            </li>
        </ul>
    </div>
    <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>
</li>
