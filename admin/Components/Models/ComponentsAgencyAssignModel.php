<?php

namespace Admin\Components\Models;

use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Model;

class ComponentsAgencyAssignModel extends Model
{
    protected $table = 'soe_components_agency';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';

    public function __construct()
    {
        parent::__construct();
        $db = \Config\Database::connect();
        $this->builder($this->table, $db);
    }

    /**
     * Get component agency data by component ID.
     *
     * @param int $component_id
     * @return array
     */
    public function getComponentAgency(int $component_id): array
    {
        $builder = $this->builder();
        $builder->where('component_id', $component_id);

        try {
            $res = $builder->get()->getResultArray();
            return $res;
        } catch (DatabaseException $e) {
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
        $this->deleteByFundAgencyId($fund_agency_id);

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
            $this->insertBatch($agency_data);
        } catch (DatabaseException $e) {
            log_message('error', $e->getMessage());
        }
    }

    /**
     * Delete records by fund agency ID.
     *
     * @param int $fund_agency_id
     * @return void
     */
    private function deleteByFundAgencyId(int $fund_agency_id): void
    {
        $builder = $this->builder();
        $builder->where('fund_agency_id', $fund_agency_id);

        try {
            $builder->delete();
        } catch (DatabaseException $e) {
            log_message('error', $e->getMessage());
        }
    }
}
