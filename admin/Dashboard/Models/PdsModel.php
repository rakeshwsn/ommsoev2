<?php
namespace Admin\Dashboard\Models;
use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class PdsModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'dashboard_pds';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = true;
	protected $protectFields        = false;
	protected $allowedFields        = [];

	// Dates
	protected $useTimestamps        = true;
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
	protected $bulider;
	
	public function getAll($filter){
		$sql="SELECT
		pd.id pds_id,
		pd.distributed_year_id,
		y.name year,
		pd.district_id,
		d.name district,
		COUNT(pd.district_id) districts_covered,
		SUM(pd.quantity) total_qty,
		SUM(pd.card_holders_benifited) total_chb,
		pd.quantity,
		pd.card_holders_benifited,
		pd.created_at
	  FROM dashboard_pds pd
		LEFT JOIN dashboard_years y
		  ON pd.distributed_year_id = y.id	 
		  LEFT JOIN soe_districts d
	  ON pd.district_id = d.id
	  WHERE pd.deleted_at IS NULL
	  AND y.deleted_at IS NULL
	  AND pd.quantity > 0 ";
	   if(isset($filter['distributed_year_id'])){
		$sql .= " AND pd.distributed_year_id = ".$filter['distributed_year_id'];
	  }
	  $sql .=  " GROUP BY pd.distributed_year_id";
	  return $this->db->query($sql)->getResult();
	}
	public function getDistrictWise($filter = [])
	{
		$sql = "SELECT
		pd.id pds_id,
		pd.distributed_year_id,
		y.name year,
		pd.district_id,
		d.name district,
		pd.quantity,
		pd.card_holders_benifited,
		pd.created_at
	  FROM dashboard_pds pd
		LEFT JOIN dashboard_years y
		  ON pd.distributed_year_id = y.id
		LEFT JOIN soe_districts d
		  ON pd.district_id = d.id
	  WHERE pd.deleted_at IS NULL
	  AND y.deleted_at IS NULL";
	   if(isset($filter['distributed_year_id'])){
		$sql .= " AND pd.distributed_year_id = ".$filter['distributed_year_id'];
	  }
		$sql .=  " ORDER BY d.name";

		return $this->db->query($sql)->getResult();
	}
    
	public function getYearwisepds() {
		$sql = "SELECT
		p.distributed_year_id,
		y.name `year`,
		p.quantity,
		p.card_holders_benifited,
		SUM(p.quantity) total_quantity,
		SUM(p.card_holders_benifited) total_chb
	  FROM dashboard_pds p
		LEFT JOIN dashboard_years y
		  ON y.id = p.distributed_year_id
	  WHERE p.deleted_at IS NULL";
	 
	  $sql .= " GROUP BY p.distributed_year_id
	  ORDER BY y.start_date";

		return $this->db->query($sql)->getResult();
	}

	
}
