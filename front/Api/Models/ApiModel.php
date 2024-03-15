<?php

namespace Front\Api\Models;

use CodeIgniter\Model;

class ApiModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'apis';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['column1', 'column2', 'column3']; // add allowed columns here

    // Dates
    protected $useTimestamps        = false;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    public function getXform($form_id)
    {
        // Use query builder instead of raw query
        return $this->builder()
            ->select('*')
            ->from('_form_info fi')
            ->join('_form_info_xform_bin fixb', 'fi._URI = fixb._TOP_LEVEL_AURI', 'left')
            ->where('fi.FORM_ID', $form_id)
            ->get()
            ->getRowArray();
    }
}
