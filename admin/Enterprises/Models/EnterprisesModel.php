<?php

namespace Admin\Enterprises\Models;

use CodeIgniter\Model;

class EnterprisesModel extends Model
{
  protected $DBGroup              = 'default';
  protected $table                = 'enterprises';
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
  protected $updatedField         = 'updated_at';
  protected $deletedField         = 'deleted_at';

  // Validation
  protected $validationRules      = [
    'managing_unit_name' =>  [
      'label'  => 'managing_unit_name|min_length[30]',
      'rules'  => 'required',
      'errors' => [
        'required' => 'Managing unit name is required',
      ]
    ],

    'contact_person' =>  [
      'label'  => 'contact_person|min_length[30]',
      'rules'  => 'required',
      'errors' => [
        'required' => 'Conatct person name is required',
      ]
    ],
    'contact_mobile' =>   [
      'label'  => 'contact_mobile|max_length[10]',
      'rules'  => 'required',
      'errors' => [
        'required' => 'Conatct is required',
      ]
    ],

    'date_estd' => [
      'label'  => 'date_estd',
      'rules'  => 'required',
      'errors' => [
        'required' => '
          Date of Enterprise Establishment is required',
      ]
    ],

    'unit_id' =>  [
      'label'  => 'unit_id',
      'rules'  => 'required|greater_than[0]',
      'errors' => [
        'required' => 'Name/Type of Unit',
        'greater_than' => 'Select Unit Type',
      ]
    ],
    'management_unit_type' => [
      'label'  => 'management_unit_type',
      'rules'  => 'required',
      'errors' => [
        'required' => '
         Selelct Date of OMU Under OMM',
      ]
    ],
    'district_id' => [
      'label'  => 'district_id',
      'rules'  => 'required|greater_than[0]',
      'errors' => [
        'required' => 'Select District name',
        'greater_than' => 'Select District name'
      ]
    ],
    'block_id' => [
      'label'  => 'block_id',
      'rules'  => 'required|greater_than[0]',
      'errors' => [
        'required' => 'Selelct Block name',
        'greater_than' => 'Select Block Name'
      ]
    ],
    'gp_id' =>  [
      'label'  => 'gp_id',
      'rules'  => 'required|greater_than[0]',
      'errors' => [
        'required' => 'Selelct Grampanchayat name',
        'greater_than' => 'Select GramPanchayat Name'
      ]
    ],
    'village_id' => [
      'label'  => 'village_id',
      'rules'  => 'required|greater_than[0]',
      'errors' => [
        'required' => 'Selelct Village name',
        'greater_than' => 'Select Village Name'
      ]
    ],
    'budget_fin_yr_id' => [
      'label'  => 'budget_fin_yr_id',
      'rules'  => 'required|greater_than[0]',
      'errors' => [
        'required' => 'Selelct Financial year ',
        'greater_than' => 'Select Financial year'
      ]
    ],
    'mou_date' =>  [
      'label'  => 'mou_date',
      'rules'  => 'required',
      'errors' => [
        'required' => '
        Selelct Budget Utilized of Financial year ',
      ]
    ],
    'unit_budget' => [
      'label'  => 'unit_budget',
      'rules'  => 'required',
      'errors' => [
        'required' => 'Select Budget Code',

      ]
    ],
    'unit_budget_amount' =>  [
      'label'  => 'unit_budget_amount',
      'rules'  => 'required',
      'errors' => [
        'required' => 'Enter Amount',
      ]
    ],
    'purpose_infr_support' =>  [
      'label'  => 'purpose_infr_support',
      'rules'  => 'required_if_value[is_support_basis_infr,yes]',
      'errors' => [
        'required_if_value' => 'Type/Purpose of Addl. infa structure is required ',
      ]
    ],
    'addl_budget' =>  [
      'label'  => 'addl_budget',
      'rules'  => 'required_if_value[is_support_basis_infr,yes]',
      'errors' => [
        'required_if_value' => 'Select Budget Code',

      ]
    ],
    'support_infr_amount' =>  [
      'label'  => 'support_infr_amount',
      'rules'  => 'required_if_value[is_support_basis_infr,yes]',
      'errors' => [
        'required_if_value' => 'Enter Amount ',
      ]
    ],

  ];
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

  public function getAll($filter = [])
  {
    $sql =  "SELECT
    e.id,
    e.unit_id,
    e.district_id,
    e.block_id,
    e.gp_id,
    e.village_id,
    e.budget_fin_yr_id,
    e.management_unit_type,
    e.managing_unit_name,
    e.contact_person,
    e.contact_mobile,
    e.date_estd,
    e.mou_date,
    e.unit_budget,
    e.addl_budget,
    e.unit_budget_amount,
    e.is_support_basis_infr,
    e.is_support_basis_infr,
    e.purpose_infr_support,
    e.support_infr_amount,
    sd.name districts,
    sb.name blocks,
    v.name villages,
    gp.name gp,
    eu.name unit_name,
    eu.group_unit,
    YEAR(e.date_estd) year
FROM enterprises e
    LEFT JOIN soe_districts sd ON sd.id = e.district_id
    LEFT JOIN soe_blocks sb ON sb.id = e.block_id
    LEFT JOIN villages v ON v.id = e.village_id
    LEFT JOIN grampanchayat gp ON gp.id = e.gp_id
    LEFT JOIN enterprises_units eu ON eu.id = e.unit_id
WHERE e.deleted_at IS NULL AND YEAR(e.date_estd) > 2000 AND e.management_unit_type='shg'";

    if (isset($filter['district_id'])) {
      $sql .= " AND e.district_id = " . $filter['district_id'];
    }
    if (isset($filter['block_id'])) {
      $sql .= " AND e.block_id = " . $filter['block_id'];
    }
    if (isset($filter['unit_id'])) {
      $sql .= " AND e.unit_id = " . $filter['unit_id'];
    }
    if (isset($filter['year'])) {
      $sql .= " AND YEAR(e.date_estd) = " . $filter['year'];
    }

    // Add ORDER BY clause if needed


    // printr($sql);
    // exit;
    return $this->db->query($sql)->getResult();
  }
  public function yearWise($district_id)
  {
    $sql = "SELECT
    e.id,
    e.district_id,
    YEAR(e.date_estd) year
FROM enterprises e
WHERE e.district_id = $district_id  AND YEAR(e.date_estd) > 2000
GROUP BY year
ORDER BY year 
 
 ";
    //  printr($sql);
    //     exit;
    return $this->db->query($sql)->getResult();
  }
}
