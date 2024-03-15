<?php

namespace Admin\MIS\Models;

use CodeIgniter\Model;

class MISFileModel extends Model
{
    protected $table = 'mis_submission_files';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    protected $protectFields = false;
    protected $allowedFields = ['column1', 'column2', 'column3']; // add allowed columns here

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Add any custom methods or functionality here
}
