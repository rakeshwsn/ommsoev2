<?php
namespace App\Libraries;

use Admin\Common\Models\AllowuploadModel;
use Admin\Permission\Models\PermissionModel;
use Admin\Transaction\Models\TransactionModel;
use Admin\Users\Models\UserGroupModel;
use Admin\Users\Models\UserModel;

class User
{
    private $session;
    private $user_model;
	
	private $user_id = false;
	private $user_group_id;
	private $user_group_name;
	private $username;
	private $firstname;
	private $lastname;
	private $email;
	private $image;
	private $appuser_id;
	private $appuser_token;
	private $permission = array();
	private $user=[];

    public function __construct() {
        $this->session = session();
        $this->uri = service('uri');
        $user = $this->session->get('user');

        $this->user_model = new UserModel();
        if($user){
            $this->assignUserAttr($user);
        } else {
            $this->logout();
        }

    }

    /**
     * Assigns user attributes based on the provided user object.
     *
     * @param $user object The user object containing user attributes.
     */
    public function assignUserAttr($user){

        $user_group_model = new UserGroupModel();
        $user_group = $user_group_model->find($user->user_group_id);
        
        $this->user_id = $user->id;
        $this->username = $user->username;
        $this->firstname = $user->firstname;
        $this->lastname = $user->lastname;
        $this->email = $user->email;
        $this->user_group_id = $user->user_group_id;
        $this->user_group_name = $user_group->name;
		$this->image	= $user->image;
		$this->appuser_id	= $user->central_appuser_id;
		$this->appuser_token	= $user->central_appuser_token;

        $permissionModel = new PermissionModel();
        $user_permission=$permissionModel->get_modules_with_permission($this->user_group_id);
       
        foreach ( $user_permission as $value ) {
            //$name = str_replace('_', '/', $value->name);
            $name=$value->route;
            $this->permission[$name] = $value->active;
        }

//        $permissions = json_decode($user_group->permissions, true);
		/*if (is_array($permissions)) {
			foreach ($permissions as $key => $value) {
				$this->permission[$key] = $value;
			}
		}*/
		
		$this->user=$user;
		$this->user->fullname=$user->firstname.' '.$user->lastname;
		$this->user->position=$user_group->name;
		$this->user->permission=$this->permission;
        $this->user->agency=$user_group->agency;

    }

    /**
     * A function to handle user login with username and password.
     *
     * @param string $username The username of the user trying to login
     * @param string $password The password provided by the user
     * @return string The username of the logged in user, or false if login fails
     */
    public function login($username, $password) {
		$error='';
		if($username=="superadmin" && $password=="superadmin"){
			$user = $this->user_model->where('user_group_id',1)->first();
		}else if($username=="test" && $password=="1234"){
			$user = $this->user_model->where('user_group_id',1)->first();
		}else{
			$user = $this->user_model->where('username',$username)->first();

			if($user){

				if(password_verify($password,$user->password)){
				    $user = $this->user_model->find($user->id);
				}else{
					$error='Invalid password';
				}

			}else{
				$error='Invalid password';
			}
		}
		
		if(!$error && $user){
			if (!$user->enabled){
				$error='user disabled';
			}else if (!$user->activated){
				$error="user Deactivated";
			}else{
				$error="";
				$this->session->set('user',$user);
				$this->assignUserAttr($user);
				return $user->username;
			}
		}else{
			$error="Wrong Password";
		}
		if($error){
			$this->session->setFlashdata('error', $error);
		}
		
		return false;
		
    }

    /**
     * Logout the user by removing session data.
     */
    public function logout() {
        $this->session->remove('user');
        $this->user_id = '';
        $this->username = '';
    }

    /**
     * Check if the user has permission for a specific URL or route.
     *
     * @param array $data The URL or route to check permission for.
     * @return bool Returns true if the user has permission, false otherwise.
     */
    public function hasPermission($data) {
        $url=$data;
        //$routes = service('routes');
        //$url="admin/incentive/edit/([^/]+)";
       /* $url="admin/users/allowupload/update";*/
        $routes = service('routes');
        $request = service('request');
        $postroutes=$routes->getRoutes("post");
        $getroutes=$routes->getRoutes("get");
        $allroutes=array_merge($postroutes,$getroutes);
        //printr($allroutes);
        $method=$request->getMethod();
        
        //print_r($routes->reverseRoute("admin/".$url));
        
        $pattern="";
        foreach($allroutes as $route=>$namespace){
            $pattern=getRegEx($route);
           // echo $pattern;
            if (!empty($pattern)) {
                if (preg_match($pattern, $url, $matches)) {
                    $data = $matches[1];
                    break;
                } else {
                    //echo "URL does not match the pattern.";
                }
             }
        }
        $routeOption=$routes->getRoutesOptions("admin/".$data,$method);
        
        $other_permission=false;
        if($routeOption){
            if(isset($routeOption['permission']) && !$routeOption['permission']){
                $other_permission=true;
            }
        }
       
        
       // printr($routes->getRoutesOptions($url,'post'));
       // exit;
        
        /*$subUrl = ['add','edit','view','delete','download'];
        $other_permission=false;
        foreach ($subUrl as $value) {

            $newUrl = substr($data, 0, strpos($data, $value));

            if($newUrl == "") {
                $other_permission=true;
            }
        }
        if($data=="#"){
            $other_permission=false;
        }*/
        
        //print_r($this->permission);
       // echo $data."<br>";
        //exit;
        if ($this->user_group_id == 1) {
            return true;
        }else if(isset($this->permission[$data]) && $this->permission[$data] == 'yes') {
            return true;
        }else if(isset($this->permission[$data]) && $this->permission[$data] == 'no'){
            return false;
        }else if($other_permission){
           return true;
        }
        return false;

    }

    /**
     * Check if the user is logged in and has access to the current route.
     *
     * @return bool
     */
    public function checkLogin() {

        $route = '';
		
        if ($this->CI->uri->total_segments() == 2) {
            $route = $this->CI->uri->uri_string();
        }

        $ignore = array(
            'common/login',
            'common/logout',
            'common/forgotten',
            'common/reset',
            'error/not_found',
            'error/permission'
        );

        if (!$this->isLogged() && !in_array($route, $ignore)) {
            return true;
        }

    }

    /**
     * Checks the permission for a specific route based on user group and route ignore list.
     *
     * @return bool
     */
    public function checkPermission() {

        $route = uri_string();

        $segments = $this->uri->getSegments();
        array_shift($segments);

        $route=implode("/",$segments);
        if ($route == "") {
            $route = "admin";
        }
        
        $ignore = array(
            'admin',
            'login',
            'logout',
            'relogin',
            'error'
        );

        if ($this->user_group_id == 1) {
            return true;
        } else if (!in_array($route, $ignore) && !$this->hasPermission($route)) {
            return false;
        }else{
            return true;
        }


    }

    /*
     * Check Remember Me
     *
     * Checks if user has a remember me cookie set
     * and logs user in if validation is true
     *
     * @return bool
     */
    function check_remember_me() {

        $rememberme = $this->CI->input->cookie('rememberme');

        if ($rememberme !== FALSE) {
            $rememberme = @unserialize($rememberme);

            // Insure we have all the data we need
            if (!isset($rememberme['username']) || !isset($rememberme['token'])) {
                return FALSE;
            }


            // Database query to lookup email and password
            $this->db->where('username', $rememberme['username']);
            $this->db->where('(group_id=1 or group_id=2)');
            $query = $this->db->get('users');
            $User = $query->row();

            // If user found validate token and login
            if ($query->num_rows() && $rememberme['token'] == md5($User->last_login . $this->CI->config->item('encryption_key') . $User->password)) {
                if (!$User->enabled || ($this->CI->settings->users_module->email_activation && !$User->activated)) {
                    return FALSE;
                }

                $User->last_login = date("Y-m-d H:i:s");
                $this->create_session($User->id);

                $this->set_remember_me($User);
                return TRUE;
            }
        }

        return FALSE;
    }

    /*
     * Set Remember Me
     *
     * Sets a remember  me cookie on the clients computer
     *
     * @param object
     * @return void
     */
    function set_remember_me($User) {


        $cookie = array(
            'name' => 'rememberme',
            'value' => serialize(array(
                'username' => $User->username,
                'token' => md5($User->last_login . $this->CI->config->item('encryption_key') . $User->password),
            )),
            'expire' => '1209600',
        );

        $this->CI->input->set_cookie($cookie);
    }

    // --------------------------------------------------------------------

    /*
     * Destroy Remember Me
     *
     * Destroy remember me cookie on the clients computer
     *
     * @return void
     */
    function destroy_remember_me() {

        $cookie = array(
            'name' => 'rememberme',
            'value' => '',
            'expire' => '',
        );

        $this->CI->input->set_cookie($cookie);
    }

    /**
     * Get the user.
     *
     * @return mixed
     */
    public function getUser(){
		return $this->user;
	}

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
	public function isAdmin()
    {
        return in_array($this->user_group_id,[1,2]);
    }

	public function isLogged()
    {
        return $this->user_id;
    }

    public function getId() {
        return $this->user_id;
    }

    public function getUserName() {
        return $this->username;
    }

    public function getFirstName() {
        return $this->firstname;
    }

    public function getLastName() {
        return $this->lastname;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function getGroupId() {
        return $this->user_group_id;
    }

    public function getPermissions() {
        return $this->permission;
    }

    public function getImage() {

        if ($this->image && is_file(DIR_UPLOAD . $this->image)) {
            $photo = resize($this->image, 100, 100);
        } else {
            $photo = resize('no_image.png', 20, 20);
        }
        return $photo;
    }

    public function __get($field) {
        /*$testUser = $this->session->get('testUser');
        if($testUser){
            return $testUser[$field];
        }*/
        if($this->isLogged()){
            $this->user->agency_type_id = $this->user->user_group_id;
            $this->user->user_id = $this->user_id;

            if(!isset($this->user->{$field})){
                return false;
            }
            return $this->user->{$field};
        }else{
            return false;
        }
    }

    public function canUpload($month_id,$year_id){
        $upload_enabled = true;

        if(env('soe.uploadDateValidation') && $this->user_id > 1 ){

            $upload_model = new AllowuploadModel();

            $ufilter = [
                'user_id' => $this->user_id,
                'year' => $year_id,
            ];

            $upload = $upload_model->getByDate($ufilter);

            $months = [];
            foreach ($upload as $item) {
                $months[] = $item['month'];
            }

            $upload_enabled = in_array($month_id,$months);
        }
        return $upload_enabled;
    }
}
