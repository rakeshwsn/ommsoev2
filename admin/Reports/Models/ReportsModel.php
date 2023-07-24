<?php

namespace Admin\Reports\Models;

use CodeIgniter\Model;

class ReportsModel extends Model
{

  //niranjan --10-07-23
  public function getMpr($filter = [])
  {

    $last_year = ($filter['year_id'] - 1);

    $sql = "SELECT
        res.scomponent_id,
        res.component_id,
        `number`,
        description,
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
      COALESCE(exp_upto_cy.phy, 0) exp_upto_cy_phy,
      COALESCE(exp_upto_cy.fin, 0) exp_upto_cy_fin,
      COALESCE(fr_upto_cy.phy, 0) fr_upto_cy_phy,
      COALESCE(fr_upto_cy.fin, 0) fr_upto_cy_fin
    FROM (SELECT
        sca.id scomponent_id,
        sc.id component_id,
        sca.number,
        sc.description,
        sca.parent,
        sca.sort_order,
        sc.row_type,
        sc.category,
        agency_type_id
    FROM (SELECT
        c.*,
        sca.fund_agency_id,agency_type_id
      FROM soe_components_agency sca
        LEFT JOIN soe_components c
          ON component_id = c.id
      WHERE 1=1";
    if (!empty($filter['component_agency_type_id'])) {
      $sql .= " AND sca.agency_type_id = " . $filter['component_agency_type_id'];
    } else {
      $sql .= " AND sca.agency_type_id IS NULL ";
    }
    $sql .= " AND sca.fund_agency_id = " . $filter['fund_agency_id'] . ") sc
      LEFT JOIN soe_components_assign sca
        ON sca.component_id = sc.id
        AND sca.fund_agency_id = sc.fund_agency_id GROUP BY component_id,sca.number) comp
      LEFT JOIN user_group ug
        ON comp.agency_type_id = ug.id
      LEFT JOIN (SELECT
    component_id,
    agency_type_id,
    units,
    unit_cost,
    SUM(physical) phy,
    SUM(financial) fin,
    block_category
    FROM soe_budgets_plan bp
    LEFT JOIN soe_budgets b
      ON b.budget_plan_id = bp.ID
    WHERE fund_agency_id =  " . $filter['fund_agency_id'] . "
    AND bp.year =  " . $filter['year_id'];
    if (!empty($filter['block_id'])) {
      $sql .= " AND block_id =  " . $filter['block_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND district_id =  " . $filter['district_id'];
    }
    $sql .= " GROUP BY b.component_id) bud ON bud.component_id=comp.component_id";

    /* ******************transaction starts****************** */
    /* ******************month's expense****************** */
    $sql .= " LEFT JOIN (SELECT
          tc.component_id,
          SUM(physical) phy,
          SUM(financial) fin
        FROM soe_transactions t
          RIGHT JOIN soe_transaction_components tc
            ON t.id = tc.transaction_id
        WHERE t.deleted_at IS NULL
        AND tc.deleted_at IS NULL
        AND t.transaction_type = 'expense'";
    $sql .= " AND t.status = 1";

    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['month_id'])) {
      $sql .= " AND t.month = " . $filter['month_id'];
    }
    if (!empty($filter['year_id'])) {
      $sql .= " AND t.year = " . $filter['year_id'];
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }
    $sql .= " GROUP BY tc.component_id) expn_mon
        ON comp.component_id = expn_mon.component_id";
    /* ******************month's fundreceipt****************** */
    $sql .= " LEFT JOIN (SELECT
          tc.component_id,
          SUM(physical) phy,
          SUM(financial) fin
        FROM soe_transactions t
          RIGHT JOIN soe_transaction_components tc
            ON t.id = tc.transaction_id
        WHERE t.deleted_at IS NULL
        AND tc.deleted_at IS NULL
        AND t.transaction_type = 'fund_receipt'";
    $sql .= " AND t.status = 1";
    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    } else if (empty($filter['block_id'])) {
      // exclude block fund receipt when user is not block user.
      $sql .= " AND t.agency_type_id NOT IN (" . implode(',', $filter['block_users']) . ")";
    }

    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
      if (!empty($filter['block_user_id'])) {
        $sql .= " AND t.user_id = " . $filter['block_user_id'];
      }
    }
    if (!empty($filter['month_id'])) {
      $sql .= " AND t.month = " . $filter['month_id'];
    }
    if (!empty($filter['year_id'])) {
      $sql .= " AND t.year = " . $filter['year_id'];
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }
    $sql .= " GROUP BY tc.component_id) fr_mon
        ON comp.component_id = fr_mon.component_id";
    /* ******************expense upto last month****************** */
    $sql .= "
      LEFT JOIN (SELECT
          tc.component_id,
          SUM(physical) phy,
          SUM(financial) fin
        FROM soe_transactions t
          RIGHT JOIN soe_transaction_components tc
            ON t.id = tc.transaction_id
        WHERE t.deleted_at IS NULL
        AND tc.deleted_at IS NULL
        AND t.transaction_type = 'expense'";
    $sql .= " AND t.status = 1";
    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
      $this_month = ($filter['month_id'] - 1);
      $sql .= " AND ((t.year BETWEEN 0 AND $last_year)
        OR (t.year = " . $filter['year_id'] . "
        AND t.month BETWEEN 0 AND $this_month))";
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }
    $sql .= " GROUP BY tc.component_id) exp_upto
        ON comp.component_id = exp_upto.component_id";
    /* ******************fundreceipt upto last month****************** */
    $sql .= "
      LEFT JOIN (SELECT
          tc.component_id,
          SUM(physical) phy,
          SUM(financial) fin
        FROM soe_transactions t
          RIGHT JOIN soe_transaction_components tc
            ON t.id = tc.transaction_id
        WHERE t.deleted_at IS NULL
        AND tc.deleted_at IS NULL
        AND t.transaction_type = 'fund_receipt'";
    $sql .= " AND t.status = 1";
    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    } else if (empty($filter['block_id'])) {
      // exclude block fund receipt when user is not block user.
      $sql .= " AND t.agency_type_id NOT IN (" . implode(',', $filter['block_users']) . ")";
    }

    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
      if (!empty($filter['block_user_id'])) {
        $sql .= " AND t.user_id = " . $filter['block_user_id'];
      }
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
      $last_month = ($filter['month_id'] - 1);
      $sql .= " AND ((t.year BETWEEN 0 AND $last_year)
        OR (t.year = " . $filter['year_id'] . "
        AND t.month BETWEEN 0 AND $last_month))";
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }
    $sql .= " GROUP BY tc.component_id) fr_upto
        ON comp.component_id = fr_upto.component_id
      LEFT JOIN (SELECT
          tc.component_id,
          SUM(physical) phy,
          SUM(financial) fin
        FROM soe_transactions t
          RIGHT JOIN soe_transaction_components tc
            ON t.id = tc.transaction_id
        WHERE t.deleted_at IS NULL
        AND tc.deleted_at IS NULL
        AND t.transaction_type = 'fund_receipt'";
    $sql .= " AND t.status = 1";
    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    } else if (empty($filter['block_id'])) {
      // exclude block fund receipt when user is not block user.
      $sql .= " AND t.agency_type_id NOT IN (" . implode(',', $filter['block_users']) . ")";
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
      if (!empty($filter['block_user_id'])) {
        $sql .= " AND t.user_id = " . $filter['block_user_id'];
      }
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
      $last_month = ($filter['month_id'] - 1);
      $sql .= " AND (t.year = " . $filter['year_id'] . "
        AND t.month BETWEEN 0 AND $last_month)";
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }
    $sql .= " GROUP BY tc.component_id) fr_upto_cy
        ON comp.component_id = fr_upto_cy.component_id
      LEFT JOIN (SELECT
          tc.component_id,
          SUM(physical) phy,
          SUM(financial) fin
        FROM soe_transactions t
          RIGHT JOIN soe_transaction_components tc
            ON t.id = tc.transaction_id
        WHERE t.deleted_at IS NULL
        AND tc.deleted_at IS NULL
        AND t.transaction_type = 'expense'";
    $sql .= " AND t.status = 1";
    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
      $last_month = ($filter['month_id'] - 1);
      $sql .= " AND (t.year = " . $filter['year_id'] . "
        AND t.month BETWEEN 0 AND $last_month)";
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }
    $sql .= " GROUP BY tc.component_id) exp_upto_cy
        ON comp.component_id = exp_upto_cy.component_id) res ORDER BY sort_order";
    //echo $sql;exit;
    return $this->db->query($sql)->getResultArray();
  }

  public function getMprTest($filter = [])
  {

    $last_year = ($filter['year_id'] - 1);

    $sql = "SELECT
            res.scomponent_id,
            res.component_id,
            `number`,
            description,
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
              COALESCE(exp_upto_cy.phy, 0) exp_upto_cy_phy,
              COALESCE(exp_upto_cy.fin, 0) exp_upto_cy_fin,
              COALESCE(fr_upto_cy.phy, 0) fr_upto_cy_phy,
              COALESCE(fr_upto_cy.fin, 0) fr_upto_cy_fin
            FROM (SELECT
                sca.id scomponent_id,
                sc.id component_id,
                sca.number,
                sc.description,
                sca.parent,
                sca.sort_order,
                sc.row_type,
                sc.category,
                agency_type_id
              FROM (SELECT
                  c.*,
                  sca.fund_agency_id,
                  agency_type_id
                FROM soe_components_agency sca
                  LEFT JOIN soe_components c
                    ON component_id = c.id
                WHERE 1 = 1";
                if (!empty($filter['fagency_type_id'])) {
                  $sql .= " AND sca.agency_type_id IN (" . implode(',',$filter['fagency_type_id']) . ")";
                } 
                
                $sql .= " AND sca.fund_agency_id = " . $filter['fund_agency_id'] . ") sc
                LEFT JOIN soe_components_assign sca
                  ON sca.component_id = sc.id
                  AND sca.fund_agency_id = sc.fund_agency_id
                  WHERE sc.deleted_at IS NULL AND sca.deleted_at IS NULL
              GROUP BY component_id,
                      sca.number) comp
              LEFT JOIN user_group ug
                ON comp.agency_type_id = ug.id
                
                LEFT JOIN (SELECT
                  component_id,
                  agency_type_id,
                  units,
                  unit_cost,
                  SUM(physical) phy,
                  SUM(financial) fin,
                  block_category
                FROM soe_budgets_plan bp
                  LEFT JOIN soe_budgets b
                    ON b.budget_plan_id = bp.id
                WHERE  b.deleted_at IS NULL
                AND bp.deleted_at IS NULL and fund_agency_id = " . $filter['fund_agency_id'] . "
                AND bp.year = " . $filter['year_id'] ;

                if (!empty($filter['block_id'])) {
                  $sql .= " AND bp.block_id =  " . $filter['block_id'];
                }else if($filter['agency_type_id']==7){
                  $sql .= " AND bp.block_id =  0";
                }
                if (!empty($filter['district_id'])) {
                  $sql .= " AND bp.district_id =  " . $filter['district_id'];
                  
                }
                $sql .= " GROUP BY b.component_id) bud ON bud.component_id=comp.component_id";
                
                $sql .= " LEFT JOIN (SELECT
                  tc.component_id,
                  SUM(physical) phy,
                  SUM(financial) fin
                FROM soe_transactions t
                  RIGHT JOIN soe_transaction_components tc
                    ON t.id = tc.transaction_id
                WHERE t.deleted_at IS NULL
                AND tc.deleted_at IS NULL
                AND t.transaction_type = 'expense'
                AND t.status = 1";
                if (!empty($filter['month_id'])) {
                  $sql .= " AND t.month = " . $filter['month_id'];
                }
                if (!empty($filter['year_id'])) {
                  $sql .= " AND t.year = " . $filter['year_id'];
                }
                if (!empty($filter['district_id'])) {
                  $sql .= " AND t.district_id = " . $filter['district_id'];
                }
                if (!empty($filter['block_id'])) {
                  $sql .= " AND t.block_id = " . $filter['block_id'];
                }
                if (!empty($filter['fund_agency_id'])) {
                  $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
                }
                if (!empty($filter['fagency_type_id'])) {
                  $sql .= " AND t.agency_type_id IN (" . implode(',',$filter['fagency_type_id']) . ")";
                } 
                if (!empty($filter['block_id'])) {
                  $sql .= " AND t.user_id IN (SELECT id FROM user u WHERE u.block_id=" . $filter['block_id'].")";
                  
                }
                $sql .= " GROUP BY tc.component_id) expn_mon ON comp.component_id = expn_mon.component_id
              LEFT JOIN (SELECT
                  tc.component_id,
                  SUM(physical) phy,
                  SUM(financial) fin
                FROM soe_transactions t
                  RIGHT JOIN soe_transaction_components tc
                    ON t.id = tc.transaction_id
                WHERE t.deleted_at IS NULL
                AND tc.deleted_at IS NULL
                AND t.transaction_type = 'fund_receipt'
                AND t.status = 1";
                if ((!empty($filter['fagency_type_id'])  || $filter['block_id']) &&  !$filter['district_id']) {
                  $sql .= " AND  t.agency_type_id IN (" . implode(',',$filter['fagency_type_id']) . ")";
                } else  {
                  $sql .= " AND t.agency_type_id NOT IN (5, 6)";
                }
                if (!empty($filter['month_id'])) {
                  $sql .= " AND t.month = " . $filter['month_id'];
                }
                if (!empty($filter['year_id'])) {
                  $sql .= " AND t.year = " . $filter['year_id'];
                }
                if (!empty($filter['district_id'])) {
                  $sql .= " AND t.district_id = " . $filter['district_id'];
                }
                if (!empty($filter['block_id'])) {
                  $sql .= " AND t.block_id = " . $filter['block_id'];
                }
                if (!empty($filter['fund_agency_id'])) {
                  $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
                }
                $sql .= " GROUP BY tc.component_id) fr_mon ON comp.component_id = fr_mon.component_id
              LEFT JOIN (SELECT
                  tc.component_id,
                  SUM(physical) phy,
                  SUM(financial) fin
                FROM soe_transactions t
                  RIGHT JOIN soe_transaction_components tc
                    ON t.id = tc.transaction_id
                WHERE t.deleted_at IS NULL
                AND tc.deleted_at IS NULL
                AND t.transaction_type = 'expense'
                AND t.status = 1";

                if (!empty($filter['block_id'])) {
                  $sql .= " AND t.block_id = " . $filter['block_id'];
                }
                if (!empty($filter['district_id'])) {
                  $sql .= " AND t.district_id = " . $filter['district_id'];
                }

                if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
                  $this_month = ($filter['month_id'] - 1);
                  $sql .= " AND ((t.year BETWEEN 0 AND $last_year)
                    OR (t.year = " . $filter['year_id'] . "
                    AND t.month BETWEEN 0 AND $this_month))";
                }
                if (!empty($filter['fund_agency_id'])) {
                  $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
                }
                if (!empty($filter['fagency_type_id'])) {
                  $sql .= " AND t.agency_type_id IN (" . implode(',',$filter['fagency_type_id']) . ")";
                } 
                if (!empty($filter['block_id'])) {
                  $sql .= " AND t.user_id IN (SELECT id FROM user u WHERE u.block_id=" . $filter['block_id'].")";
                }
                $sql .= " GROUP BY tc.component_id) exp_upto
                ON comp.component_id = exp_upto.component_id
              LEFT JOIN (SELECT
                  tc.component_id,
                  SUM(physical) phy,
                  SUM(financial) fin
                FROM soe_transactions t
                  RIGHT JOIN soe_transaction_components tc
                    ON t.id = tc.transaction_id
                WHERE t.deleted_at IS NULL
                AND tc.deleted_at IS NULL
                AND t.transaction_type = 'fund_receipt'
                AND t.status = 1";
                if (!empty($filter['block_id'])) {
                  $sql .= " AND t.block_id = " . $filter['block_id'];
                }
                if (!empty($filter['district_id'])) {
                  $sql .= " AND t.district_id = " . $filter['district_id'];
                }
                if ((!empty($filter['fagency_type_id'])  || $filter['block_id']) &&  !$filter['district_id']) {
                  $sql .= " AND  t.agency_type_id IN (" . implode(',',$filter['fagency_type_id']) . ")";
                } else {
                  $sql .= " AND t.agency_type_id NOT IN (5, 6)";
                }
                if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
                  $last_month = ($filter['month_id'] - 1);
                  $sql .= " AND ((t.year BETWEEN 0 AND $last_year)
                    OR (t.year = " . $filter['year_id'] . "
                    AND t.month BETWEEN 0 AND $last_month))";
                }
                if (!empty($filter['fund_agency_id'])) {
                  $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
                }
               
                $sql .= " GROUP BY tc.component_id) fr_upto ON comp.component_id = fr_upto.component_id
              LEFT JOIN (SELECT
                  tc.component_id,
                  SUM(physical) phy,
                  SUM(financial) fin
                FROM soe_transactions t
                  RIGHT JOIN soe_transaction_components tc
                    ON t.id = tc.transaction_id
                WHERE t.deleted_at IS NULL
                AND tc.deleted_at IS NULL
                AND t.transaction_type = 'fund_receipt'
                AND t.status = 1";
                if (!empty($filter['block_id'])) {
                  $sql .= " AND t.block_id = " . $filter['block_id'];
                }
                if (!empty($filter['district_id'])) {
                  $sql .= " AND t.district_id = " . $filter['district_id'];
                }
                if ((!empty($filter['fagency_type_id'])  || $filter['block_id']) &&  !$filter['district_id']) {
                  $sql .= " AND  t.agency_type_id IN (" . implode(',',$filter['fagency_type_id']) . ")";
                } else {
                  $sql .= " AND t.agency_type_id NOT IN (5, 6)";
                }
                if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
                  $last_month = ($filter['month_id'] - 1);
                  $sql .= " AND (t.year = " . $filter['year_id'] . "
                    AND t.month BETWEEN 0 AND $last_month)";
                }
                if (!empty($filter['fund_agency_id'])) {
                  $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
                }
                $sql .= " GROUP BY tc.component_id) fr_upto_cy ON comp.component_id = fr_upto_cy.component_id
              LEFT JOIN (SELECT
                  tc.component_id,
                  SUM(physical) phy,
                  SUM(financial) fin
                FROM soe_transactions t
                  RIGHT JOIN soe_transaction_components tc
                    ON t.id = tc.transaction_id
                WHERE t.deleted_at IS NULL
                AND tc.deleted_at IS NULL
                AND t.transaction_type = 'expense'
                AND t.status = 1";
                
                if (!empty($filter['block_id'])) {
                  $sql .= " AND t.block_id = " . $filter['block_id'];
                }
                if (!empty($filter['district_id'])) {
                  $sql .= " AND t.district_id = " . $filter['district_id'];
                }
                if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
                  $last_month = ($filter['month_id'] - 1);
                  $sql .= " AND (t.year = " . $filter['year_id'] . "
                    AND t.month BETWEEN 0 AND $last_month)";
                }
                if (!empty($filter['fund_agency_id'])) {
                  $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
                }
                if (!empty($filter['fagency_type_id'])) {
                  $sql .= " AND t.agency_type_id IN (" . implode(',',$filter['fagency_type_id']) . ")";
                } 
                if (!empty($filter['block_id'])) {
                  $sql .= " AND t.user_id IN (SELECT id FROM user u WHERE u.block_id=" . $filter['block_id'].")";
                }
                $sql .= " GROUP BY tc.component_id) exp_upto_cy ON comp.component_id = exp_upto_cy.component_id) res
          ORDER BY sort_order";
   //echo $sql;exit;
    return $this->db->query($sql)->getResultArray();
  }

  public function getMprTestNew($filter = [])
  {

    $last_year = ($filter['year_id'] - 1);

    $sql = "SELECT
            res.scomponent_id,
            res.component_id,
            `number`,
            description,
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
              COALESCE(exp_upto_cy.phy, 0) exp_upto_cy_phy,
              COALESCE(exp_upto_cy.fin, 0) exp_upto_cy_fin,
              COALESCE(fr_upto_cy.phy, 0) fr_upto_cy_phy,
              COALESCE(fr_upto_cy.fin, 0) fr_upto_cy_fin
            FROM (SELECT
                sca.id scomponent_id,
                sc.id component_id,
                sca.number,
                sc.description,
                sca.parent,
                sca.sort_order,
                sc.row_type,
                sc.category,
                agency_type_id
              FROM (SELECT
                  c.*,
                  sca.fund_agency_id,
                  agency_type_id
                FROM soe_components_agency sca
                  LEFT JOIN soe_components c
                    ON component_id = c.id
                WHERE 1 = 1";
                if (!empty($filter['fagency_type_id'])) {
                  $sql .= " AND sca.agency_type_id IN (" . implode(',',$filter['fagency_type_id']) . ")";
                } 
                
                $sql .= " AND sca.fund_agency_id = " . $filter['fund_agency_id'] . ") sc
                LEFT JOIN soe_components_assign sca
                  ON sca.component_id = sc.id
                  AND sca.fund_agency_id = sc.fund_agency_id
                  WHERE sc.deleted_at IS NULL AND sca.deleted_at IS NULL
              GROUP BY component_id,
                      sca.number) comp
              LEFT JOIN user_group ug
                ON comp.agency_type_id = ug.id
                
                LEFT JOIN (SELECT
                  component_id,
                  agency_type_id,
                  units,
                  unit_cost,
                  SUM(physical) phy,
                  SUM(financial) fin,
                  block_category
                FROM soe_budgets_plan bp
                  LEFT JOIN soe_budgets b
                    ON b.budget_plan_id = bp.id
                WHERE  b.deleted_at IS NULL
                AND bp.deleted_at IS NULL and fund_agency_id = " . $filter['fund_agency_id'] . "
                AND bp.year = " . $filter['year_id'] ;

                if (!empty($filter['block_id'])) {
                  $sql .= " AND bp.block_id =  " . $filter['block_id'];
                }else if(!empty($filter['agency_id'])){
                  $sql .= " AND bp.block_id =  0";
                }
                if (!empty($filter['district_id'])) {
                  $sql .= " AND bp.district_id =  " . $filter['district_id'];
                  
                }
                $sql .= " GROUP BY b.component_id) bud ON bud.component_id=comp.component_id";
                
                $sql .= " LEFT JOIN (SELECT
                  tc.component_id,
                  SUM(physical) phy,
                  SUM(financial) fin
                FROM soe_transactions t
                  RIGHT JOIN soe_transaction_components tc
                    ON t.id = tc.transaction_id
                WHERE t.deleted_at IS NULL
                AND tc.deleted_at IS NULL
                AND t.transaction_type = 'expense'
                AND t.status = 1";
                if (!empty($filter['month_id'])) {
                  $sql .= " AND t.month = " . $filter['month_id'];
                }
                if (!empty($filter['year_id'])) {
                  $sql .= " AND t.year = " . $filter['year_id'];
                }
                if (!empty($filter['district_id'])) {
                  $sql .= " AND t.district_id = " . $filter['district_id'];
                }
                if (!empty($filter['block_id'])) {
                  $sql .= " AND t.block_id = " . $filter['block_id'];
                }
                if (!empty($filter['fund_agency_id'])) {
                  $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
                }
                if (!empty($filter['fagency_type_id'])) {
                  $sql .= " AND t.agency_type_id IN (" . implode(',',$filter['fagency_type_id']) . ")";
                } 
                if (!empty($filter['block_id'])) {
                  $sql .= " AND t.user_id IN (SELECT id FROM user u WHERE u.block_id=" . $filter['block_id'].")";
                  
                }
                $sql .= " GROUP BY tc.component_id) expn_mon ON comp.component_id = expn_mon.component_id
              LEFT JOIN (SELECT
                  tc.component_id,
                  SUM(physical) phy,
                  SUM(financial) fin
                FROM soe_transactions t
                  RIGHT JOIN soe_transaction_components tc
                    ON t.id = tc.transaction_id
                WHERE t.deleted_at IS NULL
                AND tc.deleted_at IS NULL
                AND t.transaction_type = 'fund_receipt'
                AND t.status = 1";
                
                if(!empty($data['fund_receipt_not'])){
                    $sql .= " AND  t.agency_type_id IN (" . implode(',',$filter['fagency_type_id']) . ")";
                }else  {
                    $sql .= " AND t.agency_type_id NOT IN (5,6)";
                }
                if (!empty($filter['month_id'])) {
                  $sql .= " AND t.month = " . $filter['month_id'];
                }
                if (!empty($filter['year_id'])) {
                  $sql .= " AND t.year = " . $filter['year_id'];
                }
                if (!empty($filter['district_id'])) {
                  $sql .= " AND t.district_id = " . $filter['district_id'];
                }
                if (!empty($filter['block_id'])) {
                  $sql .= " AND t.block_id = " . $filter['block_id'];
                }
                if (!empty($filter['fund_agency_id'])) {
                  $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
                }
                $sql .= " GROUP BY tc.component_id) fr_mon ON comp.component_id = fr_mon.component_id
              LEFT JOIN (SELECT
                  tc.component_id,
                  SUM(physical) phy,
                  SUM(financial) fin
                FROM soe_transactions t
                  RIGHT JOIN soe_transaction_components tc
                    ON t.id = tc.transaction_id
                WHERE t.deleted_at IS NULL
                AND tc.deleted_at IS NULL
                AND t.transaction_type = 'expense'
                AND t.status = 1";

                if (!empty($filter['block_id'])) {
                  $sql .= " AND t.block_id = " . $filter['block_id'];
                }
                if (!empty($filter['district_id'])) {
                  $sql .= " AND t.district_id = " . $filter['district_id'];
                }

                if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
                  $this_month = ($filter['month_id'] - 1);
                  $sql .= " AND ((t.year BETWEEN 0 AND $last_year)
                    OR (t.year = " . $filter['year_id'] . "
                    AND t.month BETWEEN 0 AND $this_month))";
                }
                if (!empty($filter['fund_agency_id'])) {
                  $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
                }
                if (!empty($filter['fagency_type_id'])) {
                  $sql .= " AND t.agency_type_id IN (" . implode(',',$filter['fagency_type_id']) . ")";
                } 
                if (!empty($filter['block_id'])) {
                  $sql .= " AND t.user_id IN (SELECT id FROM user u WHERE u.block_id=" . $filter['block_id'].")";
                }
                $sql .= " GROUP BY tc.component_id) exp_upto
                ON comp.component_id = exp_upto.component_id
              LEFT JOIN (SELECT
                  tc.component_id,
                  SUM(physical) phy,
                  SUM(financial) fin
                FROM soe_transactions t
                  RIGHT JOIN soe_transaction_components tc
                    ON t.id = tc.transaction_id
                WHERE t.deleted_at IS NULL
                AND tc.deleted_at IS NULL
                AND t.transaction_type = 'fund_receipt'
                AND t.status = 1";
                if (!empty($filter['block_id'])) {
                  $sql .= " AND t.block_id = " . $filter['block_id'];
                }
                if (!empty($filter['district_id'])) {
                  $sql .= " AND t.district_id = " . $filter['district_id'];
                }

                if(!empty($data['fund_receipt_not'])){
                  $sql .= " AND  t.agency_type_id IN (" . implode(',',$filter['fagency_type_id']) . ")";
                }else  {
                    $sql .= " AND t.agency_type_id NOT IN (5,6)";
                }

                if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
                  $last_month = ($filter['month_id'] - 1);
                  $sql .= " AND ((t.year BETWEEN 0 AND $last_year)
                    OR (t.year = " . $filter['year_id'] . "
                    AND t.month BETWEEN 0 AND $last_month))";
                }
                if (!empty($filter['fund_agency_id'])) {
                  $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
                }
               
                $sql .= " GROUP BY tc.component_id) fr_upto ON comp.component_id = fr_upto.component_id
              LEFT JOIN (SELECT
                  tc.component_id,
                  SUM(physical) phy,
                  SUM(financial) fin
                FROM soe_transactions t
                  RIGHT JOIN soe_transaction_components tc
                    ON t.id = tc.transaction_id
                WHERE t.deleted_at IS NULL
                AND tc.deleted_at IS NULL
                AND t.transaction_type = 'fund_receipt'
                AND t.status = 1";
                if (!empty($filter['block_id'])) {
                  $sql .= " AND t.block_id = " . $filter['block_id'];
                }
                if (!empty($filter['district_id'])) {
                  $sql .= " AND t.district_id = " . $filter['district_id'];
                }

                if(!empty($data['fund_receipt_not'])){
                  $sql .= " AND  t.agency_type_id IN (" . implode(',',$filter['fagency_type_id']) . ")";
                }else  {
                    $sql .= " AND t.agency_type_id NOT IN (5,6)";
                }

                if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
                  $last_month = ($filter['month_id'] - 1);
                  $sql .= " AND (t.year = " . $filter['year_id'] . "
                    AND t.month BETWEEN 0 AND $last_month)";
                }
                if (!empty($filter['fund_agency_id'])) {
                  $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
                }
                $sql .= " GROUP BY tc.component_id) fr_upto_cy ON comp.component_id = fr_upto_cy.component_id
              LEFT JOIN (SELECT
                  tc.component_id,
                  SUM(physical) phy,
                  SUM(financial) fin
                FROM soe_transactions t
                  RIGHT JOIN soe_transaction_components tc
                    ON t.id = tc.transaction_id
                WHERE t.deleted_at IS NULL
                AND tc.deleted_at IS NULL
                AND t.transaction_type = 'expense'
                AND t.status = 1";
                
                if (!empty($filter['block_id'])) {
                  $sql .= " AND t.block_id = " . $filter['block_id'];
                }
                if (!empty($filter['district_id'])) {
                  $sql .= " AND t.district_id = " . $filter['district_id'];
                }
                if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
                  $last_month = ($filter['month_id'] - 1);
                  $sql .= " AND (t.year = " . $filter['year_id'] . "
                    AND t.month BETWEEN 0 AND $last_month)";
                }
                if (!empty($filter['fund_agency_id'])) {
                  $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
                }
                if (!empty($filter['fagency_type_id'])) {
                  $sql .= " AND t.agency_type_id IN (" . implode(',',$filter['fagency_type_id']) . ")";
                } 
                if (!empty($filter['block_id'])) {
                  $sql .= " AND t.user_id IN (SELECT id FROM user u WHERE u.block_id=" . $filter['block_id'].")";
                }
                $sql .= " GROUP BY tc.component_id) exp_upto_cy ON comp.component_id = exp_upto_cy.component_id) res
          ORDER BY sort_order";
          //echo $sql;exit;
    return $this->db->query($sql)->getResultArray();
  }

  //rakesh -- 01/04/23 -- budget change
  public function getMpr_old($filter = [])
  {

    $last_year = ($filter['year_id'] - 1);

    $sql = "SELECT
    res.scomponent_id,
  res.component_id,
  `number`,
  description,
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
    COALESCE(exp_upto_cy.phy, 0) exp_upto_cy_phy,
    COALESCE(exp_upto_cy.fin, 0) exp_upto_cy_fin,
    COALESCE(fr_upto_cy.phy, 0) fr_upto_cy_phy,
    COALESCE(fr_upto_cy.fin, 0) fr_upto_cy_fin
  FROM (SELECT
      sca.id scomponent_id,
      sc.id component_id,
      sca.number,
      sc.description,
      sca.parent,
      sca.sort_order,
      sc.row_type,
      sc.category,
      agency_type_id
  FROM (SELECT
      c.*,
      sca.fund_agency_id,agency_type_id
    FROM soe_components_agency sca
      LEFT JOIN soe_components c
        ON component_id = c.id
    WHERE 1=1";
    if (!empty($filter['component_agency_type_id'])) {
      $sql .= " AND sca.agency_type_id = " . $filter['component_agency_type_id'];
    } else {
      $sql .= " AND sca.agency_type_id IS NULL ";
    }
    $sql .= " AND sca.fund_agency_id = " . $filter['fund_agency_id'] . ") sc
    LEFT JOIN soe_components_assign sca
      ON sca.component_id = sc.id
      AND sca.fund_agency_id = sc.fund_agency_id GROUP BY component_id,sca.number) comp
    LEFT JOIN user_group ug
      ON comp.agency_type_id = ug.id
    LEFT JOIN (SELECT
  component_id,
  agency_type_id,
  units,
  unit_cost,
  SUM(physical) phy,
  SUM(financial) fin,
  block_category
FROM soe_budgets_plan bp
  LEFT JOIN soe_budgets b
    ON b.budget_plan_id = bp.ID
WHERE 
b.deleted_at IS NULL AND bp.deleted_at IS NULL and 
bp.fund_agency_id =  " . $filter['fund_agency_id'] . "
AND bp.year =  " . $filter['year_id'];
    if (!empty($filter['block_id'])) {
      $sql .= " AND block_id =  " . $filter['block_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND district_id =  " . $filter['district_id'];
    }
    $sql .= " GROUP BY b.component_id) bud ON bud.component_id=comp.component_id";

    /* ******************transaction starts****************** */
    /* ******************month's expense****************** */
    $sql .= " LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'expense'";
    $sql .= " AND t.status = 1";

    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['month_id'])) {
      $sql .= " AND t.month = " . $filter['month_id'];
    }
    if (!empty($filter['year_id'])) {
      $sql .= " AND t.year = " . $filter['year_id'];
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }
    if (!empty($filter['agency_type_id'])) {
      $sql .= " AND t.agency_type_id IN (" . implode(',', (array)$filter['agency_type_id']) . ")";
    }
    $sql .= " GROUP BY tc.component_id) expn_mon
      ON comp.component_id = expn_mon.component_id";
    /* ******************month's fundreceipt****************** */
    $sql .= " LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'fund_receipt'";
    $sql .= " AND t.status = 1";
    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    } else if (!empty($filter['agency_type_id'])) { //added by niranjan
      $sql .= " AND t.agency_type_id IN (" . implode(',', (array)$filter['agency_type_id']) . ")";
    } else if (empty($filter['block_id'])) {
      // exclude block fund receipt when user is not block user.
      $sql .= " AND t.agency_type_id NOT IN (" . implode(',', $filter['block_users']) . ")";
    }

    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
      if (!empty($filter['block_user_id'])) {
        $sql .= " AND t.user_id = " . $filter['block_user_id'];
      }
    }
    if (!empty($filter['month_id'])) {
      $sql .= " AND t.month = " . $filter['month_id'];
    }
    if (!empty($filter['year_id'])) {
      $sql .= " AND t.year = " . $filter['year_id'];
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }
    $sql .= " GROUP BY tc.component_id) fr_mon
      ON comp.component_id = fr_mon.component_id";
    /* ******************expense upto last month****************** */
    $sql .= "
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'expense'";
    $sql .= " AND t.status = 1";
    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
      $this_month = ($filter['month_id'] - 1);
      $sql .= " AND ((t.year BETWEEN 0 AND $last_year)
      OR (t.year = " . $filter['year_id'] . "
      AND t.month BETWEEN 0 AND $this_month))";
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }
    if (!empty($filter['agency_type_id'])) { //added by niranjan
      $sql .= " AND t.agency_type_id IN (" . implode(',', (array)$filter['agency_type_id']) . ")";
    }
    $sql .= " GROUP BY tc.component_id) exp_upto
      ON comp.component_id = exp_upto.component_id";
    /* ******************fundreceipt upto last month****************** */
    $sql .= "
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'fund_receipt'";
    $sql .= " AND t.status = 1";
    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    } else if (!empty($filter['agency_type_id'])) { //added by niranjan
      $sql .= " AND t.agency_type_id IN (" . implode(',', (array)$filter['agency_type_id']) . ")";
    } else if (empty($filter['block_id'])) {
      // exclude block fund receipt when user is not block user.
      $sql .= " AND t.agency_type_id NOT IN (" . implode(',', $filter['block_users']) . ")";
    }

    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
      if (!empty($filter['block_user_id'])) {
        $sql .= " AND t.user_id = " . $filter['block_user_id'];
      }
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
      $last_month = ($filter['month_id'] - 1);
      $sql .= " AND ((t.year BETWEEN 0 AND $last_year)
      OR (t.year = " . $filter['year_id'] . "
      AND t.month BETWEEN 0 AND $last_month))";
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }
    $sql .= " GROUP BY tc.component_id) fr_upto
      ON comp.component_id = fr_upto.component_id
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'fund_receipt'";
    $sql .= " AND t.status = 1";
    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    } else if (!empty($filter['agency_type_id'])) { //added by niranjan
      $sql .= " AND t.agency_type_id IN (" . implode(',', (array)$filter['agency_type_id']) . ")";
    } else if (empty($filter['block_id'])) {
      // exclude block fund receipt when user is not block user.
      $sql .= " AND t.agency_type_id NOT IN (" . implode(',', $filter['block_users']) . ")";
    }

    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
      if (!empty($filter['block_user_id'])) {
        $sql .= " AND t.user_id = " . $filter['block_user_id'];
      }
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
      $last_month = ($filter['month_id'] - 1);
      $sql .= " AND (t.year = " . $filter['year_id'] . "
      AND t.month BETWEEN 0 AND $last_month)";
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }
    $sql .= " GROUP BY tc.component_id) fr_upto_cy
      ON comp.component_id = fr_upto_cy.component_id
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'expense'";
    $sql .= " AND t.status = 1";
    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
      $last_month = ($filter['month_id'] - 1);
      $sql .= " AND (t.year = " . $filter['year_id'] . "
      AND t.month BETWEEN 0 AND $last_month)";
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }

    if (!empty($filter['agency_type_id'])) {
      $sql .= " AND t.agency_type_id IN (" . implode(',', (array)$filter['agency_type_id']) . ")";
    }
    $sql .= " GROUP BY tc.component_id) exp_upto_cy
      ON comp.component_id = exp_upto_cy.component_id) res ORDER BY sort_order";
    //echo $sql;exit;
    return $this->db->query($sql)->getResultArray();
  }

  public function getBlockMpr($filter = [])
  {

    $last_year = ($filter['year_id'] - 1);

    $sql = "SELECT
    res.scomponent_id,
  res.component_id,
  `number`,
  description,
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
    COALESCE(exp_upto_cy.phy, 0) exp_upto_cy_phy,
    COALESCE(exp_upto_cy.fin, 0) exp_upto_cy_fin,
    COALESCE(fr_upto_cy.phy, 0) fr_upto_cy_phy,
    COALESCE(fr_upto_cy.fin, 0) fr_upto_cy_fin
  FROM (SELECT
      sca.id scomponent_id,
      sc.id component_id,
      sca.number,
      sc.description,
      sca.parent,
      sca.sort_order,
      sc.row_type,
      sc.category,
      agency_type_id
  FROM (SELECT
      c.*,
      sca.fund_agency_id,agency_type_id
    FROM soe_components_agency sca
      LEFT JOIN soe_components c
        ON component_id = c.id
    WHERE 1=1";
    if (!empty($filter['component_agency_type_id'])) {
      $sql .= " AND sca.agency_type_id = " . $filter['component_agency_type_id'];
    } else {
      $sql .= " AND sca.agency_type_id IS NULL ";
    }
    $sql .= " AND sca.fund_agency_id = " . $filter['fund_agency_id'] . ") sc
    LEFT JOIN soe_components_assign sca
      ON sca.component_id = sc.id
      AND sca.fund_agency_id = sc.fund_agency_id GROUP BY component_id,sca.number) comp
    LEFT JOIN user_group ug
      ON comp.agency_type_id = ug.id
    LEFT JOIN (SELECT
  component_id,
  agency_type_id,
  units,
  unit_cost,
  SUM(physical) phy,
  SUM(financial) fin,
  block_category
FROM soe_budgets_plan bp
  LEFT JOIN soe_budgets b
    ON b.budget_plan_id = bp.ID
WHERE 
b.deleted_at IS NULL AND bp.deleted_at IS NULL and 
bp.fund_agency_id =  " . $filter['fund_agency_id'] . "
AND bp.year =  " . $filter['year_id'];
    if (!empty($filter['block_id'])) {
      $sql .= " AND block_id =  " . $filter['block_id'];
    }
    $sql .= " GROUP BY b.component_id) bud ON bud.component_id=comp.component_id";

    /* ******************transaction starts****************** */
    /* ******************month's expense****************** */
    $sql .= " LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'expense'";
    $sql .= " AND t.status = 1";

    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
    }
    if (!empty($filter['month_id'])) {
      $sql .= " AND t.month = " . $filter['month_id'];
    }
    if (!empty($filter['year_id'])) {
      $sql .= " AND t.year = " . $filter['year_id'];
    }
    if (!empty($filter['agency_type_id'])) {
      $sql .= " AND t.agency_type_id IN (" . implode(',', (array)$filter['agency_type_id']) . ")";
    }
    $sql .= " GROUP BY tc.component_id) expn_mon
      ON comp.component_id = expn_mon.component_id";
    /* ******************month's fundreceipt****************** */
    $sql .= " LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'fund_receipt'";
    $sql .= " AND t.status = 1";
    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    } else if (empty($filter['block_id'])) {
      // exclude block fund receipt when user is not block user.
      $sql .= " AND t.agency_type_id NOT IN (" . implode(',', $filter['block_users']) . ")";
    }

    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
      if (!empty($filter['block_user_id'])) {
        $sql .= " AND t.user_id = " . $filter['block_user_id'];
      }
    }
    if (!empty($filter['month_id'])) {
      $sql .= " AND t.month = " . $filter['month_id'];
    }
    if (!empty($filter['year_id'])) {
      $sql .= " AND t.year = " . $filter['year_id'];
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }
    $sql .= " GROUP BY tc.component_id) fr_mon
      ON comp.component_id = fr_mon.component_id";
    /* ******************expense upto last month****************** */
    $sql .= "
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'expense'";
    $sql .= " AND t.status = 1";
    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
      $this_month = ($filter['month_id'] - 1);
      $sql .= " AND ((t.year BETWEEN 0 AND $last_year)
      OR (t.year = " . $filter['year_id'] . "
      AND t.month BETWEEN 0 AND $this_month))";
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }
    if (!empty($filter['agency_type_id'])) { //added by niranjan
      $sql .= " AND t.agency_type_id IN (" . implode(',', (array)$filter['agency_type_id']) . ")";
    }
    $sql .= " GROUP BY tc.component_id) exp_upto
      ON comp.component_id = exp_upto.component_id";
    /* ******************fundreceipt upto last month****************** */
    $sql .= "
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'fund_receipt'";
    $sql .= " AND t.status = 1";

    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    } else if (!empty($filter['agency_type_id'])) { //added by niranjan
      $sql .= " AND t.agency_type_id IN (" . implode(',', (array)$filter['agency_type_id']) . ")";
    } else if (empty($filter['block_id'])) {
      // exclude block fund receipt when user is not block user.
      $sql .= " AND t.agency_type_id NOT IN (" . implode(',', $filter['block_users']) . ")";
    }

    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
      if (!empty($filter['block_user_id'])) {
        $sql .= " AND t.user_id = " . $filter['block_user_id'];
      }
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
      $last_month = ($filter['month_id'] - 1);
      $sql .= " AND ((t.year BETWEEN 0 AND $last_year)
      OR (t.year = " . $filter['year_id'] . "
      AND t.month BETWEEN 0 AND $last_month))";
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }
    $sql .= " GROUP BY tc.component_id) fr_upto
      ON comp.component_id = fr_upto.component_id
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'fund_receipt'";
    $sql .= " AND t.status = 1";
    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    } else if (!empty($filter['agency_type_id'])) { //added by niranjan
      $sql .= " AND t.agency_type_id IN (" . implode(',', (array)$filter['agency_type_id']) . ")";
    } else if (empty($filter['block_id'])) {
      // exclude block fund receipt when user is not block user.
      $sql .= " AND t.agency_type_id NOT IN (" . implode(',', $filter['block_users']) . ")";
    }

    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
      if (!empty($filter['block_user_id'])) {
        $sql .= " AND t.user_id = " . $filter['block_user_id'];
      }
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
      $last_month = ($filter['month_id'] - 1);
      $sql .= " AND (t.year = " . $filter['year_id'] . "
      AND t.month BETWEEN 0 AND $last_month)";
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }
    $sql .= " GROUP BY tc.component_id) fr_upto_cy
      ON comp.component_id = fr_upto_cy.component_id
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'expense'";
    $sql .= " AND t.status = 1";
    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
      $last_month = ($filter['month_id'] - 1);
      $sql .= " AND (t.year = " . $filter['year_id'] . "
      AND t.month BETWEEN 0 AND $last_month)";
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }

    if (!empty($filter['agency_type_id'])) {
      $sql .= " AND t.agency_type_id IN (" . implode(',', (array)$filter['agency_type_id']) . ")";
    }
    $sql .= " GROUP BY tc.component_id) exp_upto_cy
      ON comp.component_id = exp_upto_cy.component_id) res ORDER BY sort_order";
    //echo $sql;exit;
    return $this->db->query($sql)->getResultArray();
  }

  public function getAbstractBudget($filter = [])
  {

    $last_year = ($filter['year_id'] - 1);

    $sql = "SELECT mpr.block_category,
SUM(mpr.ob_phy) ob_phy,
SUM(mpr.ob_fin) ob_fin,
SUM(mpr.bud_phy) bud_phy,
SUM(mpr.bud_fin) bud_fin,
SUM(mpr.fr_upto_phy) fr_upto_phy,
SUM(mpr.fr_upto_fin) fr_upto_fin,
SUM(mpr.fr_mon_phy) fr_mon_phy,
SUM(mpr.fr_mon_fin) fr_mon_fin,
SUM(mpr.fr_cum_phy) fr_cum_phy,
SUM(mpr.fr_cum_fin) fr_cum_fin,
SUM(mpr.exp_upto_phy) exp_upto_phy,
SUM(mpr.exp_upto_fin) exp_upto_fin,
SUM(mpr.exp_mon_phy) exp_mon_phy,
SUM(mpr.exp_mon_fin) exp_mon_fin,
SUM(mpr.exp_cum_phy) exp_cum_phy,
SUM(mpr.exp_cum_fin) exp_cum_fin,
SUM(mpr.cb_phy) cb_phy,
SUM(mpr.cb_fin) cb_fin
FROM (SELECT
  res.component_id,
  `number`,
  description,
  agency_type,
  parent,
  sort_order,
  row_type,
  res.block_category,
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
    bud.block_category,
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
    COALESCE(exp_upto_cy.phy, 0) exp_upto_cy_phy,
    COALESCE(exp_upto_cy.fin, 0) exp_upto_cy_fin,
    COALESCE(fr_upto_cy.phy, 0) fr_upto_cy_phy,
    COALESCE(fr_upto_cy.fin, 0) fr_upto_cy_fin
  FROM (SELECT
  sc.id component_id,
  sc.number,
  sc.description,
  sc.parent,
  sc.sort_order,
  sc.row_type,
  ug.name agency_type,
  sc.category
FROM (SELECT * FROM soe_components
WHERE deleted_at IS NULL)sc
  LEFT JOIN (SELECT * FROM soe_budgets2
  GROUP BY component_id) sb 
    ON sb.component_id = sc.id
  LEFT JOIN user_group ug
    ON sb.agency_type_id = ug.id WHERE 1=1";
    if (!empty($filter['agency_type_id'])) {
      if (is_array($filter['agency_type_id'])) {
        $sql .= " AND ( sb.agency_type_id IN (" . implode(',', $filter['agency_type_id']) . ")";
      } else {
        $sql .= " AND ( sb.agency_type_id = " . $filter['agency_type_id'];
      }
      $sql .= " OR sb.agency_type_id is NULL OR sc.row_type = 'heading')";
    }
    if (!empty($filter['category'])) {
      if (is_array($filter['category'])) {
        $sql .= " AND sc.category IN (" . implode(',', $filter['category']) . ")";
      } else {
        $sql .= " AND sc.category = '" . $filter['category'] . "'";
      }
    }
    $sql .= " ) comp LEFT JOIN
    (SELECT
  sbb.block_id,
  sbb.budget_id,
  sbb.district_id,
  sb.component_id,
  sb.year,
  sb.unit_cost,
  SUM(sb.physical) phy,
  SUM(sb.financial) fin,
  sb.agency_type_id,sb.block_category
  FROM soe_block_budgets sbb
  LEFT JOIN soe_budgets2 sb
    ON sbb.budget_id = sb.id
WHERE sb.deleted_at IS NULL
AND sbb.deleted_at IS NULL";
    if (!empty($filter['block_id'])) {
      $sql .= " AND sbb.block_id = " . $filter['block_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND sbb.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['year_id'])) {
      $sql .= " AND sb.year = " . $filter['year_id'];
    }
    if (!empty($filter['agency_type_id'])) {
      if (is_array($filter['agency_type_id'])) {
        $sql .= " AND ( sb.agency_type_id IN (" . implode(',', $filter['agency_type_id']) . ")";
      } else {
        $sql .= " AND ( sb.agency_type_id = " . $filter['agency_type_id'];
      }
      $sql .= " OR sb.agency_type_id is NULL or sb.agency_type_id = 0)";
    }
    $sql .= " GROUP BY sb.component_id) bud ON bud.component_id=comp.component_id
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        LEFT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'expense'";
    $sql .= " AND t.status = 1";
    if (!empty($filter['user_id'])) {
      $sql .= " AND t.user_id = " . $filter['user_id'];
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['agency_type_id'])) {
      if (is_array($filter['agency_type_id'])) {
        $sql .= " AND t.agency_type_id IN (" . implode(',', $filter['agency_type_id']) . ")";
      } else {
        $sql .= " AND t.agency_type_id = " . $filter['agency_type_id'];
      }
    }
    if (!empty($filter['month_id'])) {
      $sql .= " AND t.month = " . $filter['month_id'];
    }
    if (!empty($filter['year_id'])) {
      $sql .= " AND t.year = " . $filter['year_id'];
    }
    $sql .= " GROUP BY tc.component_id) expn_mon
      ON comp.component_id = expn_mon.component_id
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        LEFT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'fund_receipt'";
    $sql .= " AND t.status = 1";
    if (!empty($filter['user_id'])) {
      $sql .= " AND t.user_id = " . $filter['user_id'];
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
      //exclude fr from block
      if (empty($filter['block_id'])) {
        //$sql .= " AND t.block_id = 0";
      }
    } else {
      //$sql .= " AND t.block_id = 0";
    }

    if (!empty($filter['agency_type_id'])) {
      if (is_array($filter['agency_type_id'])) {
        $sql .= " AND t.agency_type_id IN (" . implode(',', $filter['agency_type_id']) . ")";
      } else {
        $sql .= " AND t.agency_type_id = " . $filter['agency_type_id'];
      }
    }
    if (!empty($filter['month_id'])) {
      $sql .= " AND t.month = " . $filter['month_id'];
    }
    if (!empty($filter['year_id'])) {
      $sql .= " AND t.year = " . $filter['year_id'];
    }
    $sql .= " GROUP BY tc.component_id) fr_mon
      ON comp.component_id = fr_mon.component_id
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        LEFT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'expense'";
    $sql .= " AND t.status = 1";
    if (!empty($filter['user_id'])) {
      $sql .= " AND t.user_id = " . $filter['user_id'];
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['agency_type_id'])) {
      if (is_array($filter['agency_type_id'])) {
        $sql .= " AND t.agency_type_id IN (" . implode(',', $filter['agency_type_id']) . ")";
      } else {
        $sql .= " AND t.agency_type_id = " . $filter['agency_type_id'];
      }
    }
    if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
      $this_month = ($filter['month_id'] - 1);
      $sql .= " AND ((t.year BETWEEN 0 AND $last_year)
      OR (t.year = " . $filter['year_id'] . "
      AND t.month BETWEEN 0 AND $this_month))";
    }
    $sql .= " GROUP BY tc.component_id) exp_upto
      ON comp.component_id = exp_upto.component_id
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        LEFT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'fund_receipt'";
    $sql .= " AND t.status = 1";
    if (!empty($filter['user_id'])) {
      $sql .= " AND t.user_id = " . $filter['user_id'];
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
      //exclude fr from block
      if (empty($filter['block_id'])) {
        //$sql .= " AND t.block_id = 0";
      }
    }
    if (!empty($filter['agency_type_id'])) {
      if (is_array($filter['agency_type_id'])) {
        $sql .= " AND t.agency_type_id IN (" . implode(',', $filter['agency_type_id']) . ")";
      } else {
        $sql .= " AND t.agency_type_id = " . $filter['agency_type_id'];
      }
    }
    if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
      $last_month = ($filter['month_id'] - 1);
      $sql .= " AND ((t.year BETWEEN 0 AND $last_year)
      OR (t.year = " . $filter['year_id'] . "
      AND t.month BETWEEN 0 AND $last_month))";
    }
    $sql .= " GROUP BY tc.component_id) fr_upto
      ON comp.component_id = fr_upto.component_id
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        LEFT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'fund_receipt'";
    $sql .= " AND t.status = 1";
    if (!empty($filter['user_id'])) {
      $sql .= " AND t.user_id = " . $filter['user_id'];
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
      //exclude fr from block
      if (empty($filter['block_id'])) {
        //$sql .= " AND t.block_id = 0";
      }
    }
    if (!empty($filter['agency_type_id'])) {
      if (is_array($filter['agency_type_id'])) {
        $sql .= " AND t.agency_type_id IN (" . implode(',', $filter['agency_type_id']) . ")";
      } else {
        $sql .= " AND t.agency_type_id = " . $filter['agency_type_id'];
      }
    }
    if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
      $last_month = ($filter['month_id'] - 1);
      $sql .= " AND (t.year = " . $filter['year_id'] . "
      AND t.month BETWEEN 0 AND $last_month)";
    }
    $sql .= " GROUP BY tc.component_id) fr_upto_cy
      ON comp.component_id = fr_upto_cy.component_id
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        LEFT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'expense'";
    $sql .= " AND t.status = 1";
    if (!empty($filter['user_id'])) {
      $sql .= " AND t.user_id = " . $filter['user_id'];
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['agency_type_id'])) {
      if (is_array($filter['agency_type_id'])) {
        $sql .= " AND t.agency_type_id IN (" . implode(',', $filter['agency_type_id']) . ")";
      } else {
        $sql .= " AND t.agency_type_id = " . $filter['agency_type_id'];
      }
    }
    if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
      $last_month = ($filter['month_id'] - 1);
      $sql .= " AND (t.year = " . $filter['year_id'] . "
      AND t.month BETWEEN 0 AND $last_month)";
    }
    $sql .= " GROUP BY tc.component_id) exp_upto_cy
      ON comp.component_id = exp_upto_cy.component_id) res ) mpr GROUP BY block_category HAVING block_category is not null";

    return $this->db->query($sql)->getResultArray();
  }

  public function getAbstractMpr($filter = [])
  {
    $last_year = ($filter['year_id'] - 1);

    $sql = "SELECT
    res.scomponent_id,
  res.component_id,
  `number`,
  description,
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
    COALESCE(exp_upto_cy.phy, 0) exp_upto_cy_phy,
    COALESCE(exp_upto_cy.fin, 0) exp_upto_cy_fin,
    COALESCE(fr_upto_cy.phy, 0) fr_upto_cy_phy,
    COALESCE(fr_upto_cy.fin, 0) fr_upto_cy_fin
  FROM (SELECT
      sca.id scomponent_id,
      sc.id component_id,
      sca.number,
      sc.description,
      sca.parent,
      sca.sort_order,
      sc.row_type,
      sc.category,
      agency_type_id
  FROM (SELECT
      c.*,
      sca.fund_agency_id,agency_type_id
    FROM soe_components_agency sca
      LEFT JOIN soe_components c
        ON component_id = c.id
    WHERE 1=1";
    if (!empty($filter['component_agency_type_id'])) {
      $sql .= " AND sca.agency_type_id = " . $filter['component_agency_type_id'];
    } else {
      $sql .= " AND sca.agency_type_id IS NULL ";
    }
    $sql .= " AND sca.fund_agency_id = " . $filter['fund_agency_id'] . ") sc
    LEFT JOIN soe_components_assign sca
      ON sca.component_id = sc.id
      AND sca.fund_agency_id = sc.fund_agency_id GROUP BY component_id,sca.number) comp
    LEFT JOIN user_group ug
      ON comp.agency_type_id = ug.id
    LEFT JOIN (SELECT
  component_id,
  agency_type_id,
  units,
  unit_cost,
  SUM(physical) phy,
  SUM(financial) fin,
  block_category
FROM soe_budgets_plan bp
  LEFT JOIN soe_budgets b
    ON b.budget_plan_id = bp.ID
WHERE fund_agency_id =  " . $filter['fund_agency_id'] . "
AND bp.year =  " . $filter['year_id'];
    if (!empty($filter['block_id'])) {
      $sql .= " AND block_id =  " . $filter['block_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND district_id =  " . $filter['district_id'];
    }
    $sql .= " GROUP BY b.component_id) bud ON bud.component_id=comp.component_id";

    /* ******************transaction starts****************** */
    /* ******************month's expense****************** */
    $sql .= " LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'expense'";
    $sql .= " AND t.status = 1";

    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['month_id'])) {
      $sql .= " AND t.month = " . $filter['month_id'];
    }
    if (!empty($filter['year_id'])) {
      $sql .= " AND t.year = " . $filter['year_id'];
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }
    $sql .= " GROUP BY tc.component_id) expn_mon
      ON comp.component_id = expn_mon.component_id";
    /* ******************month's fundreceipt****************** */
    $sql .= " LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'fund_receipt'";
    $sql .= " AND t.status = 1";
    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    } else if (empty($filter['block_id'])) {
      // exclude block fund receipt when user is not block user.
      $sql .= " AND t.agency_type_id NOT IN (" . implode(',', $filter['block_users']) . ")";
    }

    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
      if (!empty($filter['block_user_id'])) {
        $sql .= " AND t.user_id = " . $filter['block_user_id'];
      }
    }
    if (!empty($filter['month_id'])) {
      $sql .= " AND t.month = " . $filter['month_id'];
    }
    if (!empty($filter['year_id'])) {
      $sql .= " AND t.year = " . $filter['year_id'];
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }
    $sql .= " GROUP BY tc.component_id) fr_mon
      ON comp.component_id = fr_mon.component_id";
    /* ******************expense upto last month****************** */
    $sql .= "
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'expense'";
    $sql .= " AND t.status = 1";
    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
      $this_month = ($filter['month_id'] - 1);
      $sql .= " AND ((t.year BETWEEN 0 AND $last_year)
      OR (t.year = " . $filter['year_id'] . "
      AND t.month BETWEEN 0 AND $this_month))";
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }
    $sql .= " GROUP BY tc.component_id) exp_upto
      ON comp.component_id = exp_upto.component_id";
    /* ******************fundreceipt upto last month****************** */
    $sql .= "
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'fund_receipt'";
    $sql .= " AND t.status = 1";
    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    } else if (empty($filter['block_id'])) {
      // exclude block fund receipt when user is not block user.
      $sql .= " AND t.agency_type_id NOT IN (" . implode(',', $filter['block_users']) . ")";
    }

    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
      if (!empty($filter['block_user_id'])) {
        $sql .= " AND t.user_id = " . $filter['block_user_id'];
      }
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
      $last_month = ($filter['month_id'] - 1);
      $sql .= " AND ((t.year BETWEEN 0 AND $last_year)
      OR (t.year = " . $filter['year_id'] . "
      AND t.month BETWEEN 0 AND $last_month))";
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }
    $sql .= " GROUP BY tc.component_id) fr_upto
      ON comp.component_id = fr_upto.component_id
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'fund_receipt'";
    $sql .= " AND t.status = 1";
    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    } else if (empty($filter['block_id'])) {
      // exclude block fund receipt when user is not block user.
      $sql .= " AND t.agency_type_id NOT IN (" . implode(',', $filter['block_users']) . ")";
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
      if (!empty($filter['block_user_id'])) {
        $sql .= " AND t.user_id = " . $filter['block_user_id'];
      }
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
      $last_month = ($filter['month_id'] - 1);
      $sql .= " AND (t.year = " . $filter['year_id'] . "
      AND t.month BETWEEN 0 AND $last_month)";
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }
    $sql .= " GROUP BY tc.component_id) fr_upto_cy
      ON comp.component_id = fr_upto_cy.component_id
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'expense'";
    $sql .= " AND t.status = 1";
    if (isset($filter['block_user']) && $filter['block_user']) {
      if (!empty($filter['user_id'])) {
        $sql .= " AND t.user_id = " . $filter['user_id'];
      }
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND t.block_id = " . $filter['block_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND t.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['month_id']) && !empty($filter['year_id'])) {
      $last_month = ($filter['month_id'] - 1);
      $sql .= " AND (t.year = " . $filter['year_id'] . "
      AND t.month BETWEEN 0 AND $last_month)";
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND t.fund_agency_id = " . $filter['fund_agency_id'];
    }
    $sql .= " GROUP BY tc.component_id) exp_upto_cy
      ON comp.component_id = exp_upto_cy.component_id) res ORDER BY sort_order";
    //echo $sql;exit;
    return $this->db->query($sql)->getResultArray();
  }

  //dashboard

  public function getClosingBalanceTotal($filter = [])
  {
    $ob = (float)$this->getOpeningBalanceTotal($filter);

    $filter['transaction_type'] = 'fund_receipt';
    $fr = (float)$this->getTransactionTotal($filter);

    $filter['transaction_type'] = 'expense';
    $ex = (float)$this->getTransactionTotal($filter);

    return $ob + $fr - $ex;
  }

  public function getOpeningBalanceTotal($filter = [])
  {

    $filter['transaction_type'] = 'fund_receipt';
    $filter['month_upto'] = ($filter['month'] - 1);
    $filter['month'] = null;

    $fr = $this->getTransactionTotal($filter);

    $filter['transaction_type'] = 'expense';

    $ex = $this->getTransactionTotal($filter);

    return $fr - $ex;
  }

  public function getTransactionTotal($filter = [])
  {
    $sql = "SELECT
  SUM(stc.financial) total
FROM soe_transactions st
  JOIN soe_transaction_components stc
    ON st.id = stc.transaction_id
WHERE st.deleted_at IS NULL
AND stc.deleted_at IS NULL AND st.status = 1";

    $sql .= $this->appendFilter($filter);
    //echo $sql;exit;
    return $this->db->query($sql)->getFirstRow()->total;
  }

  // for district level
  public function getAbstractTotal($filter = [])
  {

    if (!empty($filter['block_id'])) {
      $sql = $this->getBlockAbstractTotal($filter);
    } else {
      $sql = $this->getDistrictAbstractTotal($filter);
    }
    //echo $sql;exit;
    return $this->db->query($sql)->getResult();
  }

  //for district level
  public function getDistrictAbstractTotal($filter)
  {
    $fund_agency_id = isset($filter['fund_agency_id']) ? $filter['fund_agency_id'] : 0;
    $cy = isset($filter['year']) ? $filter['year'] : getCurrentYearId();
    $ly = ($cy - 1);
    $sql = "SELECT
  agency_type_id,
  district_id,
  agency,
  (fr_ly_total - xp_ly_total) ob_total,
  res.fr_total,
  res.xp_total,
  (fr_ly_total - xp_ly_total + fr_total - xp_total) cb_total
FROM (SELECT
    agency.agency_type_id,
    agency.district_id,
    CASE WHEN agency.district_id>0 THEN d.name ELSE ug.name END as agency,
    COALESCE(fr_ly.total, 0) fr_ly_total,
    COALESCE(xp_ly.total, 0) xp_ly_total,
    COALESCE(fr_cy.total, 0) fr_total,
    COALESCE(xp_cy.total, 0) xp_total
  FROM (SELECT
  st.agency_type_id,
  st.district_id,
  st.fund_agency_id
FROM soe_transactions st
WHERE st.fund_agency_id = $fund_agency_id
AND st.agency_type_id NOT IN (5, 6)
GROUP BY st.agency_type_id,
         st.district_id,
         st.fund_agency_id) agency LEFT JOIN soe_districts d ON d.id=agency.district_id
    LEFT JOIN user_group ug ON agency.agency_type_id=ug.id
    LEFT JOIN (SELECT
        st.agency_type_id,
        st.district_id,
        st.fund_agency_id,
        SUM(stc.financial) total
      FROM soe_transactions st
        JOIN soe_transaction_components stc
          ON st.id = stc.transaction_id
      WHERE st.deleted_at IS NULL
      AND stc.deleted_at IS NULL
      AND st.transaction_type = 'fund_receipt'
      AND st.agency_type_id NOT IN (5, 6)
      AND st.status = 1
      AND (st.year BETWEEN 0 AND $ly)
      GROUP BY st.agency_type_id,
               st.district_id,
               st.fund_agency_id) fr_ly
      ON agency.agency_type_id = fr_ly.agency_type_id
      AND agency.district_id = fr_ly.district_id
      AND agency.fund_agency_id = fr_ly.fund_agency_id
    LEFT JOIN (SELECT
        7 agency_type_id,
        st.district_id,
        st.fund_agency_id,
        SUM(stc.financial) total
      FROM soe_transactions st
        JOIN soe_transaction_components stc
          ON st.id = stc.transaction_id
      WHERE st.deleted_at IS NULL
      AND stc.deleted_at IS NULL
      AND st.transaction_type = 'expense'
      AND st.district_id > 0
      AND st.status = 1
      AND (st.year BETWEEN 0 AND $ly)
      GROUP BY st.district_id,
               st.fund_agency_id
      UNION ALL
      SELECT
        st.agency_type_id,
        st.district_id,
        st.fund_agency_id,
        SUM(stc.financial) total
      FROM soe_transactions st
        JOIN soe_transaction_components stc
          ON st.id = stc.transaction_id
      WHERE st.deleted_at IS NULL
      AND stc.deleted_at IS NULL
      AND st.transaction_type = 'expense'
      AND st.district_id = 0
      AND st.fund_agency_id = $fund_agency_id
      AND st.status = 1
      AND (st.year BETWEEN 0 AND $ly)
      GROUP BY st.agency_type_id,
               st.fund_agency_id) xp_ly
      ON agency.district_id = xp_ly.district_id
      AND agency.agency_type_id = xp_ly.agency_type_id
      AND agency.fund_agency_id = xp_ly.fund_agency_id
    LEFT JOIN (SELECT
        st.agency_type_id,
        st.district_id,
        st.fund_agency_id,
        SUM(stc.financial) total
      FROM soe_transactions st
        JOIN soe_transaction_components stc
          ON st.id = stc.transaction_id
      WHERE st.deleted_at IS NULL
      AND stc.deleted_at IS NULL
      AND st.transaction_type = 'fund_receipt'
      AND st.agency_type_id NOT IN (5, 6)
      AND st.status = 1
      AND st.year = $cy
      GROUP BY st.agency_type_id,
               st.district_id,
               st.fund_agency_id) fr_cy
      ON fr_cy.district_id = agency.district_id
      AND fr_cy.agency_type_id = agency.agency_type_id
      AND agency.fund_agency_id = fr_cy.fund_agency_id
    LEFT JOIN (SELECT
        7 agency_type_id,
        st.district_id,
        st.fund_agency_id,
        SUM(stc.financial) total
      FROM soe_transactions st
        JOIN soe_transaction_components stc
          ON st.id = stc.transaction_id
      WHERE st.deleted_at IS NULL
      AND stc.deleted_at IS NULL
      AND st.transaction_type = 'expense'
      AND st.district_id > 0
      AND st.status = 1
      AND st.year = $cy
      GROUP BY st.district_id,
               st.fund_agency_id
      UNION ALL
      SELECT
        st.agency_type_id,
        st.district_id,
        st.fund_agency_id,
        SUM(stc.financial) total
      FROM soe_transactions st
        JOIN soe_transaction_components stc
          ON st.id = stc.transaction_id
      WHERE st.deleted_at IS NULL
      AND stc.deleted_at IS NULL
      AND st.transaction_type = 'expense'
      AND st.district_id = 0
      AND st.status = 1
      AND st.year = $cy
      GROUP BY st.agency_type_id,
               st.fund_agency_id) xp_cy
      ON xp_cy.district_id = agency.district_id
      AND xp_cy.agency_type_id = agency.agency_type_id
      AND agency.fund_agency_id = xp_cy.fund_agency_id
  WHERE agency.fund_agency_id = $fund_agency_id) res WHERE 1=1";
    if (!empty($filter['district_id'])) {
      $sql .= " AND district_id=" . $filter['district_id'];
    }
    //echo $sql;exit;
    return $sql;
  }

  //for block level
  public function getBlockAbstractTotal($filter)
  {
    $fund_agency_id = isset($filter['fund_agency_id']) ? $filter['fund_agency_id'] : 0;
    $cy = isset($filter['year']) ? $filter['year'] : getCurrentYearId();
    $ly = ($cy - 1);
    $sql = "SELECT
  agency_type_id,
  res.block_id,
  agency,
  (fr_ly_total - xp_ly_total) ob_total,
  res.fr_total,
  res.xp_total,
  (fr_ly_total - xp_ly_total + fr_total - xp_total) cb_total
FROM (SELECT
    agency.agency_type_id,
    agency.block_id,
    CASE WHEN agency.block_id > 0 THEN b.name ELSE ug.name END AS agency,
    COALESCE(fr_ly.total, 0) fr_ly_total,
    COALESCE(xp_ly.total, 0) xp_ly_total,
    COALESCE(fr_cy.total, 0) fr_total,
    COALESCE(xp_cy.total, 0) xp_total
  FROM (SELECT
      u.user_group_id agency_type_id,
      u.block_id,
      u.fund_agency_id
    FROM user u
    WHERE u.id=" . $filter['user_id'] . ") agency
    LEFT JOIN soe_blocks b
      ON b.id = agency.block_id
    LEFT JOIN user_group ug
      ON agency.agency_type_id = ug.id
    LEFT JOIN (SELECT
        st.agency_type_id,
        st.block_id,
        st.fund_agency_id,
        SUM(stc.financial) total
      FROM soe_transactions st
        JOIN soe_transaction_components stc
          ON st.id = stc.transaction_id
      WHERE st.deleted_at IS NULL
      AND stc.deleted_at IS NULL
      AND st.transaction_type = 'fund_receipt'
      AND st.user_id = " . $filter['user_id'] . "
      AND st.status = 1
      AND (st.year BETWEEN 0 AND $ly)
      GROUP BY st.block_id) fr_ly
      ON agency.block_id = fr_ly.block_id
    LEFT JOIN (SELECT
        st.block_id,
        SUM(stc.financial) total
      FROM soe_transactions st
        JOIN soe_transaction_components stc
          ON st.id = stc.transaction_id
      WHERE st.deleted_at IS NULL
      AND stc.deleted_at IS NULL
      AND st.transaction_type = 'expense'
      AND st.user_id = " . $filter['user_id'] . "
      AND st.status = 1
      AND (st.year BETWEEN 0 AND $ly)
      GROUP BY st.block_id) xp_ly
      ON agency.block_id = xp_ly.block_id
    LEFT JOIN (SELECT
        st.block_id,
        SUM(stc.financial) total
      FROM soe_transactions st
        JOIN soe_transaction_components stc
          ON st.id = stc.transaction_id
      WHERE st.deleted_at IS NULL
      AND stc.deleted_at IS NULL
      AND st.transaction_type = 'fund_receipt'
      AND st.user_id = " . $filter['user_id'] . "
      AND st.status = 1
      AND st.year = $cy
      GROUP BY st.block_id) fr_cy
      ON fr_cy.block_id = agency.block_id
    LEFT JOIN (SELECT
        st.block_id,
        SUM(stc.financial) total
      FROM soe_transactions st
        JOIN soe_transaction_components stc
          ON st.id = stc.transaction_id
      WHERE st.deleted_at IS NULL
      AND stc.deleted_at IS NULL
      AND st.transaction_type = 'expense'
      AND st.user_id = " . $filter['user_id'] . "
      AND st.status = 1
      AND st.year = $cy
      GROUP BY st.block_id) xp_cy
      ON xp_cy.block_id = agency.block_id) res";

    return $sql;
  }

  protected function appendFilter($filter)
  {
    $sql = "";
    if (!empty($filter['user_id'])) {
      $sql .= " AND st.user_id = " . $filter['user_id'];
    }
    if (!empty($filter['transaction_type'])) {
      $sql .= " AND st.transaction_type = '" . $filter['transaction_type'] . "'";
    }
    if (!empty($filter['block_id'])) {
      if (is_array($filter['block_id'])) {
        $sql .= " AND st.block_id IN (" . implode(',', $filter['block_id']) . ")";
      } else {
        $sql .= " AND st.block_id = " . $filter['block_id'];
      }
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND st.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['year'])) {
      $sql .= " AND st.year = " . $filter['year'];
    }
    if (!empty($filter['year_upto'])) {
      $sql .= " AND st.year BETWEEN 0 AND " . $filter['year_upto'];
    }
    if (!empty($filter['month'])) {
      $sql .= " AND st.month = " . $filter['month'];
    }
    if (!empty($filter['month_upto'])) {
      $sql .= " AND ((st.year BETWEEN 0 AND " . ($filter['year'] - 1) . ')';
      $sql .= " OR (st.year = " . $filter['year'] . " AND st.month BETWEEN 0 AND " . $filter['month_upto'] . '))';
    }
    if (!empty($filter['agency_type_id'])) {
      if (is_array($filter['agency_type_id'])) {
        $sql .= " AND st.agency_type_id IN (" . implode(',', $filter['agency_type_id']) . ")";
      } else {
        $sql .= " AND st.agency_type_id = " . $filter['agency_type_id'];
      }
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND st.fund_agency_id = " . $filter['fund_agency_id'];
    }

    return $sql;
  }

  public function getInterestReport($filter = [])
  {

    $year = getCurrentYearId();
    $last_year = $year - 1;
    if (!empty($filter['year_id'])) {
      $year = $filter['year_id'];
      $last_year = ($filter['year_id'] - 1);
    }
    $month = getMonthIdByMonth(date('m'));
    $last_month = $month - 1;
    if (!empty($filter['month_id'])) {
      $month = $filter['month_id'];
      $last_month = ($filter['month_id'] - 1);
    }

    $sql = "WITH bank_int
AS
(SELECT
      usr.firstname,
      res.agency_type_id,
      res.district_id,
      sd.name district,
      res.block_id,
      bl.name block,
      res.fund_agency_id,
      res.year,
      res.month,
      res.total_interest
    FROM (SELECT
        u.firstname,
        u.user_group_id,
        u.district_id,
        u.block_id,
        u.fund_agency_id
      FROM user u
      WHERE u.user_group_id IN (" . implode(',', (array)$filter['agency_type_id']) . ")) usr
      LEFT JOIN (SELECT
          smt.agency_type_id,
          smt.district_id,
          smt.block_id,
          smt.fund_agency_id,
          smt.year,
          smt.month,
          smta.amount total_interest
        FROM soe_misc_transactions smt
          LEFT JOIN soe_misc_txn_amt smta
            ON smt.id = smta.txn_id
        WHERE smt.deleted_at IS NULL
        AND smta.deleted_at IS NULL
        AND smt.status = 1
        AND smta.head_id IN (SELECT
            id
          FROM soe_misc_txn_heads smth
          WHERE smth.name LIKE '%bank interest%')) res
        ON res.agency_type_id = usr.user_group_id
        AND res.district_id = usr.district_id
        AND res.block_id = usr.block_id
        AND res.fund_agency_id = usr.fund_agency_id
      LEFT JOIN soe_blocks bl
        ON bl.id = res.block_id
      LEFT JOIN soe_districts sd
        ON res.district_id = sd.id
    WHERE usr.district_id = " . $filter['district_id'] . "
    AND usr.fund_agency_id = " . $filter['fund_agency_id'] . "),
refund
AS
(SELECT
      usr.firstname,
      res.agency_type_id,
      res.district_id,
      res.block_id,
      res.fund_agency_id,
      res.year,
      res.month,
      res.total_refund
    FROM (SELECT
        u.firstname,
        u.user_group_id,
        u.district_id,
        u.block_id,
        u.fund_agency_id
      FROM user u
      WHERE u.user_group_id IN (" . implode(',', (array)$filter['agency_type_id']) . ")) usr
      LEFT JOIN (SELECT
          smt.agency_type_id,
          smt.district_id,
          smt.block_id,
          smt.fund_agency_id,
          smt.year,
          smt.month,
          smta.amount total_refund
        FROM soe_misc_transactions smt
          LEFT JOIN soe_misc_txn_amt smta
            ON smt.id = smta.txn_id
        WHERE smt.deleted_at IS NULL
        AND smta.deleted_at IS NULL
        AND smt.status = 1
        AND smta.head_id IN (SELECT
            id
          FROM soe_misc_txn_heads smth
          WHERE smth.name LIKE '%Interest Refund%')) res
        ON res.agency_type_id = usr.user_group_id
        AND res.district_id = usr.district_id
        AND res.block_id = usr.block_id
        AND res.fund_agency_id = usr.fund_agency_id
    WHERE usr.district_id = " . $filter['district_id'] . "
    AND usr.fund_agency_id = " . $filter['fund_agency_id'] . ")";

    /* -- interest upto ------------------------ */
    $sql .= " SELECT
  int_upto.block,
  int_upto.firstname,
  COALESCE(int_upto.tot_int, 0) tot_int_upto,
  COALESCE(int_mon.tot_int, 0) tot_int_mon,
  COALESCE(ref_upto.tot_ref, 0) tot_ref_upto,
  COALESCE(ref_mon.tot_ref, 0) tot_ref_mon
FROM (SELECT
    CASE WHEN bi.block_id = 0 THEN CONCAT('ATMA ', bi.district) ELSE bi.block END AS block,
    bi.firstname,
    bi.block_id,
    bi.district_id,
    bi.agency_type_id,
    bi.fund_agency_id,
    SUM(bi.total_interest) tot_int
  FROM bank_int bi
  WHERE ((bi.year BETWEEN 0 AND $last_year)
  OR (bi.year = $year
  AND bi.month BETWEEN 0 AND $last_month))
  GROUP BY bi.block_id) int_upto";
    /* -- interest month ----------------------  */
    $sql .= " LEFT JOIN (SELECT
    CASE WHEN bi.block_id = 0 THEN CONCAT('ATMA ', bi.district) ELSE bi.block END AS block,
    bi.firstname,
    bi.block_id,
    bi.district_id,
    bi.agency_type_id,
    bi.fund_agency_id,
    SUM(bi.total_interest) tot_int
  FROM bank_int bi
  WHERE bi.year = $year
  AND bi.month = $month
  GROUP BY bi.block_id) int_mon
    ON int_mon.block_id = int_upto.block_id
    AND int_upto.district_id = int_mon.district_id
    AND int_upto.agency_type_id = int_mon.agency_type_id
    AND int_upto.fund_agency_id = int_mon.fund_agency_id";
    /* -- refund upto ----------------- */
    $sql .= " LEFT JOIN (SELECT
      ref.block_id,
      ref.district_id,
      ref.agency_type_id,
      ref.fund_agency_id,
      SUM(ref.total_refund) tot_ref
    FROM refund ref
    WHERE ((ref.year BETWEEN 0 AND $last_year)
  OR (ref.year = $year
  AND ref.month BETWEEN 0 AND $last_month))
    GROUP BY ref.block_id) ref_upto
    ON ref_upto.block_id = int_upto.block_id
    AND int_upto.district_id = ref_upto.district_id
    AND int_upto.agency_type_id = ref_upto.agency_type_id
    AND int_upto.fund_agency_id = ref_upto.fund_agency_id
  LEFT JOIN (SELECT
      ref.block_id,
      ref.district_id,
      ref.agency_type_id,
      ref.fund_agency_id,
      SUM(ref.total_refund) tot_ref
    FROM refund ref
    WHERE ref.year = $year AND ref.month = $month
    GROUP BY ref.block_id) ref_mon
    ON ref_mon.block_id = int_upto.block_id
    AND int_upto.district_id = ref_mon.district_id
    AND int_upto.agency_type_id = ref_mon.agency_type_id
    AND int_upto.fund_agency_id = ref_mon.fund_agency_id";

    return $this->db->query($sql)->getResult();
  }

  public function getInterestReport_bk($filter = [])
  {

    $bi_head_id = 0;
    $ref_head_id = 0;
    if (!empty($filter['agency_type_id'])) {
      if ($filter['agency_type_id'] == 7) {
        $bi_head_id = 6;
        $ref_head_id = 8;
      } else {
        $bi_head_id = 2;
        $ref_head_id = 4;
      }
    }
    $year = getCurrentYearId();
    $last_year = $year - 1;
    if (!empty($filter['year_id'])) {
      $year = $filter['year_id'];
      $last_year = ($filter['year_id'] - 1);
    }
    $month = getMonthIdByMonth(date('m'));
    $last_month = $month - 1;
    if (!empty($filter['month_id'])) {
      $month = $filter['month_id'];
      $last_month = ($filter['month_id'] - 1);
    }

    $sql = "SELECT
  user.user_id,
  user.block_id,
  user.user_group_id,
  user.block,
  COALESCE(int_upto.total_interest, 0) int_upto,
  COALESCE(int_mon.total_interest, 0) int_mon,
  COALESCE(int_ref.total_refund, 0) int_ref_block
FROM (SELECT
    u.id user_id,
    u.block_id,
    u.user_group_id,
    COALESCE(sb.name, 'ATMA') block
  FROM user u
    LEFT JOIN soe_blocks sb
      ON u.block_id = sb.id
  WHERE 1=1";
    if (!empty($filter['district_id'])) {
      $sql .= " AND u.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['agency_type_id'])) {
      if (is_array($filter['agency_type_id'])) {
        $sql .= " AND u.user_group_id IN (" . implode(',', $filter['agency_type_id']) . ')';
      } else {
        $sql .= " AND u.user_group_id = " . $filter['agency_type_id'];
      }
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND u.fund_agency_id = " . $filter['fund_agency_id'];
    }
    $sql .= " GROUP BY u.block_id) user
  LEFT JOIN (SELECT
      smt.user_id,
      smt.block_id,
      SUM(smta.amount) total_interest
    FROM soe_misc_transactions smt
      LEFT JOIN soe_misc_txn_amt smta
        ON smt.id = smta.txn_id
    WHERE smt.deleted_at IS NULL
    AND smta.deleted_at IS NULL
    AND smt.status = 1";
    if (!empty($filter['agency_type_id'])) {
      if (is_array($filter['agency_type_id'])) {
        $sql .= " AND smt.agency_type_id IN (" . implode(',', $filter['agency_type_id']) . ')';
      } else {
        $sql .= " AND smt.agency_type_id = " . $filter['agency_type_id'];
      }
    }
    $sql .= " AND ((smt.year BETWEEN 0 AND $last_year)
    OR (smt.year = $year
    AND smt.month BETWEEN 0 AND $last_month))
    AND smta.head_id = $bi_head_id
    GROUP BY smt.block_id) int_upto
    ON int_upto.block_id = user.block_id
  LEFT JOIN (SELECT
      smt.user_id,
      smt.block_id,
      SUM(smta.amount) total_interest
    FROM soe_misc_transactions smt
      LEFT JOIN soe_misc_txn_amt smta
        ON smt.id = smta.txn_id
    WHERE smt.deleted_at IS NULL
    AND smta.deleted_at IS NULL
    AND smt.status = 1";
    if (!empty($filter['agency_type_id'])) {
      if (is_array($filter['agency_type_id'])) {
        $sql .= " AND smt.agency_type_id IN (" . implode(',', $filter['agency_type_id']) . ')';
      } else {
        $sql .= " AND smt.agency_type_id = " . $filter['agency_type_id'];
      }
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND smt.district_id = " . $filter['district_id'];
    }
    $sql .= " AND (smt.year = $year
    AND smt.month = $month)
    AND smta.head_id = $bi_head_id
    GROUP BY smt.block_id) int_mon
    ON int_mon.block_id = user.block_id
  LEFT JOIN (SELECT
      smt.user_id,
      smt.block_id,
      SUM(smta.amount) total_refund
    FROM soe_misc_transactions smt
      LEFT JOIN soe_misc_txn_amt smta
        ON smt.id = smta.txn_id
    WHERE smt.deleted_at IS NULL
    AND smta.deleted_at IS NULL
    AND smt.status = 1";
    if (!empty($filter['agency_type_id'])) {
      if (is_array($filter['agency_type_id'])) {
        $sql .= " AND smt.agency_type_id IN (" . implode(',', $filter['agency_type_id']) . ')';
      } else {
        $sql .= " AND smt.agency_type_id = " . $filter['agency_type_id'];
      }
    }

    if (!empty($filter['district_id'])) {
      $sql .= " AND smt.district_id = " . $filter['district_id'];
    }
    $sql .= " AND ((smt.year BETWEEN 0 AND $last_year)
    OR (smt.year = $year
    AND smt.month BETWEEN 0 AND $month))
    AND smta.head_id = $ref_head_id
    GROUP BY smt.block_id) int_ref
    ON int_ref.block_id = user.block_id
  ORDER BY user_group_id, block";
    //echo $sql;exit;
    return $this->db->query($sql)->getResultArray();
  }

  public function getUploadStatus($filter = [])
  {
    $sql = "WITH block_agency
      AS
      (SELECT
            sb.id block_id,
            agency.id agency_type_id
          FROM soe_blocks sb
            CROSS JOIN (SELECT
                *
              FROM user_group
              WHERE id IN (5, 6)) agency)
      
      SELECT
        *
      FROM (SELECT
          bl.district_id,
          bl.district,
          bl.block_id,
          bl.block,
          fch.frc_status,
          och.orc_status,
          fr.fr_status,
          ex.ex_status,
          `or`.or_status,
          cb.cb_status,
          mis.mis_status
        FROM (SELECT
            bl.district_id,
            bl.id block_id,
            bl.name block,
            dist.name district
          FROM (SELECT
              *
            FROM soe_blocks sb
            WHERE 1 = 1";
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND sb.fund_agency_id = '" . $filter['fund_agency_id'] . "'";
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND sb.district_id = '" . $filter['district_id'] . "'";
    }
    $sql .= ") bl
            LEFT JOIN soe_districts dist
              ON bl.district_id = dist.id) bl
          LEFT JOIN (SELECT
              sfrc.status AS frc_status,
              sfrc.block_id
            FROM soe_fund_receipt_check sfrc
            WHERE sfrc.month = " . $filter['month'] . "
            AND sfrc.year = " . $filter['year'] . "
            AND sfrc.check_type = 'fr'
            AND sfrc.agency_type_id = 5) fch
            ON bl.block_id = fch.block_id
          LEFT JOIN (SELECT
              sfrc.status AS orc_status,
              sfrc.block_id
            FROM soe_fund_receipt_check sfrc
            WHERE sfrc.month = " . $filter['month'] . "
            AND sfrc.year = " . $filter['year'] . "
            AND sfrc.check_type = 'or'
            AND sfrc.agency_type_id = 5) och
            ON bl.block_id = och.block_id
          LEFT JOIN (SELECT
              CASE WHEN COUNT(*) = COUNT(st.status) THEN CASE WHEN SUM(st.status) = 3 THEN 0 ELSE MIN(COALESCE(st.status, 1)) END ELSE NULL END AS fr_status,
              ba.block_id
            FROM block_agency ba
              LEFT JOIN (SELECT
                  status,
                  block_id,
                  agency_type_id
                FROM soe_transactions st
                WHERE deleted_at IS NULL
                AND st.transaction_type = 'fund_receipt'
                AND st.month = " . $filter['month'] . "
                AND st.year = " . $filter['year'] . "
                AND st.agency_type_id IN (5, 6)) st
                ON ba.block_id = st.block_id
                AND ba.agency_type_id = st.agency_type_id
            GROUP BY ba.block_id) fr
            ON bl.block_id = fr.block_id
          LEFT JOIN (SELECT
              CASE WHEN COUNT(*) = COUNT(st.status) THEN CASE WHEN SUM(st.status) = 3 THEN 0 ELSE MIN(COALESCE(st.status, 1)) END ELSE NULL END AS ex_status,
              ba.block_id
            FROM block_agency ba
              LEFT JOIN (SELECT
                  status,
                  block_id,
                  agency_type_id
                FROM soe_transactions st
                WHERE st.deleted_at IS NULL
                AND st.transaction_type = 'expense'
                AND st.month = " . $filter['month'] . "
                AND st.year = " . $filter['year'] . "
                AND st.agency_type_id IN (5, 6)) st
                ON ba.block_id = st.block_id
                AND ba.agency_type_id = st.agency_type_id
            GROUP BY ba.block_id) ex
            ON bl.block_id = ex.block_id
          LEFT JOIN (SELECT
              CASE WHEN COUNT(*) = COUNT(smt.status) THEN CASE WHEN SUM(smt.status) = 3 THEN 0 ELSE MIN(COALESCE(smt.status, 1)) END ELSE NULL END AS or_status,
              ba.block_id
            FROM block_agency ba
              LEFT JOIN (SELECT
                  smt.status,
                  smt.block_id,
                  smt.agency_type_id
                FROM soe_misc_transactions smt
                WHERE smt.deleted_at IS NULL
                AND smt.month = " . $filter['month'] . "
                AND smt.year = " . $filter['year'] . "
                AND smt.agency_type_id IN (5, 6)) smt
                ON ba.block_id = smt.block_id
                AND ba.agency_type_id = smt.agency_type_id
            GROUP BY ba.block_id) `or`
            ON bl.block_id = `or`.block_id
          LEFT JOIN (SELECT
              CASE WHEN COUNT(*) = COUNT(scb.status) THEN CASE WHEN SUM(scb.status) = 3 THEN 0 ELSE MIN(COALESCE(scb.status, 1)) END ELSE NULL END AS cb_status,
              ba.block_id
            FROM block_agency ba
              LEFT JOIN (SELECT
                  scb.status,
                  scb.block_id,
                  scb.agency_type_id
                FROM soe_closing_balances scb
                WHERE scb.deleted_at IS NULL
                AND scb.month = " . $filter['month'] . "
                AND scb.year = " . $filter['year'] . "
                AND scb.agency_type_id IN (5, 6)) scb
                ON ba.block_id = scb.block_id
                AND ba.agency_type_id = scb.agency_type_id
            GROUP BY ba.block_id) cb
            ON bl.block_id = cb.block_id
          LEFT JOIN (SELECT
              ms.status mis_status,
              ba.block_id
            FROM block_agency ba
              LEFT JOIN (SELECT
                  ms.status,
                  ms.block_id,
                  ms.agency_type_id
                FROM mis_submissions ms
                WHERE ms.deleted_at IS NULL
                AND ms.month = " . $filter['month'] . "
                AND ms.year = " . $filter['year'] . "
                AND ms.agency_type_id = 5 ) ms
                ON ba.block_id = ms.block_id
                AND ba.agency_type_id = ms.agency_type_id
            GROUP BY ba.block_id) mis
            ON bl.block_id = mis.block_id) bl_sts
            UNION ALL
            (SELECT
                dist.district_id,
                dist.district,
                dist.district_id block_id,
                
                CASE WHEN  dist.fund_agency_id = 1 THEN  CONCAT('ATMA ', dist.district) ELSE CONCAT('ATMA DMF ', dist.district) END block,
                dist_frc.frc_status,
                dist_orc.orc_status,
            
                fr.fr_status,
                ex.ex_status,
                `or`.or_status,
                cb.cb_status,
                mis.mis_status
              FROM (SELECT
                  sd.id district_id,
                  sd.name district,
                  u.fund_agency_id
                FROM soe_districts sd LEFT JOIN user u ON sd.id=u.district_id
                WHERE block_id=0 AND u.user_group_id=7";
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND u.fund_agency_id = '" . $filter['fund_agency_id'] . "'";
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND sd.id = '" . $filter['district_id'] . "'";
    }
    $sql .= " ) dist 
                LEFT JOIN (SELECT
                    sfrc.status AS frc_status,
                    sfrc.district_id
                  FROM soe_fund_receipt_check sfrc
                  WHERE sfrc.month = " . $filter['month'] . "
                  AND sfrc.year = " . $filter['year'] . "
                  AND sfrc.block_id = 0
                  AND sfrc.check_type = 'fr'
                  AND sfrc.agency_type_id = 7) dist_frc
                  ON dist.district_id = dist_frc.district_id
            
            
                LEFT JOIN (SELECT
                    sfrc.status AS orc_status,
                    sfrc.district_id
                  FROM soe_fund_receipt_check sfrc
                  WHERE sfrc.month = " . $filter['month'] . "
                  AND sfrc.year = " . $filter['year'] . "
                  AND sfrc.block_id = 0
                  AND sfrc.check_type = 'or'
                  AND sfrc.agency_type_id = 7) dist_orc
                  ON dist.district_id = dist_orc.district_id
            
                LEFT JOIN (SELECT
                    CASE WHEN COUNT(*) = COUNT(st.status) THEN CASE WHEN SUM(st.status) = 3 THEN 0 ELSE MIN(COALESCE(st.status, 1)) END ELSE NULL END AS fr_status,
                    st.district_id
                  FROM soe_transactions st
                  WHERE st.deleted_at IS NULL
                  AND st.transaction_type = 'fund_receipt'
                  AND st.month = " . $filter['month'] . "
                  AND st.year = " . $filter['year'] . "
                  AND st.block_id = 0
                  AND st.agency_type_id = 7
                  GROUP BY st.district_id) fr
                  ON dist.district_id = fr.district_id
            
                LEFT JOIN (SELECT
            
                    CASE WHEN COUNT(*) = COUNT(st.status) THEN CASE WHEN SUM(st.status) = 3 THEN 0 ELSE MIN(COALESCE(st.status, 1)) END ELSE NULL END AS ex_status,
                    st.district_id
                  FROM soe_transactions st
                  WHERE st.deleted_at IS NULL
                  AND st.transaction_type = 'expense'
                  AND st.month = " . $filter['month'] . "
                  AND st.year = " . $filter['year'] . "
                  AND st.block_id = 0
                  AND st.agency_type_id = 7
                  GROUP BY st.district_id) ex
                  ON dist.district_id = ex.district_id
            
                LEFT JOIN (SELECT
            
                    CASE WHEN COUNT(*) = COUNT(smt.status) THEN CASE WHEN SUM(smt.status) = 3 THEN 0 ELSE MIN(COALESCE(smt.status, 1)) END ELSE NULL END AS or_status,
                    smt.district_id
                  FROM soe_misc_transactions smt
                  WHERE smt.deleted_at IS NULL
                  AND smt.month = " . $filter['month'] . "
                  AND smt.year = " . $filter['year'] . "
                  AND smt.block_id = 0
                  AND smt.agency_type_id = 7
                  GROUP BY smt.district_id) `or`
                  ON dist.district_id = `or`.district_id
            
                LEFT JOIN (SELECT
            
                    CASE WHEN COUNT(*) = COUNT(scb.status) THEN CASE WHEN SUM(scb.status) = 3 THEN 0 ELSE MIN(COALESCE(scb.status, 1)) END ELSE NULL END AS cb_status,
                    scb.district_id
                  FROM soe_closing_balances scb
                  WHERE scb.deleted_at IS NULL
                  AND scb.month = " . $filter['month'] . "
                  AND scb.year = " . $filter['year'] . "
                  AND scb.block_id = 0
                  AND scb.agency_type_id = 7
                  GROUP BY scb.district_id) cb
                  ON dist.district_id = cb.district_id
            
                LEFT JOIN (SELECT
            
                    CASE WHEN COUNT(*) = COUNT(ms.status) THEN CASE WHEN SUM(ms.status) = 3 THEN 0 ELSE MIN(COALESCE(ms.status, 1)) END ELSE NULL END AS mis_status,
                    ms.district_id
                  FROM mis_submissions ms
                  WHERE ms.deleted_at IS NULL
                  AND ms.month = " . $filter['month'] . "
                  AND ms.year = " . $filter['year'] . "
                  AND ms.block_id = 0
                  AND ms.agency_type_id = 7
                  GROUP BY ms.district_id) mis
                  ON dist.district_id = mis.district_id)
            ORDER BY district ASC";

    return $this->db->query($sql)->getResult();
  }

  public function getUploadStatus_old($filter = [])
  {

    $sql = "SELECT * FROM 
(SELECT
  dist.id district_id,
  dist.name district,
  bl.id block_id,
  bl.name block,
  fch.status fch_status,
  fr.status fr_status,
  ex.status ex_status,
  `or`.status or_status,
  cb.status cb_status,
  mis.status mis_status
FROM (SELECT
    *
  FROM soe_blocks sb WHERE 1=1";
    if (!empty($filter['district_id'])) {
      $sql .= " AND sb.district_id = '" . $filter['district_id'] . "'";
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND sb.fund_agency_id = '" . $filter['fund_agency_id'] . "'";
    }
    $sql .= ") bl LEFT JOIN soe_districts dist ON bl.district_id=dist.id
  LEFT JOIN (SELECT
      *
    FROM soe_fund_receipt_check sfrc
    WHERE sfrc.month = " . $filter['month'] . "
    AND sfrc.year = " . $filter['year'] . ") fch
    ON bl.id = fch.block_id
  LEFT JOIN (SELECT
      *
    FROM soe_transactions st
    WHERE st.deleted_at IS NULL
    AND st.transaction_type = 'fund_receipt'
    AND st.month = " . $filter['month'] . "
    AND st.year = " . $filter['year'] . " AND st.user_id IN 
    (SELECT id FROM `user` u WHERE u.block_id=st.block_id AND u.user_group_id=5)) fr
    ON bl.id = fr.block_id
  LEFT JOIN (SELECT
      *
    FROM soe_transactions st
    WHERE st.deleted_at IS NULL
    AND st.transaction_type = 'expense'
    AND st.month = " . $filter['month'] . "
    AND st.year = " . $filter['year'] . " AND st.user_id IN 
    (SELECT id FROM `user` u WHERE u.block_id=st.block_id AND u.user_group_id=5)) ex
    ON bl.id = ex.block_id
  LEFT JOIN (SELECT
      *
    FROM soe_misc_transactions smt
    WHERE smt.deleted_at IS NULL
    AND smt.month = " . $filter['month'] . "
    AND smt.year = " . $filter['year'] . " AND smt.user_id IN 
    (SELECT id FROM `user` u WHERE u.block_id=smt.block_id AND u.user_group_id=5)) `or`
    ON bl.id = `or`.block_id
  LEFT JOIN (SELECT
      *
    FROM soe_closing_balances scb
    WHERE scb.deleted_at IS NULL
    AND scb.month = " . $filter['month'] . "
    AND scb.year = " . $filter['year'] . " AND scb.user_id IN 
    (SELECT id FROM `user` u WHERE u.block_id=scb.block_id AND u.user_group_id=5)) cb
    ON bl.id = cb.block_id
  LEFT JOIN (SELECT
      *
    FROM mis_submissions ms
    WHERE ms.deleted_at IS NULL
    AND ms.month = " . $filter['month'] . "
    AND ms.year = " . $filter['year'] . "
    AND ms.user_id IN (SELECT
        id
      FROM `user` u
      WHERE u.block_id = ms.block_id
      AND u.user_group_id = 5)) mis
    ON bl.id = mis.block_id GROUP BY block_id) bl_sts
    
    UNION ALL 

(SELECT
  dist.district_id,dist.district,
  dist.district_id block_id,
  CONCAT('ATMA ',dist.district) block,
  dist_frc.status fch_status,
  fr.status fr_status,
  ex.status ex_status,
  `or`.status or_status,
  cb.status cb_status,
  mis.status mis_status
FROM (SELECT
    sd.id district_id,
    sd.name district,
      u.id user_id,u.fund_agency_id
  FROM soe_districts sd LEFT JOIN user u ON sd.id=u.district_id
  WHERE 1=1 AND block_id=0 AND u.user_group_id=7";
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND fund_agency_id = '" . $filter['fund_agency_id'] . "'";
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND sd.id = '" . $filter['district_id'] . "'";
    }
    $sql .= " ) dist
  LEFT JOIN (SELECT
      *
    FROM soe_fund_receipt_check sfrc
    WHERE sfrc.month = " . $filter['month'] . "
    AND sfrc.year = " . $filter['year'] . "
    AND sfrc.block_id = 0) dist_frc
    ON dist.district_id = dist_frc.district_id AND dist_frc.fund_agency_id=dist.fund_agency_id
  LEFT JOIN (SELECT
      *
    FROM soe_transactions st
    WHERE st.deleted_at IS NULL
    AND st.transaction_type = 'fund_receipt'
    AND st.month = " . $filter['month'] . "
    AND st.year = " . $filter['year'] . "
    AND st.block_id = 0
    AND st.user_id IN (SELECT
        id
      FROM `user` u
      WHERE u.district_id = st.district_id
      AND u.user_group_id = 7)) fr
    ON dist.district_id = fr.district_id AND fr.fund_agency_id=dist.fund_agency_id
  LEFT JOIN (SELECT
      *
    FROM soe_transactions st
    WHERE st.deleted_at IS NULL
    AND st.transaction_type = 'expense'
    AND st.month = " . $filter['month'] . "
    AND st.year = " . $filter['year'] . "
    AND st.block_id = 0
    AND st.user_id IN (SELECT
        id
      FROM `user` u
      WHERE u.district_id = st.district_id
      AND u.user_group_id = 7)) ex
    ON dist.district_id = ex.district_id AND ex.fund_agency_id=dist.fund_agency_id
  LEFT JOIN (SELECT
      *
    FROM soe_misc_transactions smt
    WHERE smt.deleted_at IS NULL
    AND smt.month = " . $filter['month'] . "
    AND smt.year = " . $filter['year'] . "
    AND smt.block_id = 0
    AND smt.user_id IN (SELECT
        id
      FROM `user` u
      WHERE u.district_id = smt.district_id
      AND u.user_group_id = 7)) `or`
    ON dist.district_id = `or`.district_id AND `or`.fund_agency_id=dist.fund_agency_id
  LEFT JOIN (SELECT
      *
    FROM soe_closing_balances scb
    WHERE scb.deleted_at IS NULL
    AND scb.month = " . $filter['month'] . "
    AND scb.year = " . $filter['year'] . "
    AND scb.block_id = 0
    AND scb.user_id IN (SELECT
        id
      FROM `user` u
      WHERE u.district_id = scb.district_id
      AND u.user_group_id IN (7))) cb
    ON dist.district_id = cb.district_id AND cb.fund_agency_id=dist.fund_agency_id
  LEFT JOIN (SELECT
      *
    FROM mis_submissions ms
    WHERE ms.deleted_at IS NULL
    AND ms.month = " . $filter['month'] . "
    AND ms.year = " . $filter['year'] . "
    AND ms.block_id = 0
    AND ms.user_id IN (SELECT
        id
      FROM `user` u
      WHERE u.district_id = ms.district_id
      AND u.user_group_id = 7)) mis
    ON dist.district_id = mis.district_id AND mis.fund_agency_id=dist.fund_agency_id) ORDER BY district,block";
    //echo $sql;exit;
    return $this->db->query($sql)->getResult();
  }

  public function getMisStatus($filter)
  {
    $sql = "SELECT
        *
        FROM mis_submissions ms
        WHERE ms.deleted_at IS NULL
        AND ms.month = " . $filter['month'] . "
        AND ms.year = " . $filter['year'];
    if (!empty($filter['district_id'])) {
      $sql .= " AND ms.district_id = '" . $filter['district_id'] . "'";
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND ms.block_id = '" . $filter['block_id'] . "'";
    }
    return $this->db->query($sql)->getRow();
  }

  public function getFRStatus($filter)
  {
  }

  public function getTransactionAbstractDistrict($filter = [])
  {
    $sql = "SELECT
  sd.district_id,
  sd.district,
  fr.total fr_total,
  xp.total ex_total
FROM (SELECT
    sd.id district_id,
    sd.name district,
    u.fund_agency_id
  FROM soe_districts sd
    LEFT JOIN user u
      ON u.district_id = sd.id
      AND u.user_group_id = 7) sd
  LEFT JOIN (SELECT
      district_id,
      transaction_type,
      xp.fund_agency_id,
      year,
      SUM(total) total
    FROM vw_district_abstract_txn xp
    WHERE xp.transaction_type = 'fund_receipt'
    AND xp.block_id = 0
    GROUP BY district_id,
             xp.fund_agency_id,xp.year) fr
    ON fr.district_id = sd.district_id
    AND sd.fund_agency_id = fr.fund_agency_id
  LEFT JOIN (SELECT
      district_id,
      transaction_type,
      xp.fund_agency_id,
      year,
      SUM(total) total
    FROM vw_district_abstract_txn xp
    WHERE xp.transaction_type = 'expense'
    GROUP BY district_id,
             xp.fund_agency_id,xp.year) xp
    ON fr.district_id = xp.district_id AND  fr.year = xp.year
    AND sd.fund_agency_id = xp.fund_agency_id
WHERE sd.fund_agency_id = 1 AND fr.year = " . $filter['year'];

    return $this->db->query($sql)->getResult();
  }

  public function getTransactionAbstractAgency($filter = [])
  {
    if ($filter['transaction_type'] == 'expense') {
      $sql = "SELECT * FROM vw_txn_agency_type_exp";
      return $this->db->query($sql)->getResult();
    } else if ($filter['transaction_type'] == 'fund_receipt') {
      $sql = "SELECT * FROM vw_txn_agency_type_fr";
      return $this->db->query($sql)->getResult();
    }
  }

  public function getTransactionAbstractBlock($filter = [])
  {
    $sql = "SELECT * FROM vw_district_abstract_txn WHERE 
district_id=" . (int)$filter['district_id']
      . " AND (fund_agency_id=" . (int)$filter['fund_agency_id'] . ")"
      . " AND transaction_type='" . $filter['transaction_type'] . "'";
    return $this->db->query($sql)->getResult();
  }

  public function getPendingStatuses($filter = [])
  {
    $sql = "SELECT * FROM vw_pending_statuses st WHERE 1=1";
    if (!empty($filter['year_id'])) {
      $sql .= " AND st.year_id = " . $filter['year_id'];
    }
    if (!empty($filter['month_id'])) {
      $sql .= " AND st.month_id = " . $filter['month_id'];
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND st.district_id = " . $filter['district_id'];
    }
    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND st.fund_agency_id = " . $filter['fund_agency_id'];
    }

    $sql .= " ORDER BY transaction_id";
    // echo $sql;exit;
    return $this->db->query($sql)->getResultArray();
  }

  public function getPendingClosingBalance($filter = [])
  {
    $sql = "SELECT
  res.block_id,
  res.district_id,
  sd.name district,
  res.block,
  res.agency,
  res.phase,
  res.total,
  res.agency_type_id,
  res.fund_agency_id
FROM (SELECT
    bl.block_id,
    bl.district_id,
    bl.block,
    bl.agency,
    bl.phase,
    COALESCE(bl_txn.total, 0) total,
    bl_txn.agency_type_id,bl.fund_agency_id
  FROM (SELECT
  sb.id block_id,
  sb.name block,
  CONCAT(agtp.name,' ',sb.name) agency,
  sb.district_id,
  sb.fund_agency_id,
  sb.phase,
  agtp.id agency_type_id
FROM soe_blocks sb
  CROSS JOIN (SELECT
      *
    FROM user_group ug
    WHERE ug.id IN (5, 6)) agtp) bl
    LEFT JOIN (SELECT
        COUNT(id) total,
        st.block_id,
        st.district_id,
        st.agency_type_id
      FROM soe_closing_balances st
      WHERE st.deleted_at IS NULL
      AND st.year = " . $filter['year_id'] . "
      AND st.month = " . $filter['month_id'] . "
      GROUP BY st.block_id,
               st.agency_type_id) bl_txn
      ON bl_txn.block_id = bl.block_id 
      AND bl.agency_type_id=bl_txn.agency_type_id
  UNION ALL
  SELECT
    sd.block_id,
    sd.district_id,
    '' block,
    sd.firstname agency,
    0 phase,
    COALESCE(dist_txn.total, 0) total,
    sd.agency_type_id,sd.fund_agency_id
  FROM (SELECT
      u.user_group_id agency_type_id,
      u.firstname,
      u.district_id,
      u.block_id,
      u.fund_agency_id
    FROM user u
    WHERE u.user_group_id IN (7)) sd
    LEFT JOIN (SELECT
        st.district_id,
        COUNT(st.id) total,
        st.agency_type_id,
        st.fund_agency_id
      FROM soe_closing_balances st
      WHERE st.deleted_at IS NULL
      AND st.year = " . $filter['year_id'] . "
      AND st.month = " . $filter['month_id'] . "
      GROUP BY st.district_id,
               st.agency_type_id,
               st.fund_agency_id) dist_txn
      ON dist_txn.district_id = sd.district_id
      AND dist_txn.agency_type_id = sd.agency_type_id
      AND sd.fund_agency_id = dist_txn.fund_agency_id) res
  LEFT JOIN soe_districts sd
    ON res.district_id = sd.id
WHERE res.total = 0";
    if (!empty($filter['phase'])) {
      if (is_array($filter['phase'])) {
        $sql .= " AND res.phase IN (" . implode(',', $filter['phase']) . ")";
      } else {
        $sql .= " AND res.phase = " . $filter['phase'];
      }
    }

    if (!empty($filter['block_id'])) {
      $sql .= " AND res.block_id=" . $filter['block_id'];
    }

    if (!empty($filter['district_id'])) {
      $sql .= " AND district_id=" . $filter['district_id'];
    }

    if (!empty($filter['fund_agency_id'])) {
      $sql .= " AND fund_agency_id=" . $filter['fund_agency_id'];
    }
    $sql .= " ORDER BY district, res.block";

    return $this->db->query($sql)->getResultArray();
  }

  public function getMis($fitler = [])
  {

    $year = intval($fitler['year_id']);

    $month = (!empty($fitler['month_id'])) ? intval($fitler['month_id']) : 0;
    $district_id = (!empty($fitler['district_id'])) ? intval($fitler['district_id']) : 0;
    $block_id = (!empty($fitler['block_id'])) ? intval($fitler['block_id']) : 0;

    $sql = "SELECT inds.*, ach_upto.total ach_upto_mon, ach_mon.total ach_mon, (ach_mon.total + ach_upto.total) cummulative
        FROM (SELECT mci.id indicator_id, mci.component_id, mci.unit_type, mci.output_indicator, sca.parent, sca.id scomponent_id, sca.number, sc.description, sca.sort_order,sc.row_type,
      sc.category
            FROM soe_components sc
            INNER JOIN mis_component_indicators mci ON sc.id = mci.component_id
            LEFT JOIN soe_components_assign sca ON mci.component_id = sca.component_id AND sca.fund_agency_id = 1 AND sca.deleted_at IS NULL
            WHERE mci.deleted_at IS NULL AND sc.deleted_at IS NULL AND mci.input_type <> 'file'
            ORDER BY sca.sort_order) inds
        LEFT JOIN (SELECT msd.output_indicator_id, SUM(msd.achievement) total
            FROM mis_submissions ms
            LEFT JOIN mis_submission_details msd ON ms.id = msd.submission_id AND ms.deleted_at IS NULL AND msd.deleted_at IS NULL
            WHERE (ms.year BETWEEN 0 AND {$year}) AND ((ms.year = {$year} AND ms.month BETWEEN 0 AND {$month}) OR ms.year < {$year})
            AND (%s)
            GROUP BY msd.output_indicator_id) ach_upto ON inds.indicator_id = ach_upto.output_indicator_id
        LEFT JOIN (SELECT msd.output_indicator_id, SUM(msd.achievement) total
            FROM mis_submissions ms
            LEFT JOIN mis_submission_details msd ON ms.id = msd.submission_id AND ms.deleted_at IS NULL AND msd.deleted_at IS NULL
            WHERE ms.year = {$year} AND ms.month = 1
            AND (%s)
            GROUP BY msd.output_indicator_id) ach_mon ON inds.indicator_id = ach_mon.output_indicator_id";

    $district_filter = '';
    $block_filter = '';
    $params = array();

    if (!empty($district_id)) {
      $district_filter = 'ms.district_id = ?';
      $params[] = $district_id;
    }

    if (!empty($block_id)) {
      $block_filter = 'ms.block_id = ?';
      $params[] = $block_id;
    }

    if (!empty($district_filter) && !empty($block_filter)) {
      $where = "($district_filter AND $block_filter)";
    } elseif (!empty($district_filter)) {
      $where = $district_filter;
    } elseif (!empty($block_filter)) {
      $where = $block_filter;
    } else {
      $where = '1=1';
    }

    $sql = sprintf($sql, $where, $where);

    $query = $this->db->query($sql, $params);
    $result = $query->getResultArray();

    return $result;
  }

  //for SPMU dashboard --rakesh --27/04/2023
  public function getAgencywiseAbstract($filter = [])
  {
    $sql = "SELECT
  dist_yr.agency,
  dist_yr.fund_agency_id,
  dist_yr.agency_type_id,
  dist_yr.year_id,
  dist_yr.year,
  dist_yr.agency_type,
  dist_yr.agency_level,
  COALESCE(CASE WHEN dist_yr.district_id = 0 THEN state_fr.fund_receipt ELSE fr.fund_receipt END, 0) AS fund_receipt,
  COALESCE(CASE WHEN dist_yr.district_id = 0 THEN state_xp.expense ELSE xp.expense END, 0) AS expense
FROM (SELECT
    dist.district_id,
    CASE WHEN dist.district_id = 0 THEN dist.agency_level ELSE dist.district END AS agency,
    dist.fund_agency_id,
    dist.agency_type_id,
    yr.id year_id,
    yr.name year,
    dist.agency_type,
    dist.agency_level
  FROM (SELECT
      u.user_group_id agency_type_id,
      u.id user_id,
      u.firstname,
      u.district_id,
      sd.name district,
      u.fund_agency_id,
      ug.description agency_level,
      ug.name agency_type
    FROM user u
      LEFT JOIN soe_districts sd
        ON u.district_id = sd.id
      LEFT JOIN user_group ug
        ON u.user_group_id = ug.id
    WHERE u.user_group_id IN (7, 8, 11)) dist
    CROSS JOIN (SELECT
        *
      FROM soe_years sy
      WHERE id <= (SELECT
          *
        FROM vw_get_current_year)) yr) dist_yr
  LEFT JOIN (SELECT
      st.year,
      'district' AS level,
      st.district_id,
      sd.name AS agency,
      st.fund_agency_id,
      SUM(stc.financial) AS fund_receipt
    FROM soe_transaction_components stc
      JOIN soe_transactions st
        ON st.id = stc.transaction_id
        AND st.deleted_at IS NULL
        AND st.transaction_type = 'fund_receipt'
        AND st.agency_type_id IN (7)
      JOIN soe_districts sd
        ON st.district_id = sd.id
    WHERE st.deleted_at IS NULL
    AND stc.deleted_at IS NULL
    GROUP BY year,
             st.district_id,
             st.fund_agency_id) fr
    ON dist_yr.district_id = fr.district_id
    AND dist_yr.fund_agency_id = fr.fund_agency_id
    AND dist_yr.year_id = fr.year
  LEFT JOIN (SELECT
      st.year,
      'state' AS level,
      NULL district_id,
      st.agency_type_id,
      NULL AS agency,
      st.fund_agency_id,
      SUM(stc.financial) AS fund_receipt
    FROM soe_transaction_components stc
      JOIN soe_transactions st
        ON st.id = stc.transaction_id
        AND st.deleted_at IS NULL
        AND st.transaction_type = 'fund_receipt'
        AND st.agency_type_id IN (8, 11)
    WHERE st.deleted_at IS NULL
    AND stc.deleted_at IS NULL
    GROUP BY year,
             st.agency_type_id,
             st.fund_agency_id) state_fr
    ON dist_yr.fund_agency_id = state_fr.fund_agency_id
    AND dist_yr.agency_type_id = state_fr.agency_type_id
    AND dist_yr.year_id = state_fr.year
  LEFT JOIN (SELECT
      st.year,
      'district' AS level,
      st.district_id,
      sd.name AS agency,
      st.fund_agency_id,
      SUM(stc.financial) AS expense
    FROM soe_transaction_components stc
      JOIN soe_transactions st
        ON st.id = stc.transaction_id
        AND st.deleted_at IS NULL
        AND st.transaction_type = 'expense'
        AND st.agency_type_id IN (5, 6, 7)
      JOIN soe_districts sd
        ON st.district_id = sd.id
    WHERE st.deleted_at IS NULL
    AND stc.deleted_at IS NULL
    GROUP BY year,
             st.district_id,
             st.fund_agency_id) xp
    ON dist_yr.district_id = xp.district_id
    AND dist_yr.fund_agency_id = xp.fund_agency_id
    AND dist_yr.year_id = xp.year
  LEFT JOIN (SELECT
      st.year,
      'state' AS level,
      NULL district_id,
      st.agency_type_id,
      NULL AS agency,
      st.fund_agency_id,
      SUM(stc.financial) AS expense
    FROM soe_transaction_components stc
      JOIN soe_transactions st
        ON st.id = stc.transaction_id
        AND st.deleted_at IS NULL
        AND st.transaction_type = 'expense'
        AND st.agency_type_id IN (8, 11)
    WHERE st.deleted_at IS NULL
    AND stc.deleted_at IS NULL
    GROUP BY year,
             st.agency_type_id,
             st.fund_agency_id) state_xp
    ON dist_yr.fund_agency_id = state_xp.fund_agency_id
    AND dist_yr.agency_type_id = state_xp.agency_type_id
    AND dist_yr.year_id = state_xp.year WHERE 1=1";
    if (!empty($filter['year'])) {
      $sql .= " AND year_id=" . $filter['year'];
    }
    $sql .= "
ORDER BY year_id, dist_yr.district_id";

    return $this->db->query($sql)->getResultArray();
  }

  //for dashboard --rakesh 26/05/2023
  public function getDistrictwiseOpening($filter = [])
  {
    $year = $filter['year'];
    $ly = $year - 1;
    $fund_agency_id = $filter['fund_agency_id'];

    $sql = "WITH fr_upto AS (
  SELECT
    st.district_id,
    SUM(tc.financial) AS fin
  FROM soe_transaction_components tc
  LEFT JOIN soe_transactions st ON tc.transaction_id = st.id
  WHERE st.deleted_at IS NULL
    AND tc.deleted_at IS NULL
    AND st.transaction_type = 'fund_receipt'
    AND st.fund_agency_id = $fund_agency_id
    AND st.agency_type_id = 7
    AND st.year BETWEEN 0 AND $year
  GROUP BY st.district_id
),
ex_upto AS (
  SELECT
    st.district_id,
    SUM(tc.financial) AS fin
  FROM soe_transaction_components tc
  LEFT JOIN soe_transactions st ON tc.transaction_id = st.id
  WHERE st.deleted_at IS NULL
    AND tc.deleted_at IS NULL
    AND st.transaction_type = 'expense'
    AND st.fund_agency_id = $fund_agency_id
    AND st.agency_type_id IN (5, 6, 7)
    AND st.year BETWEEN 0 AND $ly
  GROUP BY st.district_id
),
expn AS (
  SELECT
    district_id,
    SUM(total) AS ex_total
  FROM vw_district_abstract_txn
  WHERE transaction_type = 'expense'
    AND year = $year
    AND fund_agency_id = $fund_agency_id
  GROUP BY district_id
)
SELECT
  fr.district_id,
  sd.name AS district,
  (COALESCE(fr.fin, 0) - COALESCE(ex.fin, 0)) AS fr_total,
  expn.ex_total
FROM fr_upto fr
LEFT JOIN ex_upto ex ON ex.district_id = fr.district_id
LEFT JOIN soe_districts sd ON fr.district_id = sd.id
LEFT JOIN expn ON expn.district_id = fr.district_id
WHERE fr.district_id > 0";

    return $this->db->query($sql)->getResult();
  }

  //for dashboard --rakesh 26/05/2023
  public function getBlockwiseOpening($filter = [])
  {
    $year = $filter['year'];
    $ly = $year - 1;
    $fund_agency_id = $filter['fund_agency_id'];
    $district_id = $filter['district_id'];

    $sql = "WITH fr_upto AS (
  SELECT
    st.district_id,
    st.block_id,
    SUM(tc.financial) AS fin
  FROM soe_transaction_components tc
  LEFT JOIN soe_transactions st ON tc.transaction_id = st.id
  WHERE st.deleted_at IS NULL
    AND tc.deleted_at IS NULL
    AND st.transaction_type = 'fund_receipt'
    AND st.fund_agency_id = $fund_agency_id
    AND st.agency_type_id IN (5,6)
    AND st.year BETWEEN 0 AND $year
  GROUP BY st.block_id
),
ex_upto AS (
  SELECT
    st.block_id,
    SUM(tc.financial) AS fin
  FROM soe_transaction_components tc
  LEFT JOIN soe_transactions st ON tc.transaction_id = st.id
  WHERE st.deleted_at IS NULL
    AND tc.deleted_at IS NULL
    AND st.transaction_type = 'expense'
    AND st.fund_agency_id = $fund_agency_id
    AND st.agency_type_id IN (5, 6)
    AND st.year BETWEEN 0 AND $ly
  GROUP BY st.block_id
),
expn AS (
  SELECT
      block_id,
      SUM(stc.financial) ex_total
    FROM soe_transactions st
      INNER JOIN soe_transaction_components stc
        ON st.id = stc.transaction_id
    WHERE st.deleted_at IS NULL
    AND stc.deleted_at IS NULL
    AND st.transaction_type = 'expense'
    AND st.year = $year
    AND st.fund_agency_id = $fund_agency_id
    GROUP BY st.block_id
)
SELECT
  fr_upto.block_id,
  sb.name AS block,
  (COALESCE(fr_upto.fin, 0) - COALESCE(ex_upto.fin, 0)) AS fr_total,
  expn.ex_total
FROM fr_upto
LEFT JOIN ex_upto ON ex_upto.block_id = fr_upto.block_id
LEFT JOIN soe_blocks sb ON fr_upto.block_id = sb.id
LEFT JOIN expn ON expn.block_id = fr_upto.block_id
WHERE fr_upto.district_id = $district_id";

    return $this->db->query($sql)->getResult();
  }
}
