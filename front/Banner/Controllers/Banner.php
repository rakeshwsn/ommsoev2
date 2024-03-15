<?php

namespace Front\Banner\Controllers;

use Admin\Banner\Models\BannerModel;
use App\Controllers\BaseController;

class Banner extends BaseController
{
    /**
     * @var BannerModel
     */
    private $bannerModel;

    public function __construct()
    {
        $this->bannerModel = new BannerModel();
    }

    /**
     * @param array $attr
     * @return string
     */
    public function getGallery(array $attr): string
    {
        $this->template->addPackage(['lightgallery'], true);
        $data = $this->prepareBannerData($this->bannerModel->getBannerImages($attr['id'] ?? 0), 'gallery');

        return $this->template->view('Front\Banner\Views\gallery', $data, true);
    }

    /**
     * @param array $attr
     * @return string
     */
    public function getSlider(array $attr): string
    {
        $data = $this->prepareBannerData($this->bannerModel->getBannerImages(3), 'slider');

        return $this->template->view('Front\Banner\Views\slider', $data, true);
    }

    /**
     * @param array $bannerImages
     * @param string $templateName
     * @return array
     */
    private function prepareBannerData(array $bannerImages, string $templateName): array
    {
        $data = ['sliders' => []];

        foreach ($bannerImages as $bannerImage) {
            $data['sliders'][] = [
                'image' => upload_url($bannerImage['image']),
                'title' => $bannerImage['title'],
                'link' => $bannerImage['link'],
                'description' => $bannerImage['description'],
            ];
        }

        return $data;
    }
}
