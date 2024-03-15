<?php

namespace Admin\Event\Controllers;

use App\Controllers\AdminController;
use Admin\Event\Models\EventModel;

/**
 * Class Event
 *
 * @package Admin\Event\Controllers
 */
class Event extends AdminController
{
    /**
     * @var EventModel
     */
    private $eventModel;

    /**
     * Event constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->eventModel = new EventModel();
    }

    /**
     * Index method
     */
    public function index()
    {
        $this->template->set_meta_title(lang('Event.heading_title'));

        return $this->getList();
    }

    /**
     * Get list method
     *
     * @return string
     */
    protected function getList()
    {
        // View data
        $data = [
            'breadcrumbs' => [
                [
                    'text' => lang('Event.heading_title'),
                    'href' => admin_url('event')
                ]
            ],
            'add' => admin_url('event/add'),
            'delete' => admin_url('event/delete'),
            'datatable_url' => admin_url('event/search'),
            'heading_title' => lang('Event.heading_title'),
            'text_list' => lang('event.text_list'),
            'text_no_results' => lang('event.text_no_results'),
            'text_confirm' => lang('event.text_confirm'),
            'button_add' => lang('event.button_add'),
            'button_edit' => lang('event.button_edit'),
            'button_delete' => lang('event.button_delete')
        ];

        if (isset($this->error['warning'])) {
            $data['error'] = $this->error['warning'];
        }

        if ($this->request->getPost('selected')) {
            $data['selected'] = (array)$this->request->getPost('selected');
        } else {
            $data['selected'] = array();
        }

        // Load view
        return $this->template->view('Admin\Event\Views\event', $data);
    }

    /**
     * Search method
     *
     * @return \CodeIgniter\HTTP\Response
     */
    public function search()
    {
        $requestData = $_REQUEST;

        $totalData = $this->eventModel->getTotalEvents();
        $totalFiltered = $totalData;

        $filter_data = [
            'filter_search' => $requestData['search']['value'],
            'order' => $requestData['order'][0]['dir'],
            'sort' => $requestData['order'][0]['column'],
            'start' => $requestData['start'],
            'limit' => $requestData['length']
        ];

        $totalFiltered = $this->eventModel->getTotalEvents($filter_data);

        $filteredData = $this->eventModel->getEvents($filter_data);

        $datatable = [];
        foreach ($filteredData as $result) {
            $action = '<div class="btn-group btn-group-sm pull-right">';
            $action .= '<a class="btn btn-sm btn-primary" href="' . admin_url('event/edit/' . $result->id) . '"><i class="fa fa-pencil"></i></a>';
            $action .= '<a class="btn-sm btn btn-danger btn-remove" href="' . admin_url('event/delete/' . $result->id) . '" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
            $action .= '</div>';

            $datatable[] = [
                '<input type="checkbox" name="selected[]" value="' . $result->id . '" />',
                $result->name,
                $result->status ? 'Enabled' : 'Disabled',
                $action
            ];
        }

        $json_data = [
            "draw" => isset($requestData['draw']) ? intval($requestData['draw']) : 1,
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $datatable
        ];

        return $this->response->setContentType('application/json')->setJSON($json_data);
    }

    /**
     * Add method
     *
     * @return \CodeIgniter\HTTP\Response
     */
    public function add()
    {
        $this->template->set_meta_title(lang('Event.heading_title'));

        if ($this->request->getMethod() === 'post' && $this->validateForm()) {

            $filename = $this->request->getFile('report');
            $originalname = $filename->getClientName();

            if ($filename->isValid()) {
                $filename->store();
            }

            $name = $filename->getName();

            $id = $this->eventModel->addEvent($this->request->getPost(), $originalname);

            $this->session->setFlashdata('message', 'Event Saved Successfully.');

            return redirect()->to(admin_url('event'));
        }

        return $this->getForm();
    }

    /**
     * Edit method
     *
     * @return \CodeIgniter\HTTP\Response
     */
    public function edit()
    {
        $this->template->set_meta_title(lang('Event.heading_title'));

        if ($this->request->getMethod() === 'post' && $this->validateForm()) {

            $id = $this->uri->getSegment(4);

            $this->eventModel->editEvent($id, $this->request->getPost());

            $this->session->setFlashdata('message', 'Event Updated Successfully.');

            return redirect()->to(admin_url('event'));
        }

        return $
