<style>
    @import url('https://fonts.googleapis.com/css?family=Roboto:300,400');

    * {
        margin: 0;
        padding: 0
    }

    a,
    a:hover {
        text-decoration: none;
    }

    .myform-area {
        background-image: url('<?php echo base_url() ?>/themes/default/assets/images/modal-bg.jpg');
        background-size: cover;
  background-position: center;
  width: 100%;
  height: 100vh;
    }

    .myform-area .form-area {
        position: relative;
        background: rgba(152, 166, 72);
        width: 100%;
        /* height: 550px; */
        overflow: hidden;
        box-shadow: -1px 0px 7px 2px #e1e1e1;
    }

    .myform-area .form-area .form-content,
    .myform-area .form-area .form-inputt {
        position: relative;
        width: 50%;
        height: 100%;
        float: left;
        box-sizing: border-box;
    }

    .myform-area .form-area .form-content {
        width: 50%;
        padding: 40px 30px;
    }

    .myform-area .form-area .form-content h2 {
        color: #fff;
    }

    .myform-area .form-area .form-content p {
        color: #fff;
    }

    .myform-area .form-area .form-content ul {
        margin-top: 50px;
    }

    .myform-area .form-area .form-content ul li {
        display: inline-block;
        margin-right: 10px;
    }

    .myform-area .form-area .form-content a i {
        margin-right: 10px;
    }

    .myform-area .form-area .form-content .facebook {
        display: block;
        padding: 10px 20px;
        background: #3B579D;
        color: #fff;
        font-size: 15px;
        
        border-radius: 4px;
        border: 1px solid #3B579D;
        -webkit-transition: all .5s;
        -o-transition: all .5s;
        transition: all .5s;
    }

    .myform-area .form-area .form-content .facebook:hover,
    .myform-area .form-area .form-content .facebook:focus {
        background: transparent;
    }

    .myform-area .form-area .form-content .twitter {
        display: block;
        padding: 10px 20px;
        background: #00ACED;
        color: #fff;
        font-size: 15px;
        
        border-radius: 4px;
        border: 1px solid #00ACED;
        -webkit-transition: all .5s;
        -o-transition: all .5s;
        transition: all .5s;
    }

    .myform-area .form-area .form-content .twitter:hover,
    .myform-area .form-area .form-content .twitter:focus {
        background: transparent;
    }

    .myform-area .form-area .form-inputt {
        background-color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 0 40px 0 #e1e1e1;
    }

    .myform-area .form-area .form-inputt {
        width: 50%;
        background: #fff;
        padding: 5px 30px;
    }

    .myform-area .form-area .form-inputt h2 {
        margin-bottom: 20px;
        color: #07315B;
    }

    .myform-area .form-area .form-inputt textarea {
        height: 120px;
        padding: 20px 0;
    }

    .myform-area .form-area .form-inputt input,
    .myform-area .form-area .form-inputt textarea {
        
        width: 100%;
        box-sizing: border-box;
        outline: none;
        border: none;
        border-bottom: 2px solid #e1e1e1;
        color: #07315B;
    }

    .myform-area .form-area .myform-button .myform-btn {
        width: 100%;
        height: 50px;
        font-size: 17px;
        background: rgba(152, 166, 72);
        border: none;
        border-radius: 50px;
        color: #fff;
        cursor: pointer;
        -webkit-transition: all .5s;
        -o-transition: all .5s;
        transition: all .5s;
    }

    .myform-area .form-area .myform-button .myform-btn:hover {
        background: #07315B;
    }
    .myform-area .form-area .form-inputt input, .myform-area .form-area .form-inputt textarea {
        border-bottom: 2px solid #98a648;
    }
</style>
<section class="myform-area">
    <div class="container">
        <div class="d-flex align-items-center justify-content-center" style="height: 100vh;">
            <div class="col-lg-8">
                <div class="form-area login-form">
                    <div class="form-content">
                        <div class="col-sm-12">
                            <!-- <a class="link-effect font-w700" href="index.php">
                        <i class="si si-fire"></i>
                        <span class="font-size-xl text-primary-dark">code</span><span class="font-size-xl">base</span>
                    </a> -->
                            <h5 class="h5 font-w700 mt-0 mb-10 text-center">Goverment of Odisha</h5>
                            <h5 class="h5 font-w700 mt-10 mb-10 text-center">Department of Agriculture & Farmers' Empowerment</h5>
                            <h5 class="h5 font-w700 mt-25 mb-10 text-center" style="padding-top: 30px;font-size: 24px;">Odisha Millets Mission</h5>
                            <p class="text-center" style="color: white !important;*! font-family: font-family: &quot;Times New Roman&quot;, Times, serif; */font-family: &quot;algerian&quot;,;font-family: &quot;Algerian&quot;, Times, serif;font-size: 30px;padding-top: 40px;">SOE &amp; MIS PORTAL</p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center" style="background: #fff;">
                        <img src="<?php echo base_url() ?>/themes/default/assets/images/Gov. Logo(1).png" alt="" style="height: 100px; width: auto; padding-top: 5px;padding-bottom: 5px;">
                        <img src="<?php echo base_url() ?>/themes/default/assets/images/OMM New Logo(2).png" alt="" style="height: 100px; width: auto; padding-top: 5px;padding-bottom: 5px;">
                    </div>


                    <div class="form-inputt">
                        <h2>Login</h2>
                        <?php echo form_open(env('app.adminRoute') . '/login', array('class' => 'js-validation-signin', 'id' => 'form-signin', 'role' => 'form')); ?>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="form-material floating">
                                    <input type="text" class="form-control" id="login-username" name="username">
                                    <label for="login-username">Username</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="form-material floating">
                                    <input type="text" class="form-control" id="password" name="password">
                                    <label for="password">Password</label>
                                </div>
                            </div>
                        </div>
                        <?php if ($login_error) { ?>
                            <div class="form-group">
                                <div class="col-12">
                                    <strong class="text-danger pull-left">Error: <?= $login_error ?></strong>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group mt-30">
                            <?php if ($redirect) { ?>
                                <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
                            <?php } ?>
                            <div class="myform-button">
                                <button class="myform-btn" type="submit">Login</button>
                            </div>
                            <div class="mt-30" style="text-align: center;">
                                <a class="link-effect text-muted mr-10 mb-5 d-inline-block" href="http://odk.milletsodisha.com">
                                    <i class="fa fa-desktop mr-5"></i> ODK Portal Login
                                </a>

                                </a>
                            </div>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>