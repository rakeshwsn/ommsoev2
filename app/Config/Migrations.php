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
	public $isEnabled = true;

	/**
	 * The name of the table that will store the current migrations state.
	 *
	 * @var string
	 */
	public $migrationTrackingTable = 'migrations';

	/**
	 * The format that will be used when creating new migrations
	 * using the CLI command.
	 *
	 * @var string
	 */
	public $timestampFormatForNewMigrations = 'Y-m-d-His_';
}
