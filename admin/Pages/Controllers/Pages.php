<?php

namespace Admin\Pages\Controllers;

use Admin\Pages\Models\PagesModel;
use App\Controllers\AdminController;
use Admin\Pages\Models\SlugModel;
use Config\Services;
use Config\Settings;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;

class Pages extends AdminController
{
    use ResponseTrait;

    private PagesModel $pagesModel;
    private SlugModel $slugModel;
    private array $error = [];

    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        parent::__construct($request, $response);

        $this->pagesModel = new PagesModel();
        $this->slugModel = new SlugModel();
    }

    public function index(): string
    {
        return $this->getList();
    }

    public function add(): string
    {
        return $this->processForm('add');
    }

    public function edit(?int $id = null): string
    {
        return $this->processForm('edit', $id);
    }

    public function delete(): string
    {
        $selected = $this->request->getPost('selected') ?? (array) $this->uri->getSegment(4);

        $this->pagesModel->delete($selected);
        $this->slugModel->whereIn('route_id', $selected)->delete();

        $this->setFlashdata('message', 'Page deleted successfully.');

        return $this->response->redirect(site_url('admin/pages'));
    }

    protected function getList(): string
    {
        $data = [
            'breadcrumbs' => [
                [
                    'text' => lang('Pages.heading_title'),
                    'href' => admin_url('pages')
                ],
            ],
            'add' => admin_url('pages/add'),
            'delete' => admin_url('pages/delete'),
            'datatable_url' => admin_url('pages/search'),
            'heading_title' => lang('Pages.heading_title'),
            'text_list' => lang('Pages.text_list'),
            'text_no_results' => lang('Pages.text_no_results'),
            'text_confirm' => lang('Pages.text_confirm'),
            'button_add' => lang('Pages.button_add'),
            'button_edit' => lang('Pages.button_edit'),
            'button_delete' => lang('Pages.button_delete'),
        ];

        if (isset($this->error['warning'])) {
            $data['error'] = $this->error['warning'];
        }

        if ($this->request->getPost('selected')) {
            $data['selected'] = (array) $this->request->getPost('selected');
        } else {
            $data['selected'] = [];
        }

        return $this->template->view('Admin\Pages\Views\page', $data);
    }

    public function search(): ResponseInterface
    {
        $requestData = $this->request->getGet();

        $totalData = $this->pagesModel->getTotalPages();
        $totalFiltered = $totalData;

        $filter_data = [
            'filter_search' => $requestData['search']['value'] ?? '',
            'order' => $requestData['order'][0]['dir'] ?? 'DESC',
            'sort' => $requestData['order'][0]['column'] ?? 'id',
            'start' => $requestData['start'] ?? 0,
            'limit' => $requestData['length'] ?? 20,
        ];

        $totalFiltered = $this->pagesModel->getTotalPages($filter_data);

        $filteredData = $this->pagesModel->getPages($filter_data);

        $datatable = [];

        foreach ($filteredData as $result) {
            $action = '<div class="btn-group btn-group-sm pull-right">';
            $action .= '<a class="btn btn-sm btn-primary" href="' . admin_url('pages/edit/' . $result->id) . '"><i class="fa fa-pencil"></i></a>';
            $action .= '<a class="btn-sm btn btn-danger btn-remove" href="' . admin_url('pages/delete/' . $result->id) . '" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
            $action .= '</div>';

            $datatable[] = [
                '<input type="checkbox" name="selected[]" value="' . $result->id . '" />',
                $result->title,
                base_url($result->slug),
                $result->layout,
                $result->status,
                $action,
            ];
        }

        return $this->respond([
            'draw' => $requestData['draw'] ?? 1,
            'recordsTotal' => $totalData,

