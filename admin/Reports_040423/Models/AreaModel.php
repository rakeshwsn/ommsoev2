<?php 
namespace Admin\Reports\Models;
use CodeIgniter\Model;

class AreaModel extends Model {
    protected $DBGroup              = 'old';

    public function getAreaCoverage($filter=[]){

        if($filter['block_id']){
            $areas = $this->getGpwiseCC([
                'year' => $filter['year'],
                'season' => $filter['season'],
                'block_id' => $filter['block_id']
            ]);
        } else if($filter['district_id']){
            $areas = $this->getBlockwiseCC([
                'year' => $filter['year'],
                'season' => $filter['season'],
                'district_id' => $filter['district_id']
            ]);
        } else {
            $areas = $this->getDistrictwiseCC([
                'year' => $filter['year'],
                'season' => $filter['season']
            ]);
        }

        return ['areas' => $areas];

    }

    protected function getDistrictwiseCC($filter=[]) {
        $sql = "SELECT
  vdcbg.district_id,
  vdcbg.district,
  vdcbg.total_blocks,
  vdcbg.total_gps,
  distarea2.ragi_nursery,
  distarea2.ragi_smi,
  distarea2.ragi_lt,
  distarea2.ragi_ls,
  distarea2.little_ls,
  distarea2.foxtail_ls,
  distarea2.sorghum_ls,
  distarea2.kodo_ls,
  distcc.total_farmers,
  distcc.intercropping,
  distcc.ragi_inc,
  distcc.ragi_non_inc,
  distcc.non_ragi_inc,
  distcc.non_ragi_non_inc
FROM vw_districtwise_cc_blocks_gps vdcbg LEFT JOIN (SELECT
    vcag.district_id,
    SUM(vcag.nursery) ragi_nursery,
    SUM(vcag.ragi_smi) ragi_smi,
    SUM(vcag.ragi_lt) ragi_lt,
    SUM(vcag.ragi_ls) ragi_ls,
    SUM(vcag.little_ls) little_ls,
    SUM(vcag.foxtail_ls) foxtail_ls,
    SUM(vcag.sorghum_ls) sorghum_ls,
    SUM(vcag.kodo_ls) kodo_ls
  FROM (SELECT * FROM vw_crop_area_gpwise WHERE season='".$filter['season']."') vcag
  GROUP BY vcag.district_id) distarea2 ON distarea2.district_id=vdcbg.district_id
  LEFT JOIN (SELECT
      dcc.district_id,
      SUM(dcc.total_farmers) total_farmers,
      SUM(dcc.intercropping) intercropping,
      SUM(dcc.ragi_inc) ragi_inc,
      SUM(dcc.ragi_non_inc) ragi_non_inc,
      SUM(dcc.non_ragi_inc) non_ragi_inc,
      SUM(dcc.non_ragi_non_inc) non_ragi_non_inc
    FROM dashboard_crop_coverage dcc
    WHERE dcc.deleted = 0
    AND dcc.year = ".$filter['year']."
    AND dcc.season = '".$filter['season']."'
    GROUP BY dcc.district_id) distcc
    ON vdcbg.district_id = distcc.district_id";
        
        $districts = $this->db->query($sql)->getResult();
        $rows = [];

        $_total_block_gps = $_total_dist_gps = $_block_id =  0;
        $_total_blocks = $_district_id = $_total_farmers =  0;
        $_intercropping = $_ragi_inc = $_ragi_non_inc = $_non_ragi_inc =  0;
        $_non_ragi_non_inc = $_ragi_nursery = $_ragi_smi = $_ragi_ls = $_ragi_lt = 0;
        $_little_ls = $_foxtail_ls = $_sorghum_ls = $_kodo_ls =0;
        $_total_ragi = $_total_non_ragi = $_grand_total = 0;

        foreach ($districts as $row) {
            $total_ragi = $row->ragi_smi + $row->ragi_ls + $row->ragi_lt;
            $total_non_ragi = $row->little_ls + $row->foxtail_ls + $row->sorghum_ls + $row->kodo_ls;
            $grand_total = $total_ragi + $total_non_ragi;
            $rows[] = [
                'total_block_gps' => 0,
                'total_dist_gps' => $row->total_gps,
                'block_id' => 0,
                'total_blocks' => $row->total_blocks,
                'gp' => '',
                'district_id' => $row->district_id,
                'total_farmers' => $row->total_farmers,
                'intercropping' => round($row->intercropping,2),
                'ragi_inc' => round($row->ragi_inc,2),
                'ragi_non_inc' => round($row->ragi_non_inc,2),
                'non_ragi_inc' => round($row->non_ragi_inc,2),
                'non_ragi_non_inc' => round($row->non_ragi_non_inc,2),
                'ragi_nursery' => round($row->ragi_nursery,2),
                'ragi_smi' => round($row->ragi_smi,2),
                'ragi_ls' => round($row->ragi_ls,2),
                'ragi_lt' => round($row->ragi_lt,2),
                'little_ls' => round($row->little_ls,2),
                'foxtail_ls' => round($row->foxtail_ls,2),
                'sorghum_ls' => round($row->sorghum_ls,2),
                'kodo_ls' => round($row->kodo_ls,2),
                'total_ragi' => round($total_ragi,2),
                'total_non_ragi' => round($total_non_ragi,2),
                'grand_total' => round($grand_total,2),
                'block' => '',
                'district' => $row->district,
            ];

            $_total_block_gps += 0;
            $_total_dist_gps += $row->total_gps;
            $_total_blocks += $row->total_blocks;
            $_total_farmers +=  $row->total_farmers;
            $_intercropping += $row->intercropping;
            $_ragi_inc += $row->ragi_inc;
            $_ragi_non_inc += $row->ragi_non_inc;
            $_non_ragi_inc += $row->non_ragi_inc;
            $_non_ragi_non_inc += $row->non_ragi_non_inc;
            $_ragi_nursery += $row->ragi_nursery;
            $_ragi_smi += $row->ragi_smi;
            $_ragi_ls += $row->ragi_ls;
            $_ragi_lt += $row->ragi_lt;
            $_little_ls += $row->little_ls;
            $_foxtail_ls += $row->foxtail_ls;
            $_sorghum_ls += $row->sorghum_ls;
            $_kodo_ls += $row->kodo_ls;
            $_total_ragi += $total_ragi;
            $_total_non_ragi += $total_non_ragi;
            $_grand_total += $grand_total;
        }

        $rows[] = [
            'total_block_gps' => $_total_block_gps,
            'total_dist_gps' => $_total_dist_gps,
            'block_id' => '',
            'total_blocks' => $_total_blocks,
            'gp' => '',
            'district_id' => '',
            'total_farmers' => round($_total_farmers,2),
            'intercropping' => round($_intercropping,2),
            'ragi_inc' => round($_ragi_inc,2),
            'ragi_non_inc' => round($_ragi_non_inc,2),
            'non_ragi_inc' => round($_non_ragi_inc,2),
            'non_ragi_non_inc' => round($_non_ragi_non_inc,2),
            'ragi_nursery' => round($_ragi_nursery,2),
            'ragi_smi' => round($_ragi_smi,2),
            'ragi_ls' => round($_ragi_ls,2),
            'ragi_lt' => round($_ragi_lt,2),
            'little_ls' => round($_little_ls,2),
            'foxtail_ls' => round($_foxtail_ls,2),
            'sorghum_ls' => round($_sorghum_ls,2),
            'kodo_ls' => round($_kodo_ls,2),
            'block' => 'Total',
            'district' => 'Total',
            'total_ragi' => round($_total_ragi,2),
            'total_non_ragi' => round($_total_non_ragi,2),
            'grand_total' => round($_grand_total,2),
        ];

        return $rows;

    }

    protected function getBlockwiseCC($filter=[]) {
        $sql = "SELECT
  blocks.block_id,
  blocks.block,
  blocks.district_id,
  blocks.total_gps,
  cc.total_farmers,
  cc.total_intercropping intercropping,
  cc.total_ragi_inc ragi_inc,
  cc.total_ragi_non_inc ragi_non_inc,
  cc.total_non_ragi_inc non_ragi_inc,
  cc.total_non_ragi_non_inc non_ragi_non_inc,
  crop.ragi_nursery,
  crop.ragi_smi,
  crop.ragi_lt,
  crop.ragi_ls,
  crop.little_ls,
  crop.foxtail_ls,
  crop.sorghum_ls,
  crop.kodo_ls
FROM (SELECT
    vbcg.block_id,
    vbcg.block,
    vbcg.district_id,
    vbcg.total_gps
  FROM vw_blockwise_cc_gps vbcg
  WHERE vbcg.district_id = ".$filter['district_id'].") blocks
  LEFT JOIN (SELECT
      dcc.block_id,
      SUM(dcc.total_farmers) total_farmers,
      SUM(dcc.intercropping) total_intercropping,
      SUM(dcc.ragi_inc) total_ragi_inc,
      SUM(dcc.ragi_non_inc) total_ragi_non_inc,
      SUM(dcc.non_ragi_inc) total_non_ragi_inc,
      SUM(dcc.non_ragi_non_inc) total_non_ragi_non_inc
    FROM dashboard_crop_coverage dcc
    WHERE dcc.deleted = 0
    AND dcc.year = ".$filter['year']."
    AND dcc.season = '".$filter['season']."'
    GROUP BY dcc.block_id) cc
    ON blocks.block_id = cc.block_id
  LEFT JOIN (SELECT
      vcag.block_id,
      SUM(vcag.nursery) ragi_nursery,
      SUM(vcag.ragi_smi) ragi_smi,
      SUM(vcag.ragi_lt) ragi_lt,
      SUM(vcag.ragi_ls) ragi_ls,
      SUM(vcag.little_ls) little_ls,
      SUM(vcag.foxtail_ls) foxtail_ls,
      SUM(vcag.sorghum_ls) sorghum_ls,
      SUM(vcag.kodo_ls) kodo_ls
    FROM vw_crop_area_gpwise vcag WHERE vcag.season='".$filter['season']."'
    GROUP BY vcag.block_id) crop
    ON crop.block_id = blocks.block_id";

        $districts = $this->db->query($sql)->getResult();
        $rows = [];

        $_total_block_gps = $_total_dist_gps = $_block_id =  0;
        $_total_blocks = $_district_id = $_total_farmers =  0;
        $_intercropping = $_ragi_inc = $_ragi_non_inc = $_non_ragi_inc =  0;
        $_non_ragi_non_inc = $_ragi_nursery = $_ragi_smi = $_ragi_ls = $_ragi_lt = 0;
        $_little_ls = $_foxtail_ls = $_sorghum_ls = $_kodo_ls =0;
        $_total_ragi = $_total_non_ragi = $_grand_total = 0;

        foreach ($districts as $row) {
            $total_ragi = $row->ragi_smi + $row->ragi_ls + $row->ragi_lt;
            $total_non_ragi = $row->little_ls + $row->foxtail_ls + $row->sorghum_ls + $row->kodo_ls;
            $grand_total = $total_ragi + $total_non_ragi;
            $rows[] = [
                'total_block_gps' => $row->total_gps,
                'total_dist_gps' => $row->total_gps,
                'total_blocks' => 0,
                'gp' => '',
                'block_id' => $row->block_id,
                'block' => $row->block,
                'district_id' => $row->district_id,
                'district' => '',
                'total_farmers' => $row->total_farmers,
                'intercropping' => round($row->intercropping,2),
                'ragi_inc' => round($row->ragi_inc,2),
                'ragi_non_inc' => round($row->ragi_non_inc,2),
                'non_ragi_inc' => round($row->non_ragi_inc,2),
                'non_ragi_non_inc' => round($row->non_ragi_non_inc,2),
                'ragi_nursery' => round($row->ragi_nursery,2),
                'ragi_smi' => round($row->ragi_smi,2),
                'ragi_ls' => round($row->ragi_ls,2),
                'ragi_lt' => round($row->ragi_lt,2),
                'little_ls' => round($row->little_ls,2),
                'foxtail_ls' => round($row->foxtail_ls,2),
                'sorghum_ls' => round($row->sorghum_ls,2),
                'kodo_ls' => round($row->kodo_ls,2),
                'total_ragi' => round($total_ragi,2),
                'total_non_ragi' => round($total_non_ragi,2),
                'grand_total' => round($grand_total,2),
            ];

            $_total_block_gps += $row->total_gps;
            $_total_dist_gps += $row->total_gps;
            $_total_blocks += 0;
            $_total_farmers +=  $row->total_farmers;
            $_intercropping += $row->intercropping;
            $_ragi_inc += $row->ragi_inc;
            $_ragi_non_inc += $row->ragi_non_inc;
            $_non_ragi_inc += $row->non_ragi_inc;
            $_non_ragi_non_inc += $row->non_ragi_non_inc;
            $_ragi_nursery += $row->ragi_nursery;
            $_ragi_smi += $row->ragi_smi;
            $_ragi_ls += $row->ragi_ls;
            $_ragi_lt += $row->ragi_lt;
            $_little_ls += $row->little_ls;
            $_foxtail_ls += $row->foxtail_ls;
            $_sorghum_ls += $row->sorghum_ls;
            $_kodo_ls += $row->kodo_ls;
            $_total_ragi += $total_ragi;
            $_total_non_ragi += $total_non_ragi;
            $_grand_total += $grand_total;
        }

        $rows[] = [
            'total_block_gps' => $_total_block_gps,
            'total_dist_gps' => $_total_dist_gps,
            'block_id' => '',
            'total_blocks' => $_total_blocks,
            'gp' => '',
            'district_id' => '',
            'total_farmers' => round($_total_farmers,2),
            'intercropping' => round($_intercropping,2),
            'ragi_inc' => round($_ragi_inc,2),
            'ragi_non_inc' => round($_ragi_non_inc,2),
            'non_ragi_inc' => round($_non_ragi_inc,2),
            'non_ragi_non_inc' => round($_non_ragi_non_inc,2),
            'ragi_nursery' => round($_ragi_nursery,2),
            'ragi_smi' => round($_ragi_smi,2),
            'ragi_ls' => round($_ragi_ls,2),
            'ragi_lt' => round($_ragi_lt,2),
            'little_ls' => round($_little_ls,2),
            'foxtail_ls' => round($_foxtail_ls,2),
            'sorghum_ls' => round($_sorghum_ls,2),
            'total_ragi' => round($_total_ragi,2),
            'total_non_ragi' => round($_total_non_ragi,2),
            'grand_total' => round($_grand_total,2),
            'kodo_ls' => round($_kodo_ls,2),
            'block' => 'Total',
            'district' => 'Total',
        ];

        return $rows;

    }

    protected function getGpwiseCC($filter=[]) {
        $sql = "SELECT
  gps.block,gps.gp,gps.gp_id,
  cc.total_farmers,
  cc.total_intercropping intercropping,
  cc.total_ragi_inc ragi_inc,
  cc.total_ragi_non_inc ragi_non_inc,
  cc.total_non_ragi_inc non_ragi_inc,
  cc.total_non_ragi_non_inc non_ragi_non_inc,
  crop.ragi_nursery,
  crop.ragi_smi,
  crop.ragi_lt,
  crop.ragi_ls,
  crop.little_ls,
  crop.foxtail_ls,
  crop.sorghum_ls,
  crop.kodo_ls
FROM (SELECT b.name block,dcag.id gp_id,dcag.name gp
  FROM dashboard_crop_area_gp dcag LEFT JOIN blocks b ON dcag.block_id = b.id
  WHERE dcag.deleted=0 AND dcag.block_id = ".$filter['block_id'].") gps
  LEFT JOIN (SELECT
      dcc.gp_id,
      SUM(dcc.total_farmers) total_farmers,
      SUM(dcc.intercropping) total_intercropping,
      SUM(dcc.ragi_inc) total_ragi_inc,
      SUM(dcc.ragi_non_inc) total_ragi_non_inc,
      SUM(dcc.non_ragi_inc) total_non_ragi_inc,
      SUM(dcc.non_ragi_non_inc) total_non_ragi_non_inc
    FROM dashboard_crop_coverage dcc
    WHERE dcc.deleted = 0
    AND dcc.year = ".$filter['year']."
    AND dcc.season = '".$filter['season']."'
    GROUP BY dcc.gp_id) cc
    ON gps.gp_id = cc.gp_id
  LEFT JOIN (SELECT
      vcag.gp_id,
      SUM(vcag.nursery) ragi_nursery,
      SUM(vcag.ragi_smi) ragi_smi,
      SUM(vcag.ragi_lt) ragi_lt,
      SUM(vcag.ragi_ls) ragi_ls,
      SUM(vcag.little_ls) little_ls,
      SUM(vcag.foxtail_ls) foxtail_ls,
      SUM(vcag.sorghum_ls) sorghum_ls,
      SUM(vcag.kodo_ls) kodo_ls
    FROM vw_crop_area_gpwise vcag
    GROUP BY vcag.gp_id) crop
    ON crop.gp_id = gps.gp_id";

        $districts = $this->db->query($sql)->getResult();
        $rows = [];

        $_total_block_gps = $_total_dist_gps = $_block_id =  0;
        $_total_blocks = $_district_id = $_total_farmers =  0;
        $_intercropping = $_ragi_inc = $_ragi_non_inc = $_non_ragi_inc =  0;
        $_non_ragi_non_inc = $_ragi_nursery = $_ragi_smi = $_ragi_ls = $_ragi_lt = 0;
        $_little_ls = $_foxtail_ls = $_sorghum_ls = $_kodo_ls =0;
        $_total_ragi = $_total_non_ragi = $_grand_total = 0;

        foreach ($districts as $row) {
            $total_ragi = $row->ragi_smi + $row->ragi_ls + $row->ragi_lt;
            $total_non_ragi = $row->little_ls + $row->foxtail_ls + $row->sorghum_ls + $row->kodo_ls;
            $grand_total = $total_ragi + $total_non_ragi;
            $rows[] = [
                'total_block_gps' => 0,
                'total_dist_gps' => 0,
                'total_blocks' => 0,
                'gp' => $row->gp,
                'block_id' => 0,
                'block' => $row->block,
                'district_id' => 0,
                'district' => '',
                'total_farmers' => $row->total_farmers,
                'intercropping' => round($row->intercropping,2),
                'ragi_inc' => round($row->ragi_inc,2),
                'ragi_non_inc' => round($row->ragi_non_inc,2),
                'non_ragi_inc' => round($row->non_ragi_inc,2),
                'non_ragi_non_inc' => round($row->non_ragi_non_inc,2),
                'ragi_nursery' => round($row->ragi_nursery,2),
                'ragi_smi' => round($row->ragi_smi,2),
                'ragi_ls' => round($row->ragi_ls,2),
                'ragi_lt' => round($row->ragi_lt,2),
                'little_ls' => round($row->little_ls,2),
                'foxtail_ls' => round($row->foxtail_ls,2),
                'sorghum_ls' => round($row->sorghum_ls,2),
                'kodo_ls' => round($row->kodo_ls,2),
                'total_ragi' => round($total_ragi,2),
                'total_non_ragi' => round($total_non_ragi,2),
                'grand_total' => round($grand_total,2),
            ];

            $_total_block_gps += 0;
            $_total_dist_gps += 0;
            $_total_blocks += 0;
            $_total_farmers +=  $row->total_farmers;
            $_intercropping += $row->intercropping;
            $_ragi_inc += $row->ragi_inc;
            $_ragi_non_inc += $row->ragi_non_inc;
            $_non_ragi_inc += $row->non_ragi_inc;
            $_non_ragi_non_inc += $row->non_ragi_non_inc;
            $_ragi_nursery += $row->ragi_nursery;
            $_ragi_smi += $row->ragi_smi;
            $_ragi_ls += $row->ragi_ls;
            $_ragi_lt += $row->ragi_lt;
            $_little_ls += $row->little_ls;
            $_foxtail_ls += $row->foxtail_ls;
            $_sorghum_ls += $row->sorghum_ls;
            $_kodo_ls += $row->kodo_ls;
            $_total_ragi += $total_ragi;
            $_total_non_ragi += $total_non_ragi;
            $_grand_total += $grand_total;
        }

        $rows[] = [
            'total_block_gps' => $_total_block_gps,
            'total_dist_gps' => $_total_dist_gps,
            'block_id' => '',
            'total_blocks' => $_total_blocks,
            'gp' => '',
            'district_id' => '',
            'total_farmers' => $_total_farmers,
            'intercropping' => round($_intercropping,2),
            'ragi_inc' => round($_ragi_inc,2),
            'ragi_non_inc' => round($_ragi_non_inc,2),
            'non_ragi_inc' => round($_non_ragi_inc,2),
            'non_ragi_non_inc' => round($_non_ragi_non_inc,2),
            'ragi_nursery' => round($_ragi_nursery,2),
            'ragi_smi' => round($_ragi_smi,2),
            'ragi_ls' => round($_ragi_ls,2),
            'ragi_lt' => round($_ragi_lt,2),
            'little_ls' => round($_little_ls,2),
            'foxtail_ls' => round($_foxtail_ls,2),
            'sorghum_ls' => round($_sorghum_ls,2),
            'kodo_ls' => round($_kodo_ls,2),
            'total_ragi' => round($_total_ragi,2),
            'total_non_ragi' => round($_total_non_ragi,2),
            'grand_total' => round($_grand_total,2),
            'block' => 'Total',
            'district' => 'Total',
        ];

        return $rows;

    }

    public function getYearByDate($date) {
        $sql = "SELECT * FROM years y WHERE DATE('$date') BETWEEN DATE(y.start_date) AND DATE(y.end_date)";

        return $this->db->query($sql)->getFirstRow();
    }

    public function getSeasons(){
        return [
            [
                'id' => 'rabi',
                'name' => 'Rabi'
            ],
            [
                'id' => 'kharif',
                'name' => 'Kharif'
            ],
        ];
    }

    public function getCurrentSeason() {
        $month = getCurrentMonthId();
        $kharif = [3,4,5,6,7];
        if(in_array($month,$kharif)){
            return 'kharif';
        } else {
            return 'rabi';
        }

    }
}