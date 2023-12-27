<?php
namespace Admin\Event\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
	protected $DBGroup = 'default';
	protected $table = 'events';
	protected $primaryKey = 'id';
	protected $useAutoIncrement = true;
	protected $insertID = 0;
	protected $returnType = 'object';
	protected $useSoftDelete = false;
	protected $protectFields = true;
	protected $allowedFields = [];

	// Dates
	protected $useTimestamps = false;
	protected $dateFormat = 'datetime';
	protected $createdField = 'created_at';
	protected $updatedField = 'updated_at';
	protected $deletedField = 'deleted_at';

	// Validation
	protected $validationRules = [
		'name' => array(
			'label' => 'Name Of Event',
			'rules' => 'trim|required|min_length[3]|max_length[20]'
		),
		'objective' => array(
			'label' => 'Objective Of The Event',
			'rules' => 'trim|required|min_length[3]|max_length[100]'
		),
		'occasion' => array(
			'label' => 'Occasion Of Event',
			'rules' => 'trim|required|min_length[3]|max_length[50]'
		),
		'event_date_from' => array(
			'label' => 'event_date_from',
			'rules' => 'trim|required'
		),
		'event_date_to' => array(
			'label' => 'Date Of Event To',
			'rules' => 'trim|required'
		),
		'place' => array(
			'label' => 'Place Of Event',
			'rules' => 'trim|required|min_length[3]|max_length[20]'
		),

		'event_days' => array(
			'label' => 'No Of Event Days',
			'rules' => 'trim|required'
		),
		'no_visitor' => array(
			'label' => 'No Of Visitor',
			'rules' => 'trim|required'
		),
		'total_visitor' => array(
			'label' => 'Tentative No Of Visitor',
			'rules' => 'trim|required'
		),
		'stakeholder' => array(
			'label' => 'Other Stakeholder involved (Collaborations)',
			'rules' => 'trim|required|min_length[3]|max_length[50]'
		),
		'guest' => array(
			'label' => 'Special Guest To Event(Name and Designation)',
			'rules' => 'trim|required|min_length[3]|max_length[50]'
		),
		'feedback' => array(
			'label' => 'Any Feedback',
			'rules' => 'trim|required|min_length[3]|max_length[100]'
		),

		'involved' => array(
			'label' => 'SHG/FPO/FA (Involved):Name',
			'rules' => 'trim|required|min_length[3]|max_length[50]'
		),
		'report_file' => array(
			'label' => 'Event Report',
			'rules' => 'trim|required'
			// 'rules' => 'uploaded[report]|max_size[report,1024]',
		),
		'report' => array(
			'label' => 'Event Report',
			// 'rules' => 'trim|required'
			'rules' => 'max_size[report,1024]',
		)
		// 'status' => array(
		// 	'field' => 'status', 
		// 	'label' => 'Status', 
		// 	'rules' => 'trim|required'
		// )       


	];
	protected $validationMessages = [];
	protected $skipValidation = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks = true;
	protected $beforeInsert = [];
	protected $afterInsert = [];
	protected $beforeUpdate = [];
	protected $afterUpdate = [];
	protected $beforeFind = [];
	protected $afterFind = [];
	protected $beforeDelete = [];
	protected $afterDelete = [];

	//protected $db;

	public function __construct()
	{
		parent::__construct();
		//$this->db = \Config\Database::connect();
	}

	public function getEvents($data = array())
	{
		$builder = $this->db->table("{$this->table} b");
		$this->filter($builder, $data);

		$builder->select("b.*");

		if (isset($data['sort']) && $data['sort']) {
			$sort = $data['sort'];
		} else {
			$sort = "b.name";
		}

		if (isset($data['order']) && ($data['order'] == 'desc')) {
			$order = "desc";
		} else {
			$order = "asc";
		}
		$builder->orderBy($sort, $order);

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 10;
			}
			$builder->limit((int) $data['limit'], (int) $data['start']);
		}
		//$builder->where($this->deletedField, null);

		$res = $builder->get()->getResult();
		return $res;

	}

	public function getTotalEvents($data = array())
	{
		$builder = $this->db->table("{$this->table} b");
		$this->filter($builder, $data);
		$count = $builder->countAllResults();
		return $count;
	}

	public function getEvent($id)
	{
		$builder = $this->db->table("{$this->table} b");
		$builder->where("id", $id);
		$res = $builder->get()->getRow();
		return $res;
	}

	public function editEvent($id, $data)
	{
		$builder = $this->db->table("{$this->table}");

		$eventdata = array(
			"name" => $data['name'],
			"objective" => $data['objective'],
			"occasion" => $data['occasion'],
			"event_date_from" => $data['event_date_from'],
			"event_date_to" => $data['event_date_to'],
			"place" => $data['place'],
			"event_days" => $data['event_days'],
			"no_visitor" => $data['no_visitor'],
			"total_visitor" => $data['total_visitor'],
			"stakeholder" => $data['stakeholder'],
			"guest" => $data['guest'],
			"feedback" => $data['feedback'],
			"involved" => $data['involved'],
			"status" => $data['status'],
			//"report"=>
		);

		$builder->where("id", $id);
		$builder->update($eventdata);

		$builder = $this->db->table("events_gallery");
		$builder->where("event_id", $id);
		$builder->delete();

		if (isset($data['event_image'])) {
			$sort_order = 1;
			foreach ($data['event_image'] as $event_image) {
				$event_image_data = array(
					"event_id" => $id,
					"image" => $event_image['image'],
					"title" => $event_image['title'],
					"link" => $event_image['link'],
					"description" => $event_image['description'],
					"sort_order" => $sort_order
				);
				$builder->insert($event_image_data);
				$sort_order++;
			}
		}

		return "success";
	}

	public function addEvent($data, $originalname)
	{

		$eventdata = array(
			"name" => $data['name'],
			"objective" => $data['objective'],
			"occasion" => $data['occasion'],
			"event_date_from" => $data['event_date_from'],
			"event_date_to" => $data['event_date_to'],
			"place" => $data['place'],
			"event_days" => $data['event_days'],
			"no_visitor" => $data['no_visitor'],
			"total_visitor" => $data['total_visitor'],
			"stakeholder" => $data['stakeholder'],
			"guest" => $data['guest'],
			"feedback" => $data['feedback'],
			"involved" => $data['involved'],
			"report" => $originalname,
			"status" => $data['status']
		);
		$this->db->table('events')->insert($eventdata);
		$id = $this->db->insertID();

		if (isset($data['event_image'])) {
			$sort_order = 1;
			foreach ($data['event_image'] as $event_image) {
				$event_image_data = array(
					"event_id" => $id,
					"image" => $event_image['image'],
					"title" => $event_image['title'],
					"link" => $event_image['link'],
					"description" => $event_image['description'],
					"sort_order" => $sort_order
				);
				$this->db->table("events_gallery")->insert($event_image_data);
				$sort_order++;
			}
		}
	}


	public function getEventImages($id)
	{
		$builder = $this->db->table('events_gallery')
			->orderBy("sort_order", "asc")
			->Where(['event_id' => $id])
			->get();

		$event_image_data = $builder->getResultArray();
		return $event_image_data;
	}

	public function getEventImagess($id)
	{
		$builder = $this->db->table('events_gallery')
			->orderBy("sort_order", "asc")
			->Where(['event_id' => $id])
			->get();

		$event_image_data = $builder->getRowArray();
		return $event_image_data;
	}

	private function filter($builder, $data)
	{


		if (!empty($data['filter_search'])) {
			$builder->where("
				b.title LIKE '%{$data['filter_search']}%'"
			);
		}
	}

	public function deleteEvent($selected = [])
	{
		$builder = $this->db->table('events');
		$builder->whereIn("id", $selected)->delete();

		$builder = $this->db->table('events_gallery ');
		$builder->whereIn("event_id", $selected)->delete();

	}

}
?>