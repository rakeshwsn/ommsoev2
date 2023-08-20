<?php

namespace Admin\Common\Models;

use CodeIgniter\Model;

class AllowuploadModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'soe_allow_uploads';
	protected $primaryKey           = 'upload_id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes        = false;
	protected $protectFields        = false;
	protected $allowedFields        = [];

	// Dates
	protected $useTimestamps        = true;
	protected $dateFormat           = 'datetime';
	protected $createdField         = '';
	protected $updatedField         = '';
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

	protected function bypass(array $data){

	    $_data = &$data['data'];
        $_data['enabled'] = true;
        $_data['month'] = getMonthIdByMonth(date('m'));
        $_data['year'] = getCurrentYearId();
        return $data;
    }

    public function uploadAllowed($filter=[]) {
        $sql = "SELECT * FROM vw_allow_uploads_block WHERE 1=1";

        if(isset($filter['agency_type_id']) && $filter['agency_type_id']){
            $sql .= " AND agency_type_id='".$filter['agency_type_id']."'";
        }

        if(isset($filter['user_id']) && $filter['user_id']){
            $sql .= " AND user_id='".$filter['user_id']."'";
        }

        if(isset($filter['month']) && $filter['month']){
            $sql .= " AND month='".$filter['month']."'";
        }

        if(isset($filter['year']) && $filter['year']){
            $sql .= " AND year='".$filter['year']."'";
        }

        $month = $this->db->query($sql)->getRowArray();

        if($month){
            if((isset($month['extended_date']) && (strtotime('today') < strtotime($month['extended_date'])))
                && (strtotime('today') >= strtotime($month['from_date']))){
                return $month;
            }

            if((strtotime('today') >= strtotime($month['from_date']))
                && (strtotime('today') <= strtotime($month['to_date']))){
                return $month;
            }
        }

        return [];
    }

    public function getByDate($filter=[]) {
	    $date = date('Y-m-d');

        $sql = "SELECT * FROM vw_allow_uploads_block WHERE 1=1";

        if(isset($filter['agency_type_id']) && $filter['agency_type_id']){
            $sql .= " AND agency_type_id=".$filter['agency_type_id'];
        }

        if(isset($filter['user_id']) && $filter['user_id']){
            $sql .= " AND user_id=".$filter['user_id'];
        }

        $sql .= " AND DATE('".$date."') BETWEEN DATE(from_date) AND DATE(IF(extended_date IS NOT NULL,extended_date,to_date)) ORDER BY upload_id DESC";
//echo $sql;exit;
        $month = $this->db->query($sql)->getResultArray();

        return $month;
    }

    public function getCurrentMonth() {
	    $date = date('Y-m-d');

        $sql = "SELECT * FROM soe_allow_uploads au 
WHERE DATE('$date') BETWEEN DATE(au.from_date) 
AND DATE_ADD(au.from_date,INTERVAL 25 DAY) LIMIT 1";

        $res = $this->db->query($sql)->getRow();
        if($res){
            return $res->month;
        } else {
            return false;
        }
    }

    public function extendDate($data=[]){
        $db = \Config\Database::connect();
	    $builder = $db->table('soe_allow_upload_blocks');
        $ex = $builder->where([
            'user_id'=>$data['user_id'],'upload_id'=>$data['upload_id']
        ])->get()->getFirstRow();

        if($ex){
            return $builder->where('id',$ex->id)->update(['to_date'=>$data['to_date']]);
        } else {
            return $builder->insert($data);
        }
    }

    public function updateStatus($filter) {
        $db = \Config\Database::connect();
        if($filter['modulecode']=='expense'){
            $builder = $db->table('soe_transactions');
        }
        if($filter['modulecode']=='fund_receipt'){
            $builder = $db->table('soe_transactions');
        }
        if($filter['modulecode']=='other_receipt'){
            $builder = $db->table('soe_misc_transactions');
        }
        if($filter['modulecode']=='mis'){
            $builder = $db->table('mis_submissions');
        }
        if($filter['modulecode']=='closing_balance'){
            $builder = $db->table('soe_closing_balances');
        }
        $row = $builder->where('id',$filter['upload_id'])->get()->getFirstRow();

        if($row->block_id > 0 && in_array($row->agency_type_id,[5,6])){
            $where = [
                'block_id'=>$row->block_id,
                'year'=>$row->year,
                'user_id'=>$row->user_id,
                'month'=>$row->month,
                'deleted_at' => null
            ];
            if(isset($row->transaction_type)){
                $where['transaction_type'] = $row->transaction_type;
            }

            return $builder->where($where)->update(['status'=>$filter['status']]);
        }

        return $builder->where('id',$filter['upload_id'])
            ->update(['status'=>$filter['status']]);
    }
}