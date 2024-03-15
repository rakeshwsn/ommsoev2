<?php

namespace App\Models;

use CodeIgniter\Model;

class MISFileModel extends Model
{
    // Define table name, primary key and if it uses auto-increment
    protected $table      = 'mis_submission_files';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    // Define return type and if soft deletes are used
    protected $returnType     = 'object';
    protected $useSoftDeletes = true;


