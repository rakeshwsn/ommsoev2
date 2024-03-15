<?php

namespace Front\Api\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Front\Api\Models\ApiModel;

class Api extends ResourceController
{
    use ResponseTrait;
    private $apiModel;

    public function __construct()
    {
        helper("aio");
        $this->apiModel = new ApiModel();
    }

    public function index()
    {
        $data['message'] = "Welcome to Integrated Farming";
        return $this->respond($data);

