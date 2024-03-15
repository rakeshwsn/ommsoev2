<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfigModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'config';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['key', 'value'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'key' => 'required|alpha_dash|min_length[3]|max_length[50]',
        'value' => 'required'
    ];
    protected $validationMessages   = [
        'key' => [
            'required' => 'The configuration key is required.',
            'alpha_dash' => 'The configuration key may only contain letters, numbers, and dashes.',
            'min_length' => 'The configuration key must be at least 3 characters long.',
            'max_length' => 'The configuration key must not exceed 50 characters.'
        ],
        'value' => [
            'required' => 'The configuration value is required.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}

