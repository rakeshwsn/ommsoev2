<?php

namespace Admin\Common\Models;

use CodeIgniter\Model;

class MonthModel extends Model
{
    // Database settings
    protected $DBGroup              = 'default';
    protected $table                = 'soe_months';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $returnType           = 'object';

    // Timestamps
    protected $useTimestamps        = false;

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->loadMonthsFromCache();
    }

    /**
     * Load months from cache
     */
    private function loadMonthsFromCache()
    {
        // Try to get months from the cache
        if (! $months = cache('months')) {
            try {
                // If not found in the cache, get the data from the database
                $months = $this->asArray()->findAll();
                cache()->save('months', $months, 60 * 60 * 24 * 365);
            } catch (\Exception $e) {
                log_message('error', 'Error loading months from the database: ' . $e->getMessage());
                cache()->save('months', [], 60 * 60 * 24 * 365);
            }
        }

        // Set the loaded months as a property
        $this->months = $months;
    }

    /**
     * Get the month object by id
     *
     * @param int $id
     * @return object
     */
    public function getMonthById(int $id)
    {
        foreach ($this->months as $month) {
            if ($month->id == $id) {
                return $month;
            }
        }

        return null;
    }

    /**
     * Get the month id by month number
     *
     * @param int $month_num
     * @return int
     */
    public function getMonthIdByMonth(int $month_num)
    {
        foreach ($this->months as $month) {
            if ($month->number == $month_num) {
                return $month->id;
            }
        }

        return 0;
    }

    /**
     * Get all months
     *
     * @return array
     */
    public function getMonths()
    {
        return $this->months;
    }

    /**
     * Get months up to a specific month id
     *
     * @param int $month_id
     * @return array
     */
    public function getMonthsUpto(int $month_id)
    {
        return $this->where('id', '<=', $month_id)
            ->orderBy('id', 'ASC')
            ->asArray()
            ->findAll();
    }

    /**
     * Get the previous month object
     *
     * @param int|null $month
     * @return object
     */
    public function getPreviousMonth($month = null)
    {
        if (!$month) {
            $month = date('n');
        }

        $month = $this->getMonthIdByMonth($month);

        foreach ($this->months as $index => $monthObj) {
            if ($monthObj->id == $month) {
                return $this->months[($index - 1)];
            }
        }

        return null;
    }

    /**
     * Get the current month object
     *
    
