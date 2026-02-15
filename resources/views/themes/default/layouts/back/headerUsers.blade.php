


<li class="nxl-item nxl-caption">
    <label>-----</label>
</li>
<li class="nxl-item {{request()->is('dashboard.users.courses') ? 'active' : ''}}">
    <a href="{{route('dashboard.users.courses')}}" class="nxl-link">
        <span class="nxl-micon"><i class="feather-book"></i></span>
        <span class="nxl-mtext">@lang('l.Courses')</span>
    </a>
</li>
<li class="nxl-item {{request()->is('dashboard.users.invoices') ? 'active' : ''}}">
    <a href="{{route('dashboard.users.invoices')}}" class="nxl-link">
        <span class="nxl-micon"><i class="feather-file-text"></i></span>
        <span class="nxl-mtext">@lang('l.Invoices')</span>
    </a>
</li>