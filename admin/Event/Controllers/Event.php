<?php

namespace Admin\Event\Controllers;

use App\Controllers\AdminController;
use Admin\Event\Models\EventModel;

/**
 * Created by Wassan.
 * User: Niranjan
 * Date: 18-12-2021
 * Time: 03:11 PM
 */
class Event extends AdminController
{

    private $error = array();
    private $eventModel;
    function __construct()
    {
        $this->eventModel = new EventModel();
    }
    public function index()
    {
        $this->template->set_meta_title(lang('Event.heading_title'));
        return $this->getList();
    }

    protected function getList()
    {

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => lang('Event.heading_title'),
            'href' => admin_url('event')
        );

        $this->template->add_package(array('datatable'), true);

        $data['add'] = admin_url('event/add');
        $data['delete'] = admin_url('event/delete');
        $data['datatable_url'] = admin_url('event/search');

        $data['heading_title'] = lang('Event.heading_title');

        $data['text_list'] = lang('event.text_list');
        $data['text_no_results'] = lang('event.text_no_results');
        $data['text_confirm'] = lang('event.text_confirm');
        $data['button_add'] = lang('event.button_add');
        $data['button_edit'] = lang('event.button_edit');
        $data['button_delete'] = lang('event.button_delete');

        if (isset($this->error['warning'])) {
            $data['error'] = $this->error['warning'];
        }

        if ($this->request->getPost('selected')) {
            $data['selected'] = (array) $this->request->getPost('selected');
        } else {
            $data['selected'] = array();
        }

        return $this->template->view('Admin\Event\Views\event', $data);
    }

    public function search()
    {
        $requestData = $_REQUEST;
        $totalData = $this->eventModel->getTotalEvents();
        $totalFiltered = $totalData;

        $filter_data = array(
            'filter_search' => $requestData['search']['value'],
            'order' => $requestData['order'][0]['dir'],
            'sort' => $requestData['order'][0]['column'],
            'start' => $requestData['start'],
            'limit' => $requestData['length']
        );
        $totalFiltered = $this->eventModel->getTotalEvents($filter_data);

        $filteredData = $this->eventModel->getEvents($filter_data);

        $datatable = array();
        foreach ($filteredData as $result) {

            $action = '<div class="btn-group btn-group-sm pull-right">';
            $action .= '<a class="btn btn-sm btn-primary" href="' . admin_url('event/edit/' . $result->id) . '"><i class="fa fa-pencil"></i></a>';
            $action .= '<a class="btn-sm btn btn-danger btn-remove" href="' . admin_url('event/delete/' . $result->id) . '" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
            $action .= '</div>';

            $datatable[] = array(
                '<input type="checkbox" name="selected[]" value="' . $result->id . '" />',
                $result->name,
                $result->status ? 'Enabled' : 'Disabled',
                $action
            );
        }
        //printr($datatable);
        $json_data = array(
            "draw" => isset($requestData['draw']) ? intval($requestData['draw']) : 1,
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $datatable
        );
        return $this->response->setContentType('application/json')
            ->setJSON($json_data);
    }

    public function add()
    {
        $this->template->set_meta_title(lang('Event.heading_title'));

        if ($this->request->getMethod(1) === 'POST' && $this->validateForm()) {

            $filename = $this->request->getFile('report');
            $originalname = $filename->getClientName();


            if ($filename->isValid()) {
                $filename->store();
            }

            $name = $filename->getName();
            // $temp_name = $filename->getTempName();
            // $arr_file = explode(".",$name);
            // $extension = end($arr_file);

            $id = $this->eventModel->addEvent($this->request->getPost(), $originalname);
            $this->session->setFlashdata('message', 'Event Saved Successfully.');

            return redirect()->to(admin_url('event'));
        }
        $this->getForm();
    }

    public function edit()
    {
        $this->template->set_meta_title(lang('Event.heading_title'));

        if ($this->request->getMethod(1) === 'POST' && $this->validateForm()) {
            $id = $this->uri->getSegment(4);

            $this->eventModel->editEvent($id, $this->request->getPost());
            $this->session->setFlashdata('message', 'Event Updated Successfully.');

            return redirect()->to(admin_url('event'));
        }
        $this->getForm();
    }

    public function delete()
    {
        if ($this->request->getPost('selected')) {
            $selected = $this->request->getPost('selected');
        } else {
            $selected = (array) $this->uri->getSegment(4);
        }
        $this->eventModel->deleteEvent($selected);

        //$this->slugModel->whereIn('route_id', $selected)->delete();

        $this->session->setFlashdata('message', 'Event deleted Successfully.');
        return redirect()->to(admin_url('event'));
    }

    protected function getForm()
    {

        $this->template->add_package(array('ckeditor', 'ckfinder', 'tablednd', 'colorbox'), true);

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => lang('Event.heading_title'),
            'href' => admin_url('event')
        );

        $_SESSION['isLoggedIn'] = true;

        $data['heading_title'] = lang('Event.heading_title');
        $data['text_form'] = $this->uri->getSegment(3) ? "Event Add" : "Event Edit";
        $data['text_image'] = lang('Event.text_image');
        $data['text_none'] = lang('Event.text_none');
        $data['text_clear'] = lang('Event.text_clear');
        $data['cancel'] = admin_url('event');


        $data['button_save'] = lang('Event.button_save');
        $data['button_cancel'] = lang('Event.button_cancel');


        if (isset($this->error['warning'])) {
            $data['error'] = $this->error['warning'];
        }

        if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
            $event_info = $this->eventModel->getEvent($this->uri->getSegment(4));
        }
        
        foreach ($this->eventModel->getFieldNames('events') as $field) {
            if ($this->request->getPost($field)) {
                $data[$field] = $this->request->getPost($field);
            } else if (isset($event_info->{$field}) && $event_info->{$field}) {
                $data[$field] = html_entity_decode($event_info->{$field}, ENT_QUOTES, 'UTF-8');
            } else {
                $data[$field] = '';
            }
        }


        // Images
        if ($this->request->getPost('event_image')) {
            $event_images = $this->request->getPost('event_image');
        } elseif ($this->uri->getSegment(4)) {
            $event_images = $this->eventModel->getEventImages($this->uri->getSegment(4));
        } else {
            $event_images = array();
        }

        $data['event_images'] = array();
        // printr($event_images);exit;
        foreach ($event_images as $event_image) {
            if (is_file(DIR_UPLOAD . $event_image['image'])) {
                $image = $event_image['image'];
                $thumb = $event_image['image'];
            } else {
                $image = '';
                $thumb = 'no_image.png';
            }

            $data['event_images'][] = array(
                'image' => $image,
                'thumb' => resize($thumb, 100, 100),
                'title' => $event_image['title'],
                'link' => $event_image['link'],
                'description' => $event_image['description']
            );
        }
        $data['no_image'] = resize('no_image.png', 100, 100);

        echo $this->template->view('Admin\Event\Views\eventForm', $data);
    }

    protected function validateForm()
    {

        $validation = \Config\Services::validation();
        $id = $this->uri->getSegment(4);
// printr($_POST);
// printr($_FILES);
// exit;
        $rules = $this->eventModel->validationRules;

        if ($this->validate($rules)) {
            return true;
        } else {
            printr($validation->getErrors());
            $this->error['warning'] = "Warning: Please check the form carefully for errors!";
            return false;
        }
        return !$this->error;
    }
}
