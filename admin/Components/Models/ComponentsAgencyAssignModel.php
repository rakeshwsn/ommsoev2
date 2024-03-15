<?php

namespace Admin\Components\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class ComponentsAgencyAssignModel extends Model
{
    protected $table = 'soe_components_agency';
    protected $primaryKey = 'id';

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';

    /**
     * Get component agency data by component ID.
     *
     * @param int $component_id
     * @return array
     */
    public function getComponentAgency(int $component_id): array
    {
        $builder = $this->builder($this->table);
        $builder->where('component_id', $component_id);

        try {
            $res = $builder->get()->getResultArray();
            return $res;
        } catch (DatabaseException $e) {
            // Log the exception or handle it appropriately
            log_message('error', $e->getMessage());
            return [];
        }
    }

    /**
     * Save component agency assignments.
     *
     * @param array $components
     * @param int $fund_agency_id
     * @return void
     */
    public function saveComponentAgency(array $components, int $fund_agency_id): void
    {
        $this->builder($this->table)
             ->where('fund_agency_id', $fund_agency_id)
             ->delete();

        $agency_data = [];

        foreach ($components as $component_id => $component) {
            foreach ($component['agency_id'] as $agency_type_id) {
                $agency_data[] = [
                    'component_id' => $component_id,
                    'agency_type_id' => $agency_type_id,
                    'fund_agency_id' => $fund_agency_id
                ];
            }
        }

        try {
            $this->builder($this->table)->insertBatch($agency_data);
        } catch (DatabaseException $e) {
            // Log the exception or handle it appropriately
            log_message('error', $e->getMessage());
        }
    }
}
