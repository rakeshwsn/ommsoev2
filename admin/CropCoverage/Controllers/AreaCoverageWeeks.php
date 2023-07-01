<?php
namespace Admin\CropCoverage\Controllers;
use App\Controllers\AdminController;
use Admin\CropCoverage\Models\AreaCoverageWeeksModel;

class AreaCoverageWeeks extends AdminController {

    private $error = array();

    function __construct(){
        $this->areacoverageweeksmodel=new AreaCoverageWeeksModel();

    }
    public function Index(){

        if ($this->request->getMethod(1) === 'POST'){ 

            $start_date=$this->request->getPost('start_date');
            $end_date=$this->request->getPost('end_date');

            $week_start = "Fri";
            $start = new \DateTime($start_date);
            $end = new \DateTime($end_date);
            $week_start_index = array_search(strtolower($week_start), array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'));

            $output = array();

            while ($start <= $end) {
            $day_of_week = (int) $start->format('w');
    
              if ($day_of_week === $week_start_index && $start >= $start && $start <= $end) {
            $output[] = array(
                  $start->format('Y-m-d'),
                  min($start->modify('+5 days'), $end)->format('Y-m-d') 
                );
            }
        $start->modify('+2 day');
        }

        $week_days = [];
            foreach($output as $week){
           
                   $week_days[] = [

                       'year' => $this->request->getPost('year'),
                       'season' => $this->request->getPost('season'),
                       'start_date' =>$week[0],
                       'end_date' => $week[1],
              
                    ];
            }
            // printr($week_days);
            // exit;
            $this->areacoverageweeksmodel->db->table('ac_crop_weeks')->insertBatch($week_days);

            
            $this->session->setFlashdata('message', 'Week Saved Successfully.');
            return redirect()->to(base_url('admin/areacoverage/weeks'));
             
        }
                
        $data['heading_title'] = lang('CropCoverage.heading_title');

        $data['weeks']= $this->areacoverageweeksmodel->getWeeks();

        return $this->template->view('Admin\CropCoverage\Views\areacoverageweeks', $data);
        
    }
      
}
?>
