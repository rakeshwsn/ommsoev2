<?php
namespace Admin\Users\Models;

use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

class UserModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'user';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes        = true;
	protected $protectFields        = false;
//	protected $allowedFields        = [];

	// Dates
	protected $useTimestamps        = true;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'firstname' => array(
            'label' => 'Name',
            'rules' => 'trim|required|max_length[100]'
        ),

        'email' =>array(
            'label' => 'Email',
            'rules' => 'required',
            'rules' => "trim|required|valid_email|max_length[255]|is_unique[user.email,id,{id}]"
        ),

        'username' =>array(
                'label' => 'Username',
                'rules' => "required|is_unique[user.username,id,{id}]"
        ),
        'password' =>array(
            'label' => 'Password',
            'rules' => 'required'
        )
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
    protected $beforeInsert         = ['setPassword','gparray','localisation','resetAssign'];
	protected $afterInsert          = [];
	protected $beforeUpdate         = ['setPassword','gparray','localisation','resetAssign'];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = ['setUserType'];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

    public function getAll($filter=[]) {
        $sql = "SELECT
  u.*,d.name district,b.name block,ug.name role
FROM user u
  LEFT JOIN soe_districts d
    ON d.id = u.district_id
  LEFT JOIN soe_blocks b
    ON b.id = u.block_id
  LEFT JOIN user_group ug
    ON ug.id = u.user_group_id
WHERE  u.deleted_at IS NULL";

        if (!empty($filter['filter_search'])) {
            $sql .= " AND (concat_ws(' ', u.firstname, u.lastname) LIKE '%{$filter['filter_search']}%'
                OR u.email LIKE '%{$filter['filter_search']}%'
				OR u.username LIKE '%{$filter['filter_search']}%'
				OR d.name LIKE '%{$filter['filter_search']}%'
				OR b.name LIKE '%{$filter['filter_search']}%'
				OR ug.name LIKE '%{$filter['filter_search']}%'
            )";
        }

        if (isset($filter['sort']) && $filter['sort']) {
            $sort = $filter['sort'];
        } else {
            $sort = "d.name,b.name";
        }

        if (isset($filter['order']) && ($filter['order'] == 'desc')) {
            $order = "desc";
        } else {
            $order = "asc";
        }
        $sql .= " ORDER BY $sort $order ";

        if (isset($filter['start']) || isset($filter['limit'])) {
            if ($filter['start'] < 0) {
                $filter['start'] = 0;
            }

            if ($filter['limit'] < 1) {
                $filter['limit'] = 10;
            }
        }

        $sql .= " LIMIT ".$filter['start'].', '.$filter['limit'];

        return $this->db->query($sql)->getResult();
//        return
	}

    public function getTotal($filter=[]) {
        $sql = "SELECT
              COUNT(u.id) total
            FROM user u
              LEFT JOIN soe_districts d
                ON d.id = u.district_id
              LEFT JOIN soe_blocks b
                ON b.id = u.block_id
              LEFT JOIN user_group ug
                ON ug.id = u.user_group_id
            WHERE user_group_id != 1 AND u.deleted_at IS NULL";

        if (!empty($filter['filter_search'])) {
            $sql .= " AND (concat_ws(' ', u.firstname, u.lastname) LIKE '%{$filter['filter_search']}%'
                OR u.email LIKE '%{$filter['filter_search']}%'
				OR u.username LIKE '%{$filter['filter_search']}%'
				OR d.name LIKE '%{$filter['filter_search']}%'
				OR b.name LIKE '%{$filter['filter_search']}%'
				OR ug.name LIKE '%{$filter['filter_search']}%'
            )";
        }

        $count = $this->db->query($sql)->getRow()->total;

//        $count = $this->countAllResults();

        return $count;
	}

	public function addCentralUser($firstname=""){
        $odk = service('odkcentral');
        $appUser = $odk->projects(2)->appUsers()->create([
            'displayName' => $firstname
        ]);
        return $appUser;
    }

    public function deleteCentralUser($id){
	    $odk = service('odkcentral');
        $odk->projects(2)->appUsers($id)->delete();
    }

    public function allOdkForms(){
        $odk = service('odkcentral');
        $forms = $odk->projects(2)->forms()->get();
        return $forms;
    }

    public function getAssignForm(){
        $odk = service('odkcentral');
        $forms = $odk->projects(2)->assignments()->forms()->get();

        return $forms;
    }

    protected  function setPassword(array $data){
        $data['data']['show_password']=$data['data']['password'];
	    $data['data']['password']=password_hash($data['data']['password'], PASSWORD_DEFAULT);

        return $data;
    }

    protected  function resetAssign(array $data){
        unset($data['data']['form_assign']);
        //printr($data);
        return $data;

    }
	
	protected function gparray(array $data){
		if(isset($data['data']['gp'])){
		$data['data']['gp']=implode(',',$data['data']['gp']);
		}
		return $data;
	}
	
	protected function localisation(array $data){

        /*if($data['data']['district_id']) {
            $districtModel=new DistrictModel();
            $district=$districtModel->asArray()->find($data['data']['district_id']);
            $data['data']['district_id']=$district?$district['id']:0;
        }
        if($data['data']['block']) {
            $blockModel=new BlockModel();
            $block=$blockModel->asArray()->find($data['data']['block_id']);
            $data['data']['block_id']=$block?$block['id']:0;
        }*/

		return $data;
		
	}

	//rakesh
    public function setUserType($data) {

	    return $data;
	}

    public function getUserUploadMonths($filter=[]) {

        $sql = "SELECT
  users.*,
  sau.id upload_id,
  users.district_id,
  users.district,
  users.block_id,
  users.block,
  sau.month month_id,
  sau.year,
  sau.from_date,
  sau.to_date,
  saub.to_date extended_date
FROM soe_allow_uploads sau
  LEFT JOIN (SELECT
      u.id user_id,
      u.user_group_id agency_type_id,
      u.username,
      u.firstname,
      u.block_id,
      sb.name block,
      u.district_id,
      sd.name district
    FROM user u
      LEFT JOIN soe_blocks sb
        ON u.block_id = sb.id
      LEFT JOIN soe_districts sd
        ON u.district_id = sd.id
    WHERE u.deleted_at IS NULL";
        if(!empty($filter['agency_type_id'])){
            $sql .= " AND u.user_group_id = ".$filter['agency_type_id'];
        } else {
            $sql .= " AND u.user_group_id IN (5, 7, 8, 9)";
        }

        $sql .= ") users
    ON sau.agency_type_id = users.agency_type_id
  LEFT JOIN soe_allow_upload_blocks saub
    ON sau.id = saub.upload_id AND saub.user_id = users.user_id WHERE 1=1";

        if(!empty($filter['district_id'])){
            $sql .= " AND users.district_id = ".$filter['district_id'];
        }
        if(!empty($filter['month'])){
            $sql .= " AND sau.month = ".$filter['month'];
        }

        return $this->db->query($sql)->getResult();
	}

    public function getUploadStatus($filter) {
	    if(empty($filter['year'])){
	        return [];
        }
	    if(empty($filter['month'])){
	        return [];
        }
	    if(empty($filter['district_id'])){
	        return [];
        }

        $year = $filter['year'];
        $month = $filter['month'];
        $district_id = $filter['district_id'];

        $sql = "SELECT
  umym.*,sts.id,COALESCE(sts.status,3)status,created_at
FROM (SELECT
    *
  FROM vw_user_modules um
    JOIN vw_all_year_month vaym
  WHERE vaym.year_id = $year
  AND vaym.month_id = $month
  AND district_id = $district_id) umym
  LEFT JOIN (SELECT
  ms.id,
  ms.status,
  ms.district_id,
  ms.block_id,
  ms.agency_type_id,
  created_at,
  'mis' modulecode
FROM mis_submissions ms
WHERE ms.deleted_at IS NULL
AND ms.month = $month
AND ms.year = $year
AND ms.district_id = $district_id
UNION ALL
SELECT
  st.id,
  st.status,
  st.district_id,
  st.block_id,
  st.agency_type_id,
  date_added created_at,
  'expense' modulecode
FROM soe_transactions st
WHERE st.deleted_at IS NULL
AND st.transaction_type = 'expense'
AND st.month = $month
AND st.year = $year
AND st.district_id = $district_id
UNION ALL
SELECT
  st.id,
  st.status,st.district_id,st.block_id,
  st.agency_type_id,date_added created_at,
  'fund_receipt' modulecode
FROM soe_transactions st
WHERE st.deleted_at IS NULL
AND st.transaction_type = 'fund_receipt'
AND st.month = $month
AND st.year = $year
AND st.district_id = $district_id
UNION ALL
SELECT
  smt.id,
  smt.status,smt.district_id,smt.block_id,
  smt.agency_type_id,created_at,
  'other_receipt' modulecode
FROM soe_misc_transactions smt
WHERE smt.deleted_at IS NULL
AND smt.year = $year
AND smt.month = $month
AND smt.district_id = $district_id
UNION ALL
SELECT
  scb.id,
  scb.status, scb.district_id,scb.block_id,
  scb.agency_type_id,created_at,
  'closing_balance' modulecode
FROM soe_closing_balances scb
WHERE scb.deleted_at IS NULL
AND scb.month = $month
AND scb.year = $year
AND scb.district_id = $district_id) sts
    ON sts.modulecode = umym.modulecode
    AND sts.district_id = umym.district_id
    AND sts.block_id = umym.block_id
    AND sts.agency_type_id = umym.user_group_id";


        return $this->db->query($sql)->getResult();
    }
}
