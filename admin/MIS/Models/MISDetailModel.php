<?php

namespace Admin\MIS\Models;

use CodeIgniter\Model;

class MISDetailModel extends Model
{
    protected $table = 'mis_submission_details';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    protected $protectFields = false;
    protected $allowedFields = ['column1', 'column2', 'column3']; // add allowed fields here

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
}
