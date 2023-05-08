<?php
namespace Admin\FPO\Controllers;
use Admin\FPO\Models\FPOModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use App\Controllers\AdminController;
use App\Traits\TreeTrait;
use CodeIgniter\Images\Image;
use Config\Url;

class FPO extends AdminController
{
    use TreeTrait;
    private $fpoModel;
    public function __construct()
    {
        $this->fpoModel = new FPOModel();
        $this->districtModel = new DistrictModel();
        $this->blockModel = new BlockModel();
        helper('fpo');
    }

    public function index(){

        $data['fpo_districts']=$this->fpoModel->getFPOByDistrict();

        if($this->user->district_id){
            $districts=$this->districtModel->getAll(['filter_district'=>$this->user->district_id]);
        }else{
            $districts=$this->districtModel->getAll();
        }


        foreach ($districts as $key=>$district){
           $districts[$key]->fpos=$this->fpoModel->getFPOByBlock($district->id);
        }
        $data['district_id']=$this->user->district_id;
        $data['districts']=$districts;
        $data['fpo_status']=$this->fpoModel->getFPOStatus();
        $data['register_status']=['1'=>'Preliminary work  Under Process','2'=>'Applied for FPO'];

        // printr($data['districts']);
        return $this->template->view('Admin\FPO\Views\index', $data);
    }

    public function add() {

        if(!$this->request->isAJAX()){
            return $this->response->setStatusCode(404);
        }

        $json_data = [
            'status' => false,
        ];

        if($this->request->getMethod(1)=='POST'){
            $this->fpoModel->insert([
                    'district_id'=> $this->request->getPost('district_id'),
                    'block_id' => $this->request->getPost('block_id'),
                    'registered'=> $this->request->getPost('registered'),
                    'register_status'=> $this->request->getPost('register_status'),
                    'other_fpo'=> $this->request->getPost('other_fpo'),
                    'other_block_id'=> $this->request->getPost('other_block_id'),
                    'name' => $this->request->getPost('name'),
                    'act' => $this->request->getPost('act'),
                ]);

            $json_data = [
                'status' => true,
            ];
            $this->session->setFlashData('message','FPO added.');
        } else {

            $json_data = [
                'status' => true,
                'title' => 'Add FPO',
                'html' => $this->getForm()
            ];
        }

        return $this->response->setJSON($json_data);
    }

    public function edit() {
        if(!$this->request->isAJAX()){
            return $this->response->setStatusCode(404);
        }

        $json_data = [
            'status' => false,
        ];

        if($this->request->getMethod(1)=='POST'){

            $id = $this->uri->getSegment(4);

            $this->fpoModel->update($id,[
                'district_id'=> $this->request->getPost('district_id'),
                'block_id' => $this->request->getPost('block_id'),
                'registered'=> $this->request->getPost('registered'),
                'register_status'=> $this->request->getPost('register_status'),
                'other_fpo'=> $this->request->getPost('other_fpo'),
                'other_block_id'=> $this->request->getPost('other_block_id'),
                'name' => $this->request->getPost('name'),
                'act' => $this->request->getPost('act'),

            ]);
            $json_data = [
                'status' => true,
            ];

            $this->session->setFlashdata('message','FPO Updated Successfully');

        } else {

            $json_data = [
                'status' => true,
                'title' => 'Edit FPO',
                'html' => $this->getForm()
            ];
        }

        return $this->response->setJSON($json_data);



        return $this->getForm();


    }

    public function details() {
        $this->template->add_package(['flatpickr','cropper'],true);
        $data=[];
        $id=$data['id']=$this->uri->getSegment(4);
        $data['fpo']=$fpo=$this->fpoModel->getFpo($id);

        if (!empty($fpo->banner) && is_file(DIR_UPLOAD . $fpo->banner)) {
            $data['fpo']->banner = resize($fpo->banner, 1000, 350);
        } else {
            $data['fpo']->banner = resize('no_banner.jpg', 1000, 350);
        }

        if (!empty($fpo->image) && is_file(DIR_UPLOAD . $fpo->image)) {
            $data['fpo']->image = resize($fpo->image, 100, 100);
        } else {
            $data['fpo']->image = resize('no_image.png', 100, 100);
        }

        $data['fpo_basic_columns']=buildFPOTree($this->fpoModel->getFPOColumns($id,'basic'));
        $data['fpo_compliance_columns']=buildFPOTree($this->fpoModel->getFPOColumns($id,'compliance'));


       // printr($data['fpo_basic_columns']);
        //exit;

        //printr($data['fpo']);
        //exit;
        return $this->template->view('\Admin\FPO\Views\details', $data);
    }

    protected function getForm(){

        helper('form');

        $district_id=0;
        if($this->request->getGet('district_id')){
            $district_id = $this->request->getGet('district_id');
        }

        $block_id=0;
        if($this->request->getGet('block_id')){
            $block_id = $this->request->getGet('block_id');
        }

        $fpo_id=0;
        if($this->uri->getSegment(4)){
            $fpo_id = $this->uri->getSegment(4);
            $data['action']=admin_url("fpo/edit/$fpo_id");
        }else{
            $data['action']=admin_url("fpo/add");
        }

        if ($fpo_id && ($this->request->getMethod(true) != 'POST')) {
            $fpo_info = $this->fpoModel->find($fpo_id);
        }


        foreach($this->fpoModel->getFieldNames('fpo') as $field) {
            if($this->request->getPost($field)) {
                $data[$field] = $this->request->getPost($field);
            } else if(isset($fpo_info->{$field}) && $fpo_info->{$field}) {
                $data[$field] = html_entity_decode($fpo_info->{$field},ENT_QUOTES, 'UTF-8');
            } else {
                $data[$field] = '';
            }
        }

        $data['district_id']=$district_id;
        $data['block_id']=$block_id;
        $data['block']=$this->blockModel->find($block_id);
        $data['blocks']=$this->blockModel->where('district_id', $district_id)->findAll();
        $data['acts']=['company'=>'Company','society'=>'Society','trust'=> 'Trust'];
        $data['yes_no']=['1'=>'Yes','0'=>'No'];
        $data['current_status']=['1'=>'Preliminary work  Under Process','2'=>'Applied for FPO'];

        //printr($data);
        return view('\Admin\FPO\Views\form', $data);
    }

    public  function gedit(){
        if(!$this->request->isAJAX()){
            return $this->response->setStatusCode(404);
        }
        $json=[];
        if($this->request->getMethod(1)=='POST'){

            $id = $this->uri->getSegment(4);

            $fpodata=$this->request->getPost();

            //printr($_FILES);
            //printr($_POST);
            $dockey=0;
            if(isset($_FILES['fpodoc']['size'])){
                $dockey=(array_keys($_FILES['fpodoc']['size']))[0];
                if($_FILES['fpodoc']['size'][$dockey]!=0){

                    $validated = $this->validate([
                        'fpodoc' => [
                            'uploaded[fpodoc]',
                            'mime_in[fpodoc,image/jpg,image/jpeg,image/gif,image/png,application/pdf]',
                            'max_size[fpodoc,4096]',
                            'errors' => [
                                'uploaded[fpodoc' => 'Please select a document.'
                            ]
                        ],
                    ]);

                    if (!$validated) {
                        $json = [
                            'errors'=>$this->validator->getErrors(),
                            'status' => false,
                        ];
                    }else{
                        $docs = $this->request->getFileMultiple('fpodoc');
                        foreach($docs as $key=>$doc) {
                            if($doc->isValid() && !$doc->hasMoved()) {
                                $newName = $id."_".$key.".".$doc->getExtension();
                                $doc->move(DIR_UPLOAD. 'fpo', $newName,true);
                                //$fpodoc[]=$doc->getName();
                                $fpodata[$key]=$doc->getName();
                            }
                        }

                    }

                }
            }




            if(!$json){
                $this->fpoModel->updateFPODetails($id,$fpodata);
                $this->session->setFlashdata('message','FPO Details Updated Successfully');
                $json = [
                    'status' => true
                ];
            }

        } else {
            $json = [
                'status' => true,
                'title' => 'Edit',
                'html' => $this->getGForm()
            ];
        }

        return $this->response->setJSON($json);



        //return $this->getGForm();
    }

    public function upload(){
        $fpo_id=$_POST['fpo_id'];
        $target=$_POST['target'];
        $image = $_POST['image'];
        $height=$_POST['height'];
        $width=$_POST['width'];

        $image_array_1 = explode(";", $image);

        $image_array_2 = explode(",", $image_array_1[1]);

        $imagedata = base64_decode($image_array_2[1]);
        $new_image="fpo/".$fpo_id."_".$target.".png";
        $image_name = DIR_UPLOAD . $new_image;
        if($target=="image") {
            $this->fpoModel->update($fpo_id, ['image' => $new_image]);
        }else{
            $this->fpoModel->update($fpo_id, ['banner' => $new_image]);
        }
        file_put_contents($image_name, $imagedata);

        echo resize($new_image,$width,$height,true)."?v=".time();
    }

    protected function getGForm(){

        helper('form');
        $data=[];
        $id=$this->uri->getSegment(4);
        $group = $this->request->getGet('group');
        $data['action']=admin_url("fpo/gedit/$id");
        $data['formdata']=$this->fpoModel->getFPOFormData($id,$group);
        //printr($data['formdata']);

        return view('\Admin\FPO\Views\gform', $data);
    }

}
