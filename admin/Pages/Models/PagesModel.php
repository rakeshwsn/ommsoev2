<?php

namespace Admin\Pages\Models;

use CodeIgniter\Model;

class PagesModel extends Model
{
    // ... (other properties)

    /**
     * @var \CodeIgniter\Database\BaseBuilder
     */
    protected $builder;

    // ... (other methods)

    /**
     * Filters the query builder based on the provided data.
     *
     * @param \CodeIgniter\Database\BaseBuilder $builder
     * @param array                             $data
     */
    private function filter(BaseBuilder $builder, array $data): void
    {
        if (!empty($data['filter_search'])) {
            $builder->where("p.title LIKE '%" . $data['filter_search'] . "%'");
        }
    }

    /**
     * Deletes pages based on the selected IDs.
     *
     * @param array $selected
     */
    public function deletePage(array $selected): void
    {
        if (empty($selected)) {
            return;
        }

        $this->where_in("id", $selected);
        $this->delete("pages");

        $this->where("route", "pages/index/%d", $selected[0]);
        $this->delete("slug");
    }

    // ... (other methods)
}
