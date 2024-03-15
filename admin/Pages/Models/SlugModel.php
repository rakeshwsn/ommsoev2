<?php

namespace Admin\Pages\Models;

use CodeIgniter\Model;

class SlugModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'seo_url';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
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

	/**
	 * Get slug by given value
	 *
	 * @param string $value
	 * @return array
	 */
	public function getByValue(string $value): array
	{
		return $this->where('slug', $value)->first();
	}

	/**
	 * Get slug by given keyword
	 *
	 * @param string $keyword
	 * @return array
	 */
	public function getByKeyword(string $keyword): array
	{
		return $this->where('keyword', $keyword)->first();
	}

