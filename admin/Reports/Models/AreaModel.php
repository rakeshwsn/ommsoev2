<?php

namespace Admin\Reports\Models;

use CodeIgniter\Model;

class AreaModel extends Model
{
    protected $DBGroup = 'old';

    protected $validSeasons = ['rabi', 'kharif'];

    public function getAreaCoverage($filter = [])
    {
        if (in_array($filter['level'], ['block_id', 'district_id'])) {
            return $this->{"get{$filter['level']}wiseCC"}( $filter );
        }

        return ['areas' => $this->getDistrictwiseCC($filter)];
    }

    protected function getDistrictwiseCC($filter = [])
    {
        $query = $this->buildQuery($filter, 'district_id');

        $result = $query->getResult();

        return $this->processResults($result);
    }

    protected function getBlockwiseCC($filter = [])
    {
        $query = $this->buildQuery($filter, 'block_id');

        $result = $query->getResult();

        return $this->processResults($result);
    }

    protected function getGpwiseCC($filter = [])
    {
        $query = $this->buildQuery($filter, 'gp_id');

        $result = $query->getResult();

        return $this->processResults($result);
    }

    protected function buildQuery($filter, $column)
    {
        $filter['season'] = $this->db->escape($filter['season']);
        $filter['year'] = (int)$filter['year'];

        $columns = [
            'district_id', 'district', 'total_blocks', 'total_gps',
            'ragi_nursery', 'ragi_smi', 'ragi_lt', 'ragi_ls',
            'little_ls', 'foxtail_ls', 'sorghum_ls', 'kodo_ls',
            'total_farmers', 'intercropping', 'ragi_inc',
            'ragi_non_inc', 'non_ragi_inc', 'non_ragi_non_inc'
        ];

        $select = implode(', ', $columns);
        $join1 = "LEFT JOIN (
            SELECT
                vcag.{$column},
                SUM(vcag.nursery) ragi_nursery,
                SUM(vcag.ragi_smi) ragi_smi,
                SUM(vcag.ragi_lt) ragi_lt,
                SUM(vcag.ragi_ls) ragi_ls,
                SUM(vcag.little_ls) little_ls,
                SUM(vcag.foxtail_ls) foxtail_ls,
                SUM(vcag.sorghum_ls) sorghum_ls,
                SUM(vcag.kodo_ls) kodo_ls
            FROM vw_crop_area_gpwise vcag
            WHERE vcag.season = {$filter['season']}
            GROUP BY vcag.{$column}
        ) distarea ON distarea.{$column} = vdcbg.{$column}";

        $join2 = "LEFT JOIN (
            SELECT
                dcc.{$column},
                SUM(dcc.total_farmers) total_farmers,
                SUM(dcc.intercropping) intercropping,
                SUM(dcc.ragi_inc) ragi_inc,
                SUM(dcc.ragi_non_inc) ragi_non_inc,
                SUM(dcc.non_ragi_inc) non_ragi_inc,
                SUM(dcc.non_ragi_non_inc) non_ragi_non_inc
            FROM dashboard_crop_coverage dcc
            WHERE dcc.deleted = 0
            AND dcc.year = {$filter['year']}
            AND dcc.season = {$filter['season']}
            GROUP BY dcc.{$column}
        ) distcc ON vdcbg.{$column} = distcc.{$column}";

        $query = $this->db
            ->select($select)
            ->from('vw_districtwise_cc_blocks_gps vdcbg')
            ->join('blocks b', 'vdcbg.district_id = b.id', 'left')
            ->join('districts d', 'vdcbg.district_id = d.id', 'left')
            ->join('states s', 'd.state_id = s.id', 'left')
            ->groupBy('vdcbg.district_id')
            ->orderBy('s.name')
            ->orderBy('d.name');

        if (isset($filter['district_id'])) {
            $query->where("vdcbg.district_id", $filter['district_id']);
        }

        $query->join($join1, 'left');
        $query->join($join2, 'left');

        return $query;
    }

    protected function processResults($result)
    {
        $rows = [];
        $total = [
            'total_block_gps' => 0,
            'total_dist_gps' => 0,
            'total_blocks' => 0,
            'total_farmers' => 0,
            'intercropping' => 0,
            'ragi_inc' => 0,
            'ragi_non_inc' => 0,
            'non_ragi_inc' => 0,
            'non_ragi_non_inc' => 0,
            'ragi_nursery' => 0,
            'ragi_smi' => 0,
            'ragi_ls' => 0,
            'ragi_lt' => 0,
            'little_ls' => 0,
            'foxt
