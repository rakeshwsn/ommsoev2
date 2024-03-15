<?php

namespace Front\Pages\Controllers;

use Admin\Pages\Models\PagesModel;
use App\Controllers\BaseController;
use App\Exceptions\PageNotFoundException;

class Page extends BaseController
{
    private PagesModel $pageModel;
    private \CodeIgniter\HTTP\URI $uri;
    private \App\Models\User $user;
    private \Config\App $settings;
    private \App\Libraries\Template $template;

    public function __construct()
    {
        parent::__construct();

        $this->pageModel = new PagesModel();
        $this->uri = service('uri');
        $this->user = service('user');
        $this->settings = service('settings');
        $this->template = service('template');
    }

    public function index(int $id = 0): void
    {
        $Page = $this->getPage($id);

        $this->renderPage($Page, true);
    }

    public function info(int $id = 0): void
    {
        $Page = $this->getPage($id);

        $this->renderPage($Page, false);
    }

    private function getPage(int $id): ?object
    {
        $Page = $this->pageModel->find($id);

        if ($Page === null || ($Page->status !== 'published' && ($Page->status !== 'draft' || !$this->user->isLogged()))) {
            throw new PageNotFoundException();
        }

        return $Page;
    }

    private function renderPage(?object $Page, bool $displayHeader): void
    {
        if ($Page === null) {
            throw new PageNotFoundException();
        }

        $this->template->set('page_id', $Page->id);
        $data['heading_title'] = $Page->title;
        $data['meta_title'] = $Page->meta_title;
        $data['content'] = do_shortcode(html_entity_decode($Page->content, ENT_QUOTES, 'UTF-8'));

        if ($Page->feature_image && is_file(DIR_UPLOAD . $Page->feature_image)) {
            $data['feature_image'] = base_url('uploads') . '/' . $Page->feature_image;
        } else {
            $data['feature_image'] = '';
        }

        $this->template->setMetaDescription($Page->meta_description)
            ->setMetaKeywords($Page->meta_keywords)
            ->setMetaTitle($Page->meta_title);

        $this->template->setLayout($Page->layout);
        $this->template->setSiteName($this->settings->config_site_title);

        $data['layout'] = $Page->layout;

        $this->template->setHeader($displayHeader);

        $this->template->view('Front\Pages\Views\page', $data);
    }
}
