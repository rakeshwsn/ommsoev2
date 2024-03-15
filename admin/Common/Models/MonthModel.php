<?php

namespace Admin\Common\Models;

use CodeIgniter\Model;

class MonthModel extends Model
{
    // Database settings
    protected $DBGroup = 'default';
    protected $table = 'soe_months';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';

    // Timestamps
    protected $useTimestamps = false;

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;

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

                // Validate the loaded months
                if (!$this->validateMonths($months)) {
                    throw new \Exception('Invalid months data loaded from the database.');
                }

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
     * Validate the given months data
     *
     * @param array $months
     * @return bool
     */
    private function validateMonths(array $months): bool
    {
        foreach ($months as $month) {
            if (
                !is_int($month->id) ||
                !is_int($month->number) ||
                !in_array($month->number, range(1, 12))
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the month object by id
     *
     * @param int $id
     * @return object|null
     */
    public function getMonthById(int $id): ?object
    {
        foreach ($this->months as $month) {
            if ($month->id == $id) {
                return $month;
            }
        }

        return null;
    }

    // ... Rest of the methods
}
