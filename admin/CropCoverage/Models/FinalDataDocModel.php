<?php
namespace Admin\CropCoverage\Models;

use CodeIgniter\Model;

class FinalDataDocModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'ac_finaldata_doc';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    protected $protectFields = false;
    // protected $allowedFields = [
    // 	'block_id',
    // 	'year_id',
    // 	'season',
    // 	'ragi_smi',
    // 	'ragi_lt',
    // 	'ragi_ls',
    // ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [

    ];


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

    public function addFileData($filedata)
    {
        $this->where('block_id', $filedata['block_id'])
            ->where('season', $filedata['season'])
            ->where('year_id', $filedata['year_id'])
            ->delete();
        $this->insert($filedata);
    }
    public function getFileName($filter)
    {
        $sql = "SELECT filename FROM ac_finaldata_doc afd WHERE afd.deleted_at IS null";

        if (!empty($filter['block_id'])) {
            $sql .= " AND afd.block_id = " . $filter['block_id'];
        } else {
            $sql .= " AND afd.block_id = " . 0;
        }
        if (!empty($filter['year_id'])) {
            $sql .= " AND afd.year_id = " . $filter['year_id'];
        }
        if (!empty($filter['season'])) {
            $sql .= " AND afd.season = '" . $filter['season'] . "'";
        }


        $result = $this->db->query($sql)->getRow();
        // echo $sql;
        // exit;
        return $result ? $result->filename : null;

    }

}
?>