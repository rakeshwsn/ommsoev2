<?php

namespace Admin\FPO\Models;

use CodeIgniter\Model;

class FPOModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'fpo';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'object';
    protected $useSoftDeletes       = false;
    protected $protectFields        = false;
    protected $allowedFields        = [];

    // Dates
    protected $useTimestamps        = false;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    // Validation
    protected $validationRules      = [

    ];
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


    public function getFPOByDistrict(){
        $sql = "SELECT
  d.id,
  d.name,
  d.total_block,
  COALESCE(d1.total_registed, 0) total_register
  /*COALESCE(d2.total_apply,0) total_apply*/
FROM (SELECT
    sd.id,
    sd.name,
    COUNT(sb.id) total_block
  FROM soe_districts sd
    LEFT JOIN soe_blocks sb
      ON sd.id = sb.district_id WHERE sb.is_program=1
  GROUP BY sb.district_id) d

  LEFT JOIN (SELECT
      f.district_id,
      COUNT(f.block_id) total_registed
    FROM fpo f
    WHERE f.registered = 1 GROUP BY f.district_id) d1
    ON d.id = d1.district_id

/*LEFT JOIN (SELECT
      f.district_id,
      COUNT(f.block_id) total_apply
    FROM fpo f
    WHERE f.status = 0) d2
    ON d.id = d2.district_id*/";

        return $this->db->query($sql)->getResultArray();
    }

    public function getFPOStatus(){
        $sql="SELECT 
    count(if(f.registered='1',1,null)) AS registered,
    count(if(f.registered!='1',1,null)) AS apply,
    count(if(f.act='company',1,null)) AS company,
    count(if(f.act='socity',1,null)) as socity
FROM fpo f";
        return $this->db->query($sql)->getRowArray();
    }
    public function getFPOByBlock($district_id){
        $sql="SELECT
  b.id as block_id,
  b.name AS block_name,
  f.id AS fpo_id,
  f.name AS fpo_name,
  f.act AS legal_form,
  f.registered ,
  f.register_status,
  f.other_fpo,
  f.other_block_id,
  f.status
FROM (SELECT
    sb.id,
    sb.name
  FROM soe_blocks sb
  WHERE sb.district_id = $district_id AND sb.is_program=1) b
  LEFT JOIN fpo f
    ON b.id = f.block_id";
        return $this->db->query($sql)->getResultArray();
    }

    public function getFPO($id){
        $builder=$this->db->table("{$this->table} f");
        $builder->select("f.*,d.name district,b.name block");
        $builder->join('soe_districts d', 'f.district_id = d.id');
        $builder->join('soe_blocks b', 'f.block_id = b.id');
        $builder->where("f.id",$id);
        $result = $builder->get()->getRow();
        return $result;
    }

    public function getFPOColumns($fpo_id,$form_type){
      $builder=$this->db->table("fpo_columns fc");
        $builder->select("fc.*,fm.column_id,fm.value");
        $builder->join('fpo_master fm', 'fc.id = fm.column_id and fm.fpo_id='.$fpo_id,'left');
        $builder->where("fc.form_type",$form_type);
        $query = $builder->get();
        $result=$query->getResultArray();
        return $result;
    }

    public function getFPOFormData($fpo_id,$form_group){
        $builder=$this->db->table("fpo_columns fc");
        $builder->select("fc.*,fm.column_id,fm.value");
        $builder->join('fpo_master fm', 'fc.id = fm.column_id and fm.fpo_id='.$fpo_id,'left');
        $builder->where("fc.parent_id",$form_group);
        $query = $builder->get();
        $result=$query->getResultArray();
        return $result;
    }

    public function updateFPODetails($id,$data){

        foreach($data as $column_id=>$value){
            $fdata = [
                'fpo_id' => $id,
                'column_id'  => $column_id,
                'value'  => $value
            ];
            if($this->getFPODetails($id,$column_id)){
                //$fdata['updated_at']=now();
                $builder = $this->db->table('fpo_master');
                $builder->where('column_id', $column_id);
                $builder->where('fpo_id', $id);
                $builder->update($fdata);

            }else{
                //$fdata['created_at']=now();
                $builder = $this->db->table('fpo_master');
                $builder->insert($fdata);
               // echo $this->db->getLastQuery();
            }

        }

    }



    public function getFPODetails($fpo_id,$column_id){
        $builder = $this->db->table('fpo_master fm');
        $builder->where("fm.fpo_id",$fpo_id);
        $builder->where("fm.column_id",$column_id);
        $query   = $builder->get();
        return $query->getResultArray();

    }
}
