<?php

declare(strict_types=1);

namespace Admin\Components\Controllers;

use Admin\Components\Models\ComponentsModel;
use Admin\Localisation\Models\BlockModel;
use App\Controllers\AdminController;
use App\Traits\TreeTrait;
use Config\Services;

class Components extends AdminController
{
    use TreeTrait;

    private ComponentsModel $componentsModel;
    private array $error = [];

    public function __construct()
    {
        $this->componentsModel = new ComponentsModel();
    }

    public function index(): string
    {
        $this->template->set_meta_title('Components');
        return $this->getList();
    }

    protected function getList(): string
    {
        // ...

        return $this->template->view('Admin\Components\Views\components', $data);
    }

    public function search(): string
    {
        // ...

        return Services::response()->setContentType('application/json')->setJSON($json_data);
    }

    public function add(): string
    {
        // ...

        return $this->template->view('Admin\Components\Views\componentForm', $data);
    }

    public function edit(?int $id = null): string
    {
        // ...

        return $this->template->view('Admin\Components\Views\componentForm', $data);
    }

    public function delete(): string
    {
        // ...

        return Services::response()->setJSON(['status' => true]);
    }

    public function phase(int $fund_agency_id): string
    {
        // ...

        return Services::response()->setJSON($phases);
    }

    public function autocomplete(): string
    {
        // ...

        return Services::response()->setJSON($json);
    }

    protected function getForm(): string
    {
        // ...

        return $this->template->view('Admin\Components\Views\componentForm', $data);
    }

    protected function validateForm(): bool
    {
        // ...

        return !$this->error;
    }

    private function update_position(int $parent, array $children): void
    {
        // ...
    }
}
