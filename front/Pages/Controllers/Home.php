<?php

namespace Front\Pages\Controllers;

use Admin\Pages\Models\PagesModel;
use Admin\Banner\Models\BannerModel;
use App\Controllers\BaseController;

class Home extends BaseController
{
    /**
     * @var PagesModel
     */
    private $pageModel;

    /**
     * @var BannerModel
     */
    private $bannerModel;

    /**
     * Home constructor.
     * @param PagesModel $pageModel
     * @param BannerModel $bannerModel
     */
    public function __construct(PagesModel $pageModel, BannerModel $bannerModel)
    {
        $this->pageModel = $pageModel;
        $this->bannerModel = $bannerModel;
    }

    /**
     * @return string
     */
    public function index(): string
    {
        $data = [];

        $page = $this->pageModel->find(1) ?? [];

        if (!empty($page)) {
            if ($page['status'] !== 'published') {
                if ($page['status'] !== 'draft') {
                    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
                }
            }

            $this->template->set_meta_title($page['title']);
            $data['content'] = html_entity_decode($page['content'], ENT_QUOTES, 'UTF-8');
            $this->template->set('site_name', trim($this->settings->config_site_title));
        }

        $data['banners'] = array_map(fn ($banner) => [
            'image' => upload_url($banner['image']),
            'title' => $banner['title'],
            'link' => $banner['link'],
            'description' => $banner['description'],
        ], $this->bannerModel->getBannerImages(3) ?? []);

        $data['sliders'] = array_map(fn ($slider) => [
            'image' => upload_url($slider['image']),
            'title' => $slider['title'],
            'link' => $slider['link'],
            'description' => $slider['description'],
        ], $this->bannerModel->getBannerImages(4) ?? []);

        $this->template->add_package(['lightgallery'], true);
        $this->template->add_javascript('themes/default/assets/js/plugins.js');
        $this->template
