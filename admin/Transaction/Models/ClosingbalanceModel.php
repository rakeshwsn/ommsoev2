<?php

namespace Admin\Transaction\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class ClosingbalanceModel extends Model
{
    use ModelTrait;

    public function __construct()
    {
        parent::__construct();
        $this->init();
    }

	protected $DBGroup              = 'default';
	protected $table                = 'soe_closing_balances';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes        = true;
	protected $protectFields        = false;
	protected $allowedFields        = [];

	// Dates
	protected $useTimestamps        = true;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
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

    public function getOpeningBalance(array $filter) {
        $this->select('advance + bank + cash as total',false);
        $where = [];
        if(!empty($filter['block_id'])){
            $where['block_id'] = $filter['block_id'];
        }
        if(!empty($filter['user_id'])){
            $where['user_id'] = $filter['user_id'];
        }
        if(!empty($filter['district_id'])){
            $where['district_id'] = $filter['district_id'];
        }
        if(!empty($filter['agency_type_id'])){
            $where['agency_type_id'] = $filter['agency_type_id'];
        }
        $this->orderBy('id','asc');
        $this->where($where);

        $total = $this->first()->total;

        return $total;
	}

    public function getClosingBalance(array $filter) {
        // cb = prv_cb + mon_fr + misc_rec - mon_exp

        $ob = $this->getOpeningBalance(['user_id'=>$filter['user_id']]);

        $upto = [
            'month' => $filter['month'],
            'year' => $filter['year'],
            'agency_type_id' => $filter['agency_type_id'],
        ];
        if(!empty($filter['block_id'])){
            $upto['block_id'] = $filter['block_id'];
        }
        if(!empty($filter['district_id'])){
            $upto['district_id'] = $filter['district_id'];
        }

        $txnModel = new TransactionModel();
        $upto['transaction_type'] = 'fund_receipt';
        $mon_fr = $txnModel->getTotalAmount($upto);

        $upto['transaction_type'] = 'expense';
        $mon_exp = $txnModel->getTotalAmount($upto);

        $miscTxnModel = new MisctransactionModel();
        $misc_rec = $miscTxnModel->getTotalAmount($upto);

        $cb = $ob + $mon_fr->fin + $misc_rec - $mon_exp->fin;

        return $cb;
	}

    public function getLedgerReport($filter=[],$return_type='object')
    {
        $block_id = isset($filter['block_id']) ? $filter['block_id']:0;
        $district_id = isset($filter['district_id']) ? $filter['district_id']:0;
        $year = $filter['year'];
        $agency_type_id = $filter['agency_type_id'];
        $user_id = $filter['user_id'];
        $fund_agency_id = $filter['fund_agency_id'];

        if($block_id){
            $sql = "CALL proc_get_ledger_statement($block_id,$year,$agency_type_id,$user_id,$fund_agency_id)";
        }

        if($district_id){
            $sql = "CALL proc_get_ledger_statement_district($district_id,$year,$agency_type_id,$user_id,$fund_agency_id)";
        }

        if($return_type=='array'){
            $func = 'getResultArray';
        } else {
            $func = 'getResult';
        }

        return $this->db->query($sql)->$func();
	}

    public function getTransactionDetail($id) {
        $result = $this->db
            ->table('soe_closing_balances t')
            ->select('u.firstname, c.name block,d.name district,ut.name agency_type,y.name year,month,t.created_at,t.status,t.agency_type_id,t.remarks')
            ->join('soe_blocks c','c.id=t.block_id','LEFT')
            ->join('soe_districts d','d.id=t.district_id','LEFT')
            ->join('user_group ut','ut.id=t.agency_type_id','LEFT')
            ->join('soe_years y','y.id=t.year','LEFT')
            ->join('users u','u.id=t.user_id','LEFT')
            ->where('t.id',$id)->get()->getRow();

        return $result;
    }

    public function getOpeningBalanceBreakup($filter=[]) {
        $month = $filter['month'];
        $year = $filter['year'];
        if($month-1==0 && $year-1==0){
            $month = $month-1;
            $year = $year-1;
        } else if($year-1>0){
            $month = 12;
            $year = $year-1;
        } else {
            $month=$month-1;
        }
        $sql = "SELECT
  SUM(advance) advance,
  SUM(bank) bank,
  SUM(cash) cash
FROM (SELECT
    *
  FROM soe_closing_balances scb
  WHERE scb.deleted_at IS NULL";
        if(isset($filter['block_id'])){
            $sql .= " AND scb.block_id = ".(int)$filter['block_id'];
        }
        if(isset($filter['district_id'])){
            $sql .= " AND scb.district_id = ".$filter['district_id'];
        }
        if(!empty($filter['agency_type_id'])){
            $ati = (array)$filter['agency_type_id'];
            $sql .= " AND scb.agency_type_id IN (".implode(',',$ati).")";
        }
        $sql .= " AND scb.month = ".$month."
    AND scb.year = ".$year.") cb";
//echo $sql; exit;
        return $this->db->query($sql)->getFirstRow();
    }

    public function getOpeningBalanceBreakupOld($filter=[]) {
        $ati = [];
        $sql = "SELECT SUM(advance) advance,SUM(bank) bank,SUM(cash) cash FROM 
(SELECT
  *
FROM soe_closing_balances scb
WHERE scb.deleted_at IS NULL";
        if(!empty($filter['block_id'])){
            $sql .= " AND scb.block_id = ".$filter['block_id'];
        }
        if(!empty($filter['agency_type_id'])){
            $ati = (array)$filter['agency_type_id'];
            $sql .= " AND scb.agency_type_id IN (".implode(',',$ati).")";
        }
        $sql .= " 
AND scb.month != ".$filter['month']."
AND scb.year != ".$filter['year']."
ORDER BY id DESC LIMIT ".count($ati).") cb";
//echo $sql;exit;
        return $this->db->query($sql)->getFirstRow();
    }

    public function getOpeningBalanceByMonth($filter=[]) {
        $year = $filter['year'];
        $last_year = ($filter['year']-1);
        $month = $filter['month'];
        $last_month = ($filter['month']-1);

        $sql = "SELECT
  ob.total ob_total,fr_upto.total fr_upto_total,
  fr.total fr_total, otr.total otr_total, otr_upto.total otr_upto_total,
  ex_upto.total ex_upto_total, ex.total ex_total
FROM (SELECT
    COALESCE((advance + bank + cash), 0) total
  FROM soe_closing_balances
  WHERE deleted_at IS NULL
  AND year = 0
  AND month = 0";
        if(!empty($filter['district_id'])){
            $sql .= " AND district_id = ".$filter['district_id'];
        }
        if(!empty($filter['agency_type_id'])){
            $sql .= " AND agency_type_id = ".$filter['agency_type_id'];
        }
        $sql .= " ) ob
  JOIN (SELECT
      COALESCE(SUM(stc.financial), 0) total
    FROM soe_transactions st
      LEFT JOIN soe_transaction_components stc
        ON st.id = stc.transaction_id
    WHERE st.deleted_at IS NULL
    AND stc.deleted_at IS NULL
    AND st.transaction_type = 'fund_receipt'
    AND st.status = 1";
        if(!empty($filter['district_id'])){
            $sql .= " AND st.district_id = ".$filter['district_id'];
        }
        if(!empty($filter['agency_type_id'])){
            $sql .= " AND st.agency_type_id = ".$filter['agency_type_id'];
        }
        if(!empty($filter['user_id'])){
            $sql .= " AND st.user_id = ".$filter['user_id'];
        }
    $sql .= " 
    AND ((st.year BETWEEN 0 AND $last_year)
    OR (st.year = $year
    AND st.month BETWEEN 0 AND $last_month))) fr_upto
  JOIN (SELECT
      COALESCE(SUM(stc.financial), 0) total
    FROM soe_transactions st
      LEFT JOIN soe_transaction_components stc
        ON st.id = stc.transaction_id
    WHERE st.deleted_at IS NULL
    AND stc.deleted_at IS NULL
    AND st.transaction_type = 'fund_receipt'
    AND st.status = 1";
        if(!empty($filter['district_id'])){
            $sql .= " AND st.district_id = ".$filter['district_id'];
        }
        if(!empty($filter['agency_type_id'])){
            $sql .= " AND st.agency_type_id = ".$filter['agency_type_id'];
        }
        if(!empty($filter['user_id'])){
            $sql .= " AND st.user_id = ".$filter['user_id'];
        }
    $sql .=" 
    AND (st.year = $year
    AND st.month = $month)) fr
  JOIN (SELECT
      COALESCE(SUM(stc.financial), 0) total
    FROM soe_transactions st
      LEFT JOIN soe_transaction_components stc
        ON st.id = stc.transaction_id
    WHERE st.deleted_at IS NULL
    AND stc.deleted_at IS NULL
    AND st.transaction_type = 'expense'
    AND st.status = 1";
        if(!empty($filter['district_id'])){
            $sql .= " AND st.district_id = ".$filter['district_id'];
        }
        if(!empty($filter['agency_type_id'])){
            $sql .= " AND st.agency_type_id = ".$filter['agency_type_id'];
        }
        if(!empty($filter['user_id'])){
            $sql .= " AND st.user_id = ".$filter['user_id'];
        }
    $sql .= " 
    AND ((st.year BETWEEN 0 AND $last_year)
    OR (st.year = $year
    AND st.month BETWEEN 0 AND $last_month))) ex_upto
  JOIN (SELECT
      COALESCE(SUM(stc.financial), 0) total
    FROM soe_transactions st
      LEFT JOIN soe_transaction_components stc
        ON st.id = stc.transaction_id
    WHERE st.deleted_at IS NULL
    AND stc.deleted_at IS NULL
    AND st.transaction_type = 'expense'
    AND st.status = 1";
        if(!empty($filter['district_id'])){
            $sql .= " AND st.district_id = ".$filter['district_id'];
        }
        if(!empty($filter['agency_type_id'])){
            $sql .= " AND st.agency_type_id = ".$filter['agency_type_id'];
        }
        if(!empty($filter['user_id'])){
            $sql .= " AND st.user_id = ".$filter['user_id'];
        }
    $sql .= " 
    AND (st.year = $year
    AND st.month = $month)) ex
  JOIN (SELECT
      COALESCE(SUM(IF(mt.txn_type = 'out', -1, 1) * mt.total), 0) total
    FROM (SELECT
        COALESCE(SUM(mta.amount), 0) total,
        mth.txn_type
      FROM soe_misc_transactions mt
        LEFT JOIN soe_misc_txn_amt mta
          ON mt.id = mta.txn_id
        LEFT JOIN soe_misc_txn_heads mth
          ON mta.head_id = mth.id
      WHERE mt.deleted_at IS NULL
      AND mta.deleted_at IS NULL
      AND status = 1";
        if(!empty($filter['district_id'])){
            $sql .= " AND mt.district_id = ".$filter['district_id'];
        }
        if(!empty($filter['agency_type_id'])){
            $sql .= " AND mt.agency_type_id = ".$filter['agency_type_id'];
        }
        if(!empty($filter['user_id'])){
            $sql .= " AND mt.user_id = ".$filter['user_id'];
        }
      $sql .= "
      AND ((mt.year BETWEEN 0 AND $last_year)
    OR (mt.year = $year
    AND mt.month BETWEEN 0 AND $last_month))
      GROUP BY mth.txn_type) mt) otr_upto
  JOIN (SELECT
      COALESCE(SUM(IF(mt.txn_type = 'out', -1, 1) * mt.total), 0) total
    FROM (SELECT
        COALESCE(SUM(mta.amount), 0) total,
        mth.txn_type
      FROM soe_misc_transactions mt
        LEFT JOIN soe_misc_txn_amt mta
          ON mt.id = mta.txn_id
        LEFT JOIN soe_misc_txn_heads mth
          ON mta.head_id = mth.id
      WHERE mt.deleted_at IS NULL
      AND mta.deleted_at IS NULL
      AND status = 1";
        if(!empty($filter['district_id'])){
            $sql .= " AND mt.district_id = ".$filter['district_id'];
        }
        if(!empty($filter['agency_type_id'])){
            $sql .= " AND mt.agency_type_id = ".$filter['agency_type_id'];
        }
        if(!empty($filter['user_id'])){
            $sql .= " AND mt.user_id = ".$filter['user_id'];
        }
      $sql .= "
      AND mt.year = $year
      AND mt.month = $month
      GROUP BY mth.txn_type) mt) otr";

        return $this->db->query($sql)->getFirstRow();
    }

    //validation for adding transaction
    public function pendingUploads($filter = []) {

        $district_id = isset($filter['district_id']) ? $filter['district_id']:0;
        $block_id = isset($filter['block_id']) ? $filter['block_id']:0;
        $year = isset($filter['year']) ? $filter['year']:0;
        $month = isset($filter['month']) ? $filter['month']:0;
        $agency_type = isset($filter['agency_type']) ? $filter['agency_type']:0;
        $fund_agency_id = isset($filter['fund_agency_id']) ? $filter['fund_agency_id']:0;

        if($this->settings->block_user == $agency_type){
            //check if pending transactions
            $sql = "SELECT
  COUNT(id) total
FROM soe_transactions st
WHERE st.deleted_at IS NULL
AND st.agency_type_id = 5
AND st.transaction_type = 'expense'
AND st.block_id=$block_id AND fund_agency_id = $fund_agency_id
AND st.year=$year
AND st.month < $month";
echo $sql;exit;
            return $this->db->query($sql)->getFirstRow();

        } else if($this->settings->district_user == $agency_type){
            //check if pending expenses from blocks
            $sql = "SELECT
  scb.block_id,
  COUNT(id) total
FROM soe_transactions scb
WHERE scb.deleted_at IS NULL
AND scb.transaction_type = 'expense'
AND scb.month > 0
AND scb.agency_type_id = 5
AND scb.year = $year
AND scb.district_id = $district_id AND fund_agency_id = $fund_agency_id
AND scb.month < $month
GROUP BY scb.block_id";

            $data['block_cbs'] = $this->db->query($sql)->getResult();

            //check if pending cb at ATMA
            $sql = "SELECT
  COUNT(id) total
FROM soe_transactions scb
WHERE scb.deleted_at IS NULL
AND scb.transaction_type = 'expense'
AND scb.month > 0
AND scb.agency_type_id = 7 AND fund_agency_id = $fund_agency_id
AND scb.year = $year
AND scb.district_id = $district_id
AND scb.month < $month";

            $data['district_cbs'] = $this->db->query($sql)->getFirstRow()->total;

            //check if pending status from blocks

            $sql = "SELECT
  scb.block_id,
  COUNT(id) total
FROM soe_transactions scb
WHERE scb.deleted_at IS NULL
AND scb.transaction_type = 'expense'
AND scb.month > 0
AND scb.agency_type_id = 5 AND fund_agency_id = $fund_agency_id
AND scb.year = $year
AND scb.district_id = $district_id AND scb.status != 1 
AND scb.month <= $month GROUP BY scb.block_id";

            $data['pending_cbs'] = $this->db->query($sql)->getResult();

            return $data;

        }

    }
}
