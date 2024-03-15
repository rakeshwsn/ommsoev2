<?php

namespace Front\Api\Controllers;

use Admin\Localisation\Models\DistrictModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\ClusterBlockModel;
use Admin\Localisation\Models\ClusterModel;
use Admin\Localisation\Models\GrampanchayatModel;
use Admin\Forms\Models\HouseholdModel;
use Admin\Forms\Models\AggricultureModel;
use Admin\Forms\Models\HorticultureModel;
use Admin\Users\Models\MemberModel;
use Admin\Localisation\Models\VillageModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Front\Api\Models\ApiModel;

class Api extends ResourceController
{
    use ResponseTrait;
    private $apiModel;
    private $user;
    private $odk;

    public function __construct()
    {
        helper("aio");
        $this->apiModel = new ApiModel();
        $this->user = service('user');
        $this->odk = service('odkcentral');
    }

    public function index()
    {
        $data['message'] = "Welcome to Integrated Farming";
        return $this->respond($data);
    }

    public function submission()
    {
        $json = [
            "instanceId" => "uuid:85cb9aff-005e-4edd-9739-dc9c1a829c44",
            "instanceName" => "village third house",
            "submitterId" => 23,
            "deviceId" => "imei:123456",
            "reviewState" => "approved",
            "createdAt" => "2
