<?php

namespace Admin\Reports\Models;

use CodeIgniter\Model;

class ReportsModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    private function getTransactionTotalByType($filter)
    {
        $builder = $this->db->table('soe_transactions st');
        $builder->select('SUM(stc.financial) total');
        $builder->join('soe_transaction_components stc', 'st.id = stc.transaction_id', 'left');
        $builder->where('st.deleted_at', null);
        $builder->where('stc.deleted_at', null);
        $builder->where('st.status', 1);

        if (isset($filter['user_id'])) {
            $builder->where('st.user_id', $filter['user_id']);
        }
        if (isset($filter['transaction_type'])) {
            $builder->where('st.transaction_type', $filter['transaction_type']);
        }
        if (isset($filter['block_id'])) {
            $builder->where('st.block_id', $filter['block_id']);
        }
        if (isset($filter['district_id'])) {
            $builder->where('st.district_id', $filter['district_id']);
        }
        if (isset($filter['year'])) {
            $builder->where('st.year', $filter['year']);
        }
        if (isset($filter['year_upto'])) {
            $builder->where('st.year <=', $filter['year_upto']);
        }
        if (isset($filter['month'])) {
            $builder->where('st.month', $filter['month']);
        }
        if (isset($filter['month_upto'])) {
            $builder->where('st.year <', $filter['year']);
            $builder->where('st.month <=', $filter['month_upto']);
        }
        if (isset($filter['agency_type_id'])) {
            $builder->where('st.agency_type_id', $filter['agency_type_id']);
        }
        if (isset($filter['fund_agency_id'])) {
            $builder->where('st.fund_agency_id', $filter['fund_agency_id']);
        }

        $query = $builder->get();
        return $query->getRow()->total;
    }

    public function getMpr($filter = [])
    {
        $filter['transaction_type'] = 'expense';
        $expenseTotal = $this->getTransactionTotalByType($filter);

        $filter['transaction_type'] = 'fund_receipt';
        $fundReceiptTotal = $this->getTransactionTotalByType($filter);

        $filter['transaction_type'] = null;
        $openingBalance = $fundReceiptTotal - $expenseTotal;

        $sql = "SELECT
            res.component_id,
            `number`,
            description,
            agency_type,
            parent,
            sort_order,
            row_type,
            (res.fr_upto_phy - res.exp_upto_phy) ob_phy,
            (res.fr_upto_fin - res.exp_upto_fin) ob_fin,
            res.bud_phy,
            res.bud_fin,
            res.fr_upto_cy_phy fr_upto_phy,
            res.fr_upto_cy_fin fr_upto_fin,
            res.fr_mon_phy,
            res.fr_mon_fin,
            (res.fr_upto_cy_phy + res.fr_mon_phy) fr_cum_phy,
            (res.fr_upto_cy_fin + res.fr_mon_fin) fr_cum_fin,
            res.exp_upto_cy_phy exp_upto_phy,
            res.exp_upto_cy_fin exp_upto_fin,
            res.exp_mon_phy,
            res.exp_mon_fin,
            (res.exp_upto_cy_phy + res.exp_mon_phy) exp_cum_phy,
            (res.exp_upto_cy_fin + res.exp_mon_fin) exp_cum_fin,
            (res.fr_upto_phy - res.exp_upto_phy + res.fr_mon_phy - res.exp_mon_phy) cb_phy,
            (res.fr_upto_fin - res.exp_upto_fin + res.fr_mon_fin - res.exp_mon_fin) cb_fin
          FROM (SELECT
              comp.*,
              bud.phy bud_phy,
              bud.fin bud_fin,
              COALESCE(expn_mon.phy, 0) exp_mon_phy,
              COALESCE(expn_mon.fin, 0) exp_mon_fin,
              COALESCE(fr_mon.phy, 0) fr_mon_phy,
              COALESCE(fr_mon.fin, 0) fr_mon_fin,
              COALESCE(exp_upto.phy, 0) exp_upto_phy,
              COALESCE(exp_upto.fin, 0) exp_upto_fin,
              COALESCE(fr_upto.phy, 0) fr_upto_phy,
              COALESCE(fr_upto.fin, 0) fr_upto_fin,
              COALESCE(exp_upto_cy.phy, 0) exp_
