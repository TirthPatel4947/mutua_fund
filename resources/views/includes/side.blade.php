<!-- BEGIN: Main Menu -->
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">

            <!-- General Section  -->
            <li class="navigation-header"><span>General</span><i class="feather icon-minus" data-toggle="tooltip" data-placement="right" data-original-title="General"></i></li>

            <!-- Dashboard Link -->
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" aria-label="Dashboard">
                    <i class="feather icon-home"></i>
                    <span class="menu-title" data-i18n="Dashboard">Dashboard</span>
                    <span class="badge badge-primary badge-pill float-right mr-2"></span>
                </a>
            </li>


            <!-- Buy Link -->
            <li class="nav-item">
                <a href="{{ route('buy') }}" aria-label="buy funds">
                    <i class="feather icon-monitor"></i>
                    <span class="menu-title" data-i18n="Buy">Buy</span>
                </a>
            </li>

            <!-- Sell Link -->
            <li class="nav-item">
                <a href="{{ route('sale') }}" aria-label="sale">
                    <i class="feather icon-clipboard"></i>
                    <span class="menu-title" data-i18n="Sell">sale</span>
                </a>
            </li>
            <!-- Transactions Link -->
            <li class="nav-item">
                <a href="{{ route('report') }}" aria-label="Report">
                    <i class="feather icon-file"></i>
                    <span class="menu-title" data-i18n="Report">Transactions</span>
                </a>
            </li>
            <!-- Overview Link -->
            <li class="nav-item">
                <a href="{{ route('fund.details') }}" aria-label="Overview">
                    <i class="feather icon-info"></i>
                    <span class="menu-title" data-i18n="Overview">Over View</span>
                </a>
            </li>

            





            <!-- Portfolio Link -->
            <li class="nav-item">
                <a href="/portfolio" aria-label="Fund/Stock">
                    <i class="feather icon-credit-card"></i>
                    <span class="menu-title" data-i18n="Fund/Stock"> Portfolio</span>
                </a>
            </li>


       
        </ul>
    </div>
</div>
<!-- END: Main Menu -->