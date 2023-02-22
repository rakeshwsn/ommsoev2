<?php
namespace Admin\Components\Models;
use CodeIgniter\Model;

class ComponentsAssignModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'soe_components_assign';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = '';
    protected $updatedField  = '';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getAssignComponent($fund_agency_id){
        $builder=$this->db->table("{$this->table} sca");
        $builder->select("sca.id,sca.component_id,sca.number,sc.description,sca.parent,sca.sort_order,sca.fund_agency_id,sc.row_type");
        $builder->join("soe_components sc","sca.component_id=sc.id","left");
        $builder->where("sca.fund_agency_id",$fund_agency_id);
        $builder->orderBy('sort_order','ASC');
        return $builder->get()->getResultArray();
    }
    public function getMaxSortorder($id){
        $builder=$this->db->table("{$this->table}");
        $builder->where("fund_agency_id",$id);
        $builder->selectMax('sort_order');
        $result=$builder->get()->getRow();
        return $result->sort_order ;
    }
}
