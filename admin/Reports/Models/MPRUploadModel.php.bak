<?php 
namespace Admin\Reports\Models;
use CodeIgniter\Model;

class MPRUploadModel extends Model {
    protected $DBGroup              = 'default';
    protected $table                = 'soe_mpr_uploads';
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

    public function getMPRUploads($filter=[]) {
        if(empty($filter['district_id'])){
            $filter['district_id'] = 0;
        }
        if(empty($filter['district_id'])){
            $filter['fund_agency_id'] = 0;
        }
        if(empty($filter['year_id'])){
            $filter['year_id'] = getCurrentYearId();
        }

        $sql = "SELECT
        vaym.year_id,
        vaym.month_id,
        vaym.year,
        vaym.month,
        smu.file,
        smu.created_at
        FROM vw_all_year_month vaym
        LEFT JOIN (SELECT
            *
            FROM soe_mpr_uploads
            WHERE district_id = '".$filter['district_id']."'
            AND fund_agency_id = '".$filter['fund_agency_id']."') smu
            ON vaym.year_id = smu.year
            AND vaym.month_id = smu.month
        WHERE vaym.year_id = '".$filter['year_id']."'";
        
        return $this->db->query($sql)->getResult();
    }

    public function getMPRByDistricts($filter=[]){
        $sql="
        SELECT
            sd.id,
            sd.name,
            smu.year,
            smu.month,
            smu.file,
            smu.fund_agency_id,
            smu.created_at
            FROM (SELECT
                sd.id,
                sd.name
            FROM soe_districts sd
                LEFT JOIN soe_blocks sb
                ON sd.id = sb.district_id
            WHERE sb.fund_agency_id = '".$filter['fund_agency_id']."'
            GROUP BY sb.district_id) sd
            LEFT JOIN (SELECT
                *
                FROM soe_mpr_uploads
                WHERE year ='".$filter['year_id']."'
                AND month = '".$filter['month_id']."'
                AND deleted_at IS NULL) smu
                ON sd.id = smu.district_id";
        return $this->db->query($sql)->getResult();  
    }
}