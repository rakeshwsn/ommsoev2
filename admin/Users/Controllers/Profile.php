<?php

namespace App\Controllers\Admin\Users;

use App\Controllers\AdminController;
use App\Models\UserModel;
use Config\Services;
use Config\Url;

class Profile extends AdminController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $this->template->addPackage(['uploader', 'jquery_loading'], true);

        $userId = $this->user->user_id;
        $user = $this->userModel->find($userId);

        if ($this->request->getMethod() === 'post' && $this->validateForm()) {
            $data = [
                'firstname' => $this->request->getPost('firstname'),
                'email' => $this->request->getPost('email'),
                'phone' => $this->request->getPost('phone'),
                'agency_name' => $this->request->getPost('agency_name'),
            ];

            if ($this->request->getPost('password')) {
                $data['password'] = $this->request->getPost('password');
            }

            $this->userModel->update($userId, $data);

            return redirect()->to(Url::profile())->with('message', 'Profile information updated.');
        }

        $data['profile_image'] = 'https://raticupr.sirv.com/Images/no-image.jpg';
        if ($user->image) {
            $data['profile_image'] = site_url($user->image);
        }

        $data['agency_name'] = $user->agency_name;
        $data['firstname'] = $user->firstname;
        $data['email'] = $user->email;
        $data['phone'] = $user->phone;
        $data['district_id'] = $user->district_id;
        $data['block_id'] = $user->block_id;

        $districtModel = new \Admin\Localisation\Models\DistrictModel();
        $blockModel = new \Admin\Localisation\Models\BlockModel();

        $data['district'] = '';
        if ($user->district_id) {
            $data['district'] = $districtModel->find($user->district_id)->name;
        }

        $data['block'] = '';
        if ($user->block_id) {
            $data['block'] = $blockModel->find($user->block_id)->name;
        }

        $data['validation'] = Services::validation();

        $data['upload_url'] = site_url(Url::profileUpload());

        return $this->template->view('Admin\Users\Views\profile', $data);
    }

    public function upload()
    {
        $input = $this->validate([
            'file' => [
                'uploaded[file]',
                'mime_in[file,image/jpg,image/jpeg,image/png,image/JPG,image/JPEG,image/PNG]',
                'max_size[file,1024]',
                'ext_in[file,jpg,png,jpeg,JPG,PNG,JPEG]',
            ]
        ]);

        $filepath = 'images/profile/';

        if (!$input) {
            $data = [
                'status' => false,
                'message' => 'Invalid file',
                'errors' => $this->validator->getErrors()
            ];
        } else {
            $file = $this->request->getFile('file');
            $file->move($filepath);

            $data = [
                'status' => true,
                'image' => base_url($filepath . $file->getName()),
                'filename' => $file->getName(),
                'filepath' => $filepath . $file->getName()
            ];

            $this->userModel->update($this->user->user_id, ['image' => $filepath . $file->getName()]);
        }

        return $this->response->setJSON($data);
    }

    protected function validateForm()
    {
        if ($this->request->getPost('password')) {
            $rules = [
                'password' => [
                    'label' => 'Password',
                    'rules' => 'min_length[5]|max_length[20]',
                ],
                'password_confirm' => [
                    'label' => 'Confirm Password',
                    'rules' => 'matches[password]',
                ],
            ];

            if ($this->validate($rules)) {
                return true;
            } else {
                $this->error['warning'] = "Warning: Please check the form carefully for errors!";
                session()->setFlashdata('message', $this->error['warning']);
                return false;
            }
        }

        return !$this->error;
    }
}
