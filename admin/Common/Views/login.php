<?php
$template = service('template');
$validation = \Config\Services::validation();


?>
<main id="main-container">

       <!-- Page Content -->
<div class="bg-image" style="background-image: url(<?php echo base_url() ?>/themes/default/assets/images/modal-bg.jpg);">
    <div class="row mx-0 bg-black-op">
        <div class="hero-static col-md-6 col-xl-8 d-none d-md-flex align-items-md-end">
            <div class="p-30 invisible" data-toggle="appear">
                <!-- <p class="font-size-h3 font-w600 text-white">
                    Get Inspired and Create.
                </p> -->
                <!-- <p class="font-italic text-white-op">
                    Copyright &copy; <span class="js-year-copy">2017</span>
                </p> -->
            </div>
        </div>
      
        <div class="hero-static col-md-6 col-xl-4 d-flex align-items-center bg-white invisible" data-toggle="appear" data-class="animated fadeInRight">
            <div class="content content-full">
                <!-- Header -->
                <div class="col-sm-12">
                    <!-- <a class="link-effect font-w700" href="index.php">
                        <i class="si si-fire"></i>
                        <span class="font-size-xl text-primary-dark">code</span><span class="font-size-xl">base</span>
                    </a> -->
                    <h5 class="h5 font-w700 mt-30 mb-10 text-center">Goverment Of Odisha</h5>
					<h5 class="h5 font-w700 mt-10 mb-10 text-center">Department of Agriculture & Farmers' Empowerment</h5>
					<h5 class="h5 font-w700 mt-10 mb-10 text-center">Odisha Millet Mission</h5>
                    <h2 class="h5 font-w400 text-muted mb-0 mt-3 text-center">SOE & MIS PORTAL</h2>
                </div>
                <!-- END Header -->

                <!-- Sign In Form -->
                <!-- jQuery Validation (.js-validation-signin class is initialized in js/pages/op_auth_signin.js) -->
                <!-- For more examples you can check out https://github.com/jzaefferer/jquery-validation -->
                <?php echo form_open(env('app.adminRoute').'/login',array('class' => 'js-validation-signin', 'id' => 'form-signin','role'=>'form')); ?>
                    <div class="form-group row">
                        <div class="col-12">
                            <div class="form-material floating">
                          
                                            <input type="text" class="form-control" placeholder="Username" id="login-username" name="username">
                              
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <div class="form-material floating">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                             
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="login-remember-me" name="login-remember-me" style="opacity: 0.7;">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Remember Me</span>
                            </label>
                        </div>
                    </div>
                    <?php if($login_error) { ?>
                                    <div class="form-group row">
                                        <div class="col-12">
                                                <strong class="text-danger pull-left">Error: <?=$login_error?></strong>
                                        </div>
                                    </div>
                                    <?php } ?>
                    <div class="form-group">
                                    <?php if ($redirect) { ?>
											<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
											<?php } ?>
											<button type="submit" class="btn btn-alt-primary">
                                                <i class="si si-login mr-10"></i> Sign In
                                            </button>
                        <div class="mt-30">
                        <a class="link-effect text-muted mr-10 mb-5 d-inline-block" href="http://odk.milletsodisha.com">
                                            <i class="fa fa-desktop mr-5"></i> ODK Portal Login
                                        </a>
                           
                            </a>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                <!-- END Sign In Form -->
            </div>
        </div>
    </div>
</div>
<!-- END Page Content -->


    </main>
   