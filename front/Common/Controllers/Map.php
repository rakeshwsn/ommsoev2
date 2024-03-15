<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\URI;

class Map extends Controller
{
    protected $base_url;

    public function __construct()
    {
        parent::__construct();
        $this->base_url = (new URI())->getBaseURL();
    }

    public function index()
    {
        $data = [
            'mapUrl' => $this->base_url . 'common/map/data',
            'svgMap' => view('svg_map'),
            'gps' => [
                'totalGps' => 0,
                'totalVillages' => 0
            ],
            'totalFarmers' => 0
        ];

        return view('map', $data);
    }

    public function plain()
    {
        return view('plain_map');
    }

    public function data()
    {
        // Add your data retrieval and processing logic here
    }
}
