<?php

namespace Front\Api\Models;

use CodeIgniter\Model;

class ApiModel extends Model
{
    // Connection settings
    protected $DBGroup = 'default';
    protected $table = 'apis';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    // Soft deletes
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    // Timestamps
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get XForm data by form ID
     *
     * @param int $form_id The form ID
     *
     * @return array The XForm data
     */
    public function getXform($form_id)
    {
        // Use the query builder to prevent SQL injection attacks
        return $this->builder()
            ->select('*')
            ->from('_form_info fi')
            ->join('_form_info_xform_bin fixb', 'fi._URI = fixb._TOP_LEVEL_AURI', 'left')
            ->where('fi.FORM_ID', $form_id)
            ->get()
            ->getRowArray();
    }
}
