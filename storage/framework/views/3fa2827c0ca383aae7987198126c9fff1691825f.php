        <?php echo $__env->make('incs.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        <div class="wrapper sidebar_large">

            <!-- INCLUDE SIDEBAR -->

            <?php echo $__env->make('partials._sidebar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

            <!-- INCLUDE TOP NAV -->
            <!-- top navigation -->
            <div class="nav_header js_height-header py-4">
            	<a id="menu_toggle"><i class="fas fa-exchange-alt"></i></a>
            	<div class="px-3 d-flex justify-content-center align-items-center w-100">
            		<h1 class="title mb-0"><?php echo e(\App\HelperX::appName()); ?></h1>
            	</div>
            </div>
            <!-- /top navigation -->

            <!-- page content -->
            <div class="content_wrapper">

                <!-- CONTENT BLOCK HERE -->
                

                <div class="dashboard">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>

            </div>
            <!-- /page content -->

            <!-- INCLUDE FOOTER -->
            <!-- footer content -->
            <?php echo $__env->make('incs.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <!-- /footer content -->
