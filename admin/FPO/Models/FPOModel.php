<?php

namespace Admin\FPO\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DataException;

class FPOModel extends Model
{
    // ... (same as before)

    public function getFPOByDistrict()
    {
        return $this->queryFPOByDistrict()->getResultArray();
    }

    public function getFPOStatus()
    {
        return $this->queryFPOStatus()->getRowArray();
    }

    public function getFPOByBlock($district_id)
    {
        return $this->queryFPOByBlock($district_id)->getResultArray();
    }

    public function getFPO($id)
    {
        $builder = $this->db->table("{$this->table} f");
        $builder->select("f.*,d.name district,b.name block");
        $builder->join('soe_districts d', 'f.district_id = d.id');
        $builder->join('soe_blocks b', 'f.block_id = b.id');
        $builder->where("f.id", $id);
        $result = $builder->get()->getRow();
        return $result;
    }

    public function getFPOColumns($fpo_id, $form_type)
    {
        $builder = $this->db->table("fpo_columns fc");
        $builder->select("fc.*,fm.column_id,fm.value");
        $builder->join('fpo_master fm', 'fc.id = fm.column_id and fm.fpo_id=' . $fpo_id, 'left');
        $builder->where("fc.form_type", $form_type);
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function getFPOFormData($fpo_id, $form_group)
    {
        $builder = $this->db->table("fpo_columns fc");
        $builder->select("fc.*,fm.column_id,fm.value");
        $builder->join('fpo_master fm', 'fc.id = fm.column_id and fm.fpo_id=' . $fpo_id, 'left');
        $builder->where("fc.parent_id", $form_group);
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function updateFPODetails($id, $data)
    {
        // Validate input
        if (!is_array($data) || empty($data)) {
            throw new DataException('Invalid data provided');
        }

        // Begin transaction
        $this->db->transBegin();

        foreach ($data as $column_id => $value) {
            $fdata = [
                'fpo_id' => $id,
                'column_id' => $column_id,
                'value' => $value
            ];

            try {
                // Check if the record already exists
                $existingRecord = $this->getFPODetails($id, $column_id);

                if (!empty($existingRecord)) {
                    // Update the existing record
                    $builder = $this->db->table('fpo_master');
                    $builder->where('column_id', $column_id);
                    $builder->where('fpo_id', $id);
                    $builder->update($fdata);
                } else {
                    // Insert a new record
                    $builder = $this->db->table('fpo_master');
                    $builder->insert($fdata);
                }
            } catch (DataException $e) {
                // Rollback transaction and rethrow exception
                $this->db->transRollback();
                throw $e;
            }
        }

        // Commit transaction
        $this->db->transCommit();
    }

    public function getFPODetails($fpo_id, $column_id)
    {
        $builder = $this->db->table('fpo_master fm');
        $builder->where("fm.fpo_id", $fpo_id);
        $builder->where("fm.column_id", $column_id);
        $query = $builder->get();
        return $query->getResultArray();
    }

    // ... (same as before)

    private function queryFPOByDistrict()
    {
        $subquery1 = $this->db->table('soe_districts sd')
            ->select('sd.id, sd.name, COUNT(sb.id) total_block')
            ->join('soe_blocks sb', 'sd.id = sb.district_id', 'left')
            ->groupBy('sb.district_id')
            ->getCompiledSelect();

        $subquery2 = $this->db->table('fpo')
            ->select('f.district_id, COUNT(f.block_id) total_registed')
            ->where('f.registered', 1)
            ->groupBy('f.district_id')
            ->getCompiledSelect();

        $query = $this->db->table('(' . $subquery1 . ') d')
            ->select('d.id, d.name, d.total_block, COALESCE(d1.total_registed, 0) total_register')
            ->join($subquery2 . ' d1', 'd.id = d1.district_id', 'left')
            ->get();

        return $query;
    }

    private function queryFPOStatus()
    {
        $query = $this->db->table('fpo f')
            ->select('
                count(if(f.registered="1",1,null))
