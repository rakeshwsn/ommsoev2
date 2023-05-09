<style>
    @import url('https://fonts.googleapis.com/css?family=Roboto:300,400');

* {
    margin:0;
    padding:0
}
a,a:hover{
  text-decoration: none;
}
.myform-area{
  overflow: hidden;
  padding: 60px 0;
  background-image: url('<?php echo base_url() ?>/themes/default/assets/images/modal-bg.jpg');
  position: relative;
  padding-top: 100px;
  padding-bottom: 100px;
  background-position: center;
  height: 100vh;
}
.myform-area .form-area{
  position: relative;
  background: rgba(152, 166, 72);
  width: 100%;
  height: 550px;
  overflow: hidden;
  box-shadow: -1px 0px 7px 2px #e1e1e1;
}

.myform-area .form-area .form-content,
.myform-area .form-area .form-input{
    position: relative;
    width: 50%;
    height: 100%;
    float: left;
    box-sizing: border-box;
}

.myform-area .form-area .form-content{
  width: 50%;
  padding: 40px 30px;
}

.myform-area .form-area .form-content h2{
  color: #fff;
}
.myform-area .form-area .form-content p{
  color: #fff;
}
.myform-area .form-area .form-content ul{
  margin-top: 50px;
}

.myform-area .form-area .form-content ul li{
  display: inline-block;
  margin-right: 10px;
}
.myform-area .form-area .form-content a i{
    margin-right: 10px;
}

.myform-area .form-area .form-content .facebook{
  display: block;
  padding: 10px 20px;
  background: #3B579D;
  color: #fff;
  font-size: 15px;
  text-transform: capitalize;
  border-radius: 4px;
  border: 1px solid #3B579D;
  -webkit-transition: all .5s;
  -o-transition: all .5s;
  transition: all .5s;
}

.myform-area .form-area .form-content .facebook:hover,
.myform-area .form-area .form-content .facebook:focus{
    background: transparent;
}

.myform-area .form-area .form-content .twitter{
  display: block;
   padding: 10px 20px;
   background: #00ACED;
   color: #fff;
   font-size: 15px;
   text-transform: capitalize;
   border-radius: 4px;
   border: 1px solid #00ACED;
   -webkit-transition: all .5s;
   -o-transition: all .5s;
   transition: all .5s;
}

.myform-area .form-area .form-content .twitter:hover,
.myform-area .form-area .form-content .twitter:focus{
    background: transparent;
}
.myform-area .form-area .form-input{
  background-color: white;
  position: relative;
  overflow: hidden;
  box-shadow: 0 0 40px 0 #e1e1e1;
}
.myform-area .form-area .form-input{
    width: 50%;
    background: #fff;
    padding: 40px 30px;
}

.myform-area .form-area .form-input h2{
  margin-bottom: 20px;
    color: #07315B;
}

.myform-area .form-area .form-input input{
    position: relative;
    height: 60px;
    padding: 20px 0;
}
.myform-area .form-area .form-input textarea{
    height: 120px;
    padding: 20px 0;
}

.myform-area .form-area .form-input input,
.myform-area .form-area .form-input textarea{
    text-transform: capitalize;
    width: 100%;
    box-sizing: border-box;
    outline: none;
    border: none;
    border-bottom: 2px solid #e1e1e1;
    color: #07315B;
}
.myform-area .form-area .form-input form .form-group{
    position: relative;
}
.myform-area .form-area .form-input form .form-group label{
    position: absolute;
    text-transform: capitalize;
    top: 20px;
    left: 0;
    pointer-events: none;
    font-size: 14px;
    color: #595959;
    margin-bottom: 0;
    transition: all .6s;
}
.myform-area .form-area .form-input input:focus ~ label,
.myform-area .form-area .form-input textarea:focus ~ label,
.myform-area .form-area .form-input input:valid ~ label,
.myform-area .form-area .form-input textarea:valid ~ label{
    top: -5px;
    opacity: 0;
    left: 0;
    color: rgba(103,58,183);
    font-size: 12px;
    color: #07315B;
    font-weight: bold;
}
.myform-area .form-area .form-input input:focus,
.myform-area .form-area .form-input textarea:focus,
.myform-area .form-area .form-input input:valid,
.myform-area .form-area .form-input textarea:valid{
    border-bottom: 2px solid rgba(103,58,183);
}
.myform-area .form-area .form-text{
    margin-top: 30px;
}
.myform-area .form-area .form-text span a{
    color: rgba(103,58,183);
}
.myform-area .form-area .myform-button{
    margin-top: 30px;
}
.myform-area .form-area .myform-button .myform-btn{
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
.myform-area .form-area .myform-button .myform-btn:hover{
    background: #07315B;
}

</style>
<section class="myform-area">
              <div class="container">
                  <div class="row justify-content-center">
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
                 <!-- <ul>
                                      <li><a href="#" class="facebook"><i class="fa fa-facebook-f"></i><span>facebook</span></a></li>
                                      <li><a href="#" class="twitter"><i class="fa fa-twitter"></i><span>twitter</span></a></li>
                                  </ul> -->
                              </div>
                               <div class="d-flex" style="background: #fff;">
                                <img src="<?php echo base_url() ?>/themes/default/assets/images/Gov. Logo(1).png" alt="" style="height: 50%;/*! height: 155px; */float: left;padding-left: 25px;padding-top: 10px;">
                                <img src="<?php echo base_url() ?>/themes/default/assets/images/OMM New Logo(2).png" alt="" style="height: 50%;/*! height: 155px; */float: left;padding-top: 10px;">
                              

                            </div>

                              <div class="form-input">
                                  <h2>Login</h2>
                                  <?php echo form_open(env('app.adminRoute').'/login',array('class' => 'js-validation-signin', 'id' => 'form-signin','role'=>'form')); ?>
                    <div class="form-group">
                       
                           <input type="text" class="" placeholder="Username" id="login-username" name="username">
                              
            
                    </div>
                    <div class="form-group ">
                        
                            <input type="password" class="" id="password" name="password" placeholder="Password">
                             
                          
                    </div>
                    <!-- <div class="form-group">
                        <div class="col-12">
                            <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="login-remember-me" name="login-remember-me" style="opacity: 0.7;">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Remember Me</span>
                            </label>
                        </div>
                    </div> -->
                    <?php if($login_error) { ?>
                                    <div class="form-group">
                                        <div class="col-12">
                                                <strong class="text-danger pull-left">Error: <?=$login_error?></strong>
                                        </div>
                                    </div>
                                    <?php } ?>
                    <div class="form-group">
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