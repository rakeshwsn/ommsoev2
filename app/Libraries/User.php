<?php

namespace App\Libraries;

use Admin\Permission\Models\PermissionModel;
use Admin\Users\Models\UserGroupModel;
use Admin\Users\Models\UserModel;

class User
{
    /**
     * @var \CodeIgniter\Session\Session
     */
    private $session;

    /**
     * @var UserModel
     */
    private $userModel;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $firstname;

    /**
     * @var string
     */
    private $lastname;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $image;

    /**
     * @var int
     */
    private $userGroupId;

    /**
     * @var string
     */
    private $userGroupName;

    /**
     * @var string
     */
    private $appuserId;

    /**
     * @var string
     */
    private $appuserToken;

    /**
     * @var array
     */
    private $permission;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->session = session();
        $this->userModel = new UserModel();

        $user = $this->session->get('user');

        if ($user) {
            $this->assignUserAttr($user);
        } else {
            $this->logout();
        }
    }

    /**
     * @param array $user
     */
    private function assignUserAttr(array $user): void
    {
        $userGroupModel = new UserGroupModel();
        $userGroup = $userGroupModel->find($user['user_group_id']);

        $this->id = $user['id'];
        $this->username = $user['username'];
        $this->firstname = $user['firstname'];
        $this->lastname = $user['lastname'];
        $this->email = $user['email'];
        $this->userGroupId = $user['user_group_id'];
        $this->userGroupName = $userGroup['name'];
        $this->image = $user['image'];
        $this->appuserId = $user['central_appuser_id'];
        $this->appuserToken = $user['central_appuser_token'];

        $permissionModel = new PermissionModel();
        $userPermissions = $permissionModel->get_modules_with_permission($this->userGroupId);

        foreach ($userPermissions as $permission) {
            $this->permission[$permission->name] = $permission->active;
        }
    }

    /**
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function login(string $username, string $password): bool
    {
        // ...
    }

    /**
     *
     */
    public function logout(): void
    {
        // ...
    }

    /**
     * @param string $data
     * @return bool
     */
    public function hasPermission(string $data): bool
    {
        // ...
    }

    /**
     * @return bool
     */
    public function checkLogin(): bool
    {
        // ...
    }

    /**
     * @return bool
     */
    public function checkPermission(): bool
    {
        // ...
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return "{$this->firstname} {$this->lastname}";
    }

    /**
     * @return array
     */
    public function getUserData(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'image' => $this->image,
            'appuserId' => $this->appuserId,
            'appuserToken' => $this->appuserToken,
            'userGroupId' => $this->userGroupId,
            'userGroupName' => $this->userGroupName,
        ];
    }

    /**
     * @return array
     */
    public function getGroupData(): array
    {
        return [
            'id' => $this->userGroupId,
            'name' => $this->userGroupName,
        ];
    }

    /**
     * @return array
     */
    public function getPermissionsData(): array
    {
        return $this->permission;
    }

    /**
     * @return string
     */
    public function getImagePath(): string
    {
        return DIR_UPLOAD
