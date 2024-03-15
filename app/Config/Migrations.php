<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Migrations extends BaseConfig
{
    /**
     * Whether to enable or disable migrations.
     *
     * @var bool
     */
    public bool $isEnabled = true;

    /**
     * The name of the table that will store the current migrations state.
     *
     * @var string
     */
    public string $migrationTrackingTable = 'migrations';

    /**
     * The format that will be used when creating new migrations
     * using the CLI command.
     *
     * @var string
     */
    public string $timestampFormatForNewMigrations = 'Y-m-d-His_';

    /**
     * Validate the configuration values.
     */
    public function validate(): void
    {
        if ($this->isEnabled === false && env('APP_ENV') !== 'local') {
            throw new \RuntimeException('Migrations are disabled in production. Please enable migrations or set the environment to local for development.');
        }

        if (! is_table_exists($this->migrationTrackingTable)) {
            throw new \RuntimeException("The migration tracking table '{$this->migrationTrackingTable}' does not exist.");
        }
    }
}

