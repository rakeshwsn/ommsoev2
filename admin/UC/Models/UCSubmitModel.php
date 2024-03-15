<?php

namespace Admin\UC\Models;

use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

class UCSubmitModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'soe_uc_submit';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
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

    public function __construct(ConnectionInterface &$db)
    {
        parent::__construct($db);
    }

    // Get recipient ID based on district ID and fund agency ID
    public function getRecipientId(int $district_id, int $fund_agency_id = 1): ?int
    {
        $builder = $this->db->table('soe_uc_recipients r');
        $builder->where('r.district_id', $district_id);
        $builder->where('r.fund_agency_id', $fund_agency_id);
        $query = $builder->get();

        if ($query->getNumRows() === 0) {
            return null;
        }

        return $query->getRow()->id;
    }

    // Get UC report based on filter
    public function getUCReport(array $filter = []): array
    {
        $recipient_id = 0;

        if (isset($filter['recipient_id'])) {
            $recipient_id = $filter['recipient_id'];
        }

        if (isset($filter['district_id'])) {
            $recipient_id = $this->getRecipientId($filter['district_id']);

            if (!$recipient_id) {
                return [];
            }
        }

        $builder = $this->db->table('soe_uc_allotment sua');
        $builder->select('sua.recipient_id, sua.year, SUM(sua.amount) allotment');
        $builder->where('sua.deleted_at', null);
        $builder->groupBy('sua.year');

        if ($recipient_id) {
            $builder->where('sua.recipient_id', $recipient_id);
        }

        $allotment_query = $builder->get();

        $builder = $this->db->table('soe_uc_submit sus');
        $builder->select('
            allt.recipient_id,
            allt.year year_id,
            y.name `year`,
            allt.allotment,
            sbmt.date_submit,
            sbmt.letter_no,
            sbmt.uc_submit,
            (allt.allotment - sbmt.uc_submit) balance
        ');
        $builder->join('soe_uc_allotment allt', 'allt.year = sus.year', 'left');
        $builder->join('soe_years y', 'y.id = allt.year', 'left');
        $builder->where('sus.deleted_at', null);

        if ($recipient_id) {
            $builder->where('allt.recipient_id', $recipient_id);
        }

        $builder->groupBy('sus.year');

        $submissions_query = $builder->get();

        $result = [];

        foreach ($allotment_query->getResult() as $allotment) {
            $row = $submissions_query->getRowWhere('year_id', $allotment->year);

            if ($row) {
                $result[] = [
                    'recipient_id' => $allotment->recipient_id,
                    'year_id' => $allotment->year,
                    'year' => $row->year,
                    'allotment' => $allotment->allotment,
                    'date_submit' => $row->date_submit,
                    'letter_no' => $row->letter_no,
                    'uc_submit' => $row->uc_submit,
                    'balance' => $row->balance,
                ];
            }
        }

        return $result;
    }

    // Get submissions based on filter
    public function getSubmissions(array $filter = []): array
    {
        $builder = $this->db->table('soe_uc_submit sus');
        $builder->select('
            sus.id uc_id,
            sus.date_submit,
            sus.letter_no,
            sus.page_no,
            sus.amount,
            sus.document
        ');
        $builder->join('soe_uc_allotment sua', 'sus.allotment_id = sua.id', 'left');
        $builder->where('sua.deleted_at', null);
        $builder->where('sus.deleted_at', null);

        if (isset($filter['year'])) {
            $builder->where('sua.year', $filter['year']);
        }

        if (isset($filter['recipient_id'])) {
            $builder->where('sua.recipient_id', $filter['recipient_id']);
        }

        $query = $builder->get();

        return $query->getResult();
    }
}
