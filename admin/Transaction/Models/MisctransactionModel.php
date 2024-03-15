<?php

namespace Admin\Transaction\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Query;

class MisctransactionModel extends Model
{
    // ... (same as original code)

    public function getHeads($agency_type_id): array
    {
        return $this->db->query("SELECT * FROM soe_misc_txn_heads WHERE agency_type_id = $agency_type_id")
            ->getResultArray();
    }

    public function getAll(array $filter = []): array
    {
        $this->validateFilter($filter);

        $query = $this->db->table('soe_misc_transactions mt');

        $query->select('mt.id, y.name year, m.name month, ug.name agency_type, fa.name fund_agency, mt.created_at, in_amt.credit, out_amt.debit, mt.status');
        $query->join('soe_months m', 'm.id = mt.month', 'LEFT');
        $query->join('user_group ug', 'ug.id = mt.agency_type_id', 'LEFT');
        $query->join('soe_fund_agency fa', 'fa.id = mt.fund_agency_id', 'LEFT');
        $query->join('(
            SELECT mta.txn_id, SUM(mta.amount) credit
            FROM soe_misc_txn_amt mta
            LEFT JOIN soe_misc_txn_heads mth ON mta.head_id = mth.id
            WHERE mth.txn_type = \'in\'
            GROUP BY mta.txn_id
        ) in_amt', 'mt.id = in_amt.txn_id', 'LEFT');
        $query->join('(
            SELECT mta.txn_id, SUM(mta.amount) debit
            FROM soe_misc_txn_amt mta
            LEFT JOIN soe_misc_txn_heads mth ON mta.head_id = mth.id
            WHERE mth.txn_type = \'out\'
            GROUP BY mta.txn_id
        ) out_amt', 'mt.id = out_amt.txn_id', 'LEFT');

        if (isset($filter['agency_type_id'])) {
            $query->where('mt.agency_type_id', $filter['agency_type_id']);
        }
        if (isset($filter['user_id'])) {
            $query->where('mt.user_id', $filter['user_id']);
        }

        if (isset($filter['sort'])) {
            $query->orderBy($filter['sort'], $filter['order'] ?? 'DESC');
        } else {
            $query->orderBy('mt.year DESC, mt.month');
        }

        if (isset($filter['start']) && isset($filter['limit'])) {
            $query->limit($filter['limit'], $filter['start']);
        }

        $result = $query->getWhere(['mt.deleted_at' => null])->getResultArray();

        return $result;
    }

    public function getTotal(array $filter = []): int
    {
        $this->validateFilter($filter);

        $query = $this->db->table('soe_misc_transactions t');
        $query->select('COUNT(*) total');

        if (isset($filter['agency_type_id'])) {
            $query->where('t.agency_type_id', $filter['agency_type_id']);
        }
        if (isset($filter['user_id'])) {
            $query->where('t.user_id', $filter['user_id']);
        }

        $query->where('t.deleted_at', null);

        $result = $query->get()->getRowArray();

        return (int) $result['total'];
    }

    public function getTotalAmount(array $filter = []): float
    {
        $this->validateFilter($filter);

        $query = $this->db->table('(
            SELECT
                COALESCE(SUM(IF(mt.txn_type = \'out\', -1, 1) * mt.total),0) total
            FROM (
                SELECT
                    COALESCE(SUM(mta.amount), 0) total,
                    mth.txn_type
                FROM soe_misc_transactions mt
                LEFT JOIN soe_misc_txn_amt mta ON mt.id = mta.txn_id
                LEFT JOIN soe_misc_txn_heads mth ON mta.head_id = mth.id
                WHERE mt.deleted_at IS NULL
                AND mta.deleted_at IS NULL
                AND mt.status = 1
        ');

        if (isset($filter['block_id'])) {
            $query->where('mt.block_id', $filter['block_id']);
        }
        if (isset($filter['agency_type_id'])) {
            $query->where('mt.agency_type_id', $filter['agency_type_id']);
        }
        if (isset($filter['year'])) {
            $query->where('mt.year', $filter['year']);
        }
        if (isset($filter['month'])) {
            $query->where('mt.month', $filter['month']);
        }

        $query->getCompiledSelect();

        $result = $query->getWhere(['mt.deleted_at' => null])->getRowArray();

        return (
