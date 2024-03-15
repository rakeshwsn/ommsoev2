<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Generators extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Generator Command View Mappings
     * ---------------------------------
     *
     * Map the generator commands to their corresponding view files.
     *
     * @var array
     */
    public array $views = [
        'scaffolding' => 'Generators\scaffolding_view',
        'controller'  => 'Generators\controller_view',
        'model'       => 'Generators\model_view',
        'migration'   => 'Generators\migration_view',
        'seed'        => 'Generators\seed_view',
        'entity'      => 'Generators\entity_view',
    ];
}
