<?php
namespace Admin\Components\Models;
use CodeIgniter\Model;

class ComponentsAgencyAssignModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'component_agency';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
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

    public function getComponentAgency($component_id){
        $builder=$this->db->table("component_agency ca");
        $builder->where("component_id",$component_id);
        $res = $builder->get()->getResultArray();
        return $res;
    }

    public function saveComponentAgency($components){
        
        $agency_data=[];
        
        foreach ($components as $component_id=>$component) {
            $builder=$this->db->table("component_agency");
            $builder->where("component_id",$component_id);
            $builder->delete();
            
            foreach($component['agency_id'] as $agency_type_id){
                $agency_data[]=array(
                    "component_id"=>$component_id,
                    "agency_type_id"=>$agency_type_id
                );
                
            }
           
            //
        }
        $builder=$this->db->table("component_agency");
        $builder->insertBatch($agency_data);
       // dd($agency_data);
		
    }
   
}
