        @include('incs.header')

        <div class="wrapper sidebar_large">

            <!-- INCLUDE SIDEBAR -->

            @include('partials._sidebar')

            <!-- INCLUDE TOP NAV -->
            <!-- top navigation -->
            <div class="nav_header js_height-header py-4">
            	<a id="menu_toggle"><i class="fas fa-exchange-alt"></i></a>
            	<div class="px-3 d-flex justify-content-center align-items-center w-100">
            		<h1 class="title mb-0">{{\App\HelperX::appName()}}</h1>
            	</div>
            </div>
            <!-- /top navigation -->

            <!-- page content -->
            <div class="content_wrapper">

                <!-- CONTENT BLOCK HERE -->
                

                <div class="dashboard">
                    @yield('content')
                </div>

            </div>
            <!-- /page content -->

            <!-- INCLUDE FOOTER -->
            <!-- footer content -->
            @include('incs.footer')
            <!-- /footer content -->
