<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                @can('view_dashboard')
                    <li class="nav-item">
                        <a href="{{ route('admin.home') }}" class="nav-link">
                            <i class="mdi mdi-home"></i>
                            <span class="hide-menu">Dashboard </span>
                        </a>
                    </li>
                @endcan
                @can('view_customers')
                    <li>
                        <a class="has-arrow" href="#" aria-expanded="false">
                            <i class="mdi mdi-account"></i>
                            <span class="hide-menu">Customer</span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <li>
                                <a href="{{ route('admin.customers.index') }}"
                                    class="nav-link {{ request()->is('admin/customers') || request()->is('admin/customers/*') ? 'active' : '' }}">
                                    <i class="fa fa-alpha-down nav-icon">
                                    </i>
                                    Customers
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.subscriptions.index') }}"
                                    class="nav-link {{ request()->is('admin/subscriptions') || request()->is('admin/subscriptions/*') ? 'active' : '' }}">
                                    <i class="fa fa-alpha-down nav-icon">
                                    </i>
                                    Subscriptions
                                </a>
                            </li>

                        </ul>
                    </li>
                @endcan
                @can('view_security')
                    <li>
                        <a class="has-arrow" href="#" aria-expanded="false">
                            <i class="mdi mdi-security"></i>
                            <span class="hide-menu">Security</span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <li>
                                <a href="{{ route('admin.blacklist.index') }}"
                                    class="nav-link {{ request()->is('admin/blacklist') || request()->is('admin/blacklist/*') ? 'active' : '' }}">
                                    <i class="fa fa-alpha-down nav-icon">

                                    </i>
                                    Blacklists
                                </a>
                            </li>


                        </ul>
                    </li>
                @endcan
                @can('view_products')
                    <li>
                        <a class="has-arrow" href="#" aria-expanded="false">
                            <i class="mdi mdi-format-line-spacing"></i>
                            <span class="hide-menu">Catalogue</span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <li>
                                <a href="{{ route('admin.contentstype.index') }}"
                                    class="nav-link {{ request()->is('admin/contentstype') || request()->is('admin/contentstype/*') ? 'active' : '' }}">
                                    <i class="fa fa-alpha-down nav-icon">

                                    </i>
                                    SMS Contents Type
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.contents.index') }}"
                                    class="nav-link {{ request()->is('admin/contents') || request()->is('admin/contents/*') ? 'active' : '' }}">
                                    <i class="fa fa-alpha-down nav-icon"> </i>
                                    SMS Contents
                                </a>
                            </li>

                        </ul>
                    </li>
                @endcan

		@can('view_general_reports')
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <i class="mdi mdi-bullhorn"></i>
                        <span class="hide-menu">BroadCast</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">

                        <li>
                            <a href="{{ route('admin.contact.groups') }}"
                                class="nav-link {{ request()->is('admin/contact/groups') || request()->is('admin/contact/groups/*') ? 'active' : '' }}">
                                <i class="fa fa-alpha-down nav-icon"> </i>
                                Groups
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.contact.campaign') }}"
                                class="nav-link {{ request()->is('admin/contact/campaign') || request()->is('admin/contact/campaign/*') ? 'active' : '' }}">

                                <i class="fa fa-alpha-down nav-icon"> </i>
                                SMS Campaigns
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.contact.outboundcampaing') }}"
                                class="nav-link {{ request()->is('admin/contact/outboundcampaing') || request()->is('admin/contact/outboundcampaing/*') ? 'active' : '' }}">

                                <i class="fa fa-alpha-down nav-icon"> </i>
                               OBD Campaigns
                            </a>
                        </li>


                    </ul>
                </li>
		@endcan

                @can('view_tickets')
                            <li class="nav-item">
                                <a href="{{ route('admin.promotions.index') }}"
                                    class="nav-link {{ request()->is('admin/promotion') || request()->is('admin/promotion/*') ? 'active' : '' }}">
                                    <i class="mdi mdi-comment"></i>
                                    <span class="hide-menu">Promotion</span>
                                </a>
                            </li>
                @endcan
                @can('view_tickets')
                    <li class="nav-item">
                        <a href="{{ route('admin.ticket.index') }}"
                            class="nav-link {{ request()->is('admin/ticket') || request()->is('admin/ticket/*') ? 'active' : '' }}">
                            <i class="mdi mdi-ticket"></i>
                            <span class="hide-menu">Tickets</span>
                        </a>
                    </li>
                @endcan

                @can('view_transactions')
                    <li>
                        <a class="has-arrow" href="#" aria-expanded="false">
                            <i class="mdi mdi-bank"></i>
                            <span class="hide-menu">Transaction</span>
                        </a>
                        <ul aria-expanded="false" class="collapse">

                            <li>
                                <a href="{{ route('admin.billing') }}"
                                    class="nav-link {{ request()->is('admin/billing') || request()->is('admin/billing/*') ? 'active' : '' }}">
                                    <i class="fa fa-alpha-down nav-icon">

                                    </i>
                                    Billing Details
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.invoice.index') }}"
                                    class="nav-link {{ request()->is('admin/invoice') || request()->is('admin/invoice/*') ? 'active' : '' }}">
                                    <i class="fa fa-alpha-down nav-icon">

                                    </i>
                                    Invoice Lists
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.transactions.doctors') }}"
                                    class="nav-link {{ request()->is('admin/doctortranscations') || request()->is('admin/doctortranscations/*') ? 'active' : '' }}">
                                    <i class="fa fa-alpha-down nav-icon">

                                    </i>
                                    Doctor's Transaction
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.transactions.index') }}"
                                    class="nav-link {{ request()->is('admin/transactions') || request()->is('admin/transactions/*') ? 'active' : '' }}">
                                    <i class="fa fa-alpha-down nav-icon">

                                    </i>
                                    All Transactions
                                </a>
                            </li>

                        </ul>
                    </li>
                @endcan
                @can('view_users')
                    <li>
                        <a class="has-arrow" href="#" aria-expanded="false">
                            <i class="mdi mdi-account-multiple"></i>
                            <span class="hide-menu">User Management</span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <li>
                                <a href="{{ route('admin.roles.index') }}"
                                    class="nav-link {{ request()->is('admin/roles') || request()->is('admin/roles/*') ? 'active' : '' }}">
                                    Roles
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.index') }}"
                                    class="nav-link {{ request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : '' }}">
                                    Users
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
                @can('view_reports')
                    <li>
                        <a class="has-arrow" href="#" aria-expanded="false">
                            <i class="mdi mdi mdi-chart-bar"></i>
                            <span class="hide-menu">Report</span>
                        </a>
                        
                        <ul aria-expanded="false" class="collapse">
   			   @can('view_customer_reports')
                            <li>
                                <a href="{{ route('admin.messagereportview') }}" class="nav-link">
                                    <i class="fa fa-alpha-down nav-icon">
                                    </i>
                                    Message Report
                                </a>
                            </li>
                            @endcan
                            @can('view_tickets_reports')
                                <li>
                                    <a href="{{ route('admin.ticketreport') }}"
                                        class="nav-link {{ request()->is('admin/ticketreport') || request()->is('admin/ticketreport/*') ? 'active' : '' }}">
                                        <i class="fa fa-alpha-down nav-icon">

                                        </i>
                                        Tickets Reports
                                    </a>
                                </li>
                            @endcan
                            @can('view_revenue_reports')
                                <li>
                                    <a href="{{ route('admin.revenueview') }}"
                                        class="nav-link {{ request()->is('admin/revenueview') || request()->is('admin/revenueview/*') ? 'active' : '' }}">
                                        <i class="fa fa-alpha-down nav-icon">

				                     	</i>
 				                    	Daily Revenue
                                    </a>
				                </li>

                            @endcan
                            @can('view_content_checking_reports')
                                <li>
                                    <a href="{{ route('admin.monthlyrevenueview') }}"
                                        class="nav-link {{ request()->is('admin/monthlyrevenueview') || request()->is('admin/monthlyrevenueview/*') ? 'active' : '' }}">
                                        <i class="fa fa-alpha-down nav-icon">

                                        </i>
                                        Monthly Revenue
                                    </a>
                                </li>
                            @endcan
                            @can('view_content_reports')
                                <li>
                                    <a href="{{ route('admin.contentview') }}"
                                        class="nav-link {{ request()->is('admin/contentview') || request()->is('admin/contentview/*') ? 'active' : '' }}">
                                        <i class="fa fa-alpha-down nav-icon">

                                        </i>
                                        Contents Reports
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>

                @endcan
                @can('view_settings_admin')
                    <li>
                        <a class="has-arrow" href="#" aria-expanded="false">
                            <i class="ti-settings"></i>
                            <span class="hide-menu">Settings</span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <li class="nav-item">
                                <a href="{{ route('admin.products.index') }}"
                                    class="nav-link {{ request()->is('admin/products') || request()->is('admin/products/*') ? 'active' : '' }}">
                                    Products
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.backup.index') }}"
                                    class="nav-link {{ request()->is('admin/backup') || request()->is('admin/backup/*') ? 'active' : '' }}">
                                    Backups
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
            </ul>
        </nav>
    </div>
</aside>
