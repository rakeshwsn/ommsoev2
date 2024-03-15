<?php

namespace Admin\Event\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        // ... validation rules ...
    ];

    protected $skipValidation = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function getEvents($data = [])
    {
        // ... query building and execution ...
    }

    public function getTotalEvents($data = [])
    {
        // ... query building and execution ...
    }

    public function getEvent($id)
    {
        // ... query building and execution ...
    }

    public function editEvent($id, $data)
    {
        // ... update query and deletion of related records ...
    }

    public function addEvent($data, $originalname)
    {
        // ... insertion of new record ...
    }

    public function getEventImages($id)
    {
        // ... query building and execution ...
    }

    public function getEventImagess($id)
    {
        // ... query building and execution ...
    }

    public function filter($builder, $data)
    {
        // ... filtering logic ...
    }

    public function deleteEvent($selected = [])
    {
        // ... deletion of records ...
    }
}
