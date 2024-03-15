<?php namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\CLI\Commands;
use Psr\Log\LoggerInterface;
use DirectoryIterator;

/**
 * Create a Module in Modular structure
 *
 * @package App\Commands
 * @author XPerez <>
 */
class ModuleCreate extends BaseCommand
{
    /**
     * Group
     *
     * @var string
     */
    protected $group       = 'Development';

    /**
     * Command's name
     *
     * @var string
     */
    protected $name        = 'module:create';

    /**
     * Command description
     *
     * @var string
     */
    protected $description = 'Create CodeIgniter Modules in app/Modules folder';

    /**
     * Command usage
     *
     * @var string
     */
    protected $usage        = 'module:create [ModuleName] [Options]';

    /**
     * the Command's Arguments
     *
     * @var array
     */
    protected $arguments    = [ 'ModuleName' => 'Module name to be created' ];

    /**
     * the Command's Options
     *
     * @var array
     */
    protected $options      = [
        '-f' => 'Set module folder other than app/Modules',
        '-c' => 'Create only con[F]ig, [C]ontroller, [L]ibrary, [M]odel, [V]iew, [O]ther dirs'
    ];

    /**
     * Module Name to be Created
     */
    protected $module_name;

    /**
     * Module folder
     */
    protected $module_folder;

    /**
     * View folder
     */
    protected $view_folder;

    public function __construct(LoggerInterface $logger, Commands $commands)
    {
        parent::__construct($logger, $commands);
    }

    /**
     * Run route:update CLI
     */
    public function run(array $params)
    {
        helper('inflector');

        if(!isset($params[0]))
        {
            if(is_cli()){
				CLI::error("Module name must be set!. \n\nUsage:\n".$this->usage);
            }else{
                $this->session->setFlashdata('error', "Module name must be set!. \n\nUsage:\n".$this->usage);
            }
			return;
        }

        $this->module_name = $params[0];

        if(strlen(preg_replace('/[^A-Za-z0-9]+/','',$this->module_name)) <> mb_strlen($this->module_name))
        {
            if(is_cli()) {
                CLI::error("Module name must to be plain ascii characters A-Z or a-z, and can contain numbers 0-9");
            }else{
                $this->session->setFlashdata('error', "Module name must to be plain ascii characters A-Z or a-z, and can contain numbers 0-9");
            }
            return;
        }

        $this->module_name = ucfirst($this->module_name);

        $module_folder = $params['f'] ?? CLI::getOption('f');

        if ($module_folder) {
            if (!is_dir($module_folder)) {
                if(is_cli()) {
                    CLI::error("Module folder does not exist!");
                }else{
                    $this->session->setFlashdata('error', "Module folder does not exist!");
                }
                return;
            }
            $this->module_folder = realpath($module_folder);
        } else {
            $this->module_folder = APPPATH . 'Modules';
        }

        $this->view_folder = APPPATH . 'Views';

        try {
            $c = $params['c'] ?? CLI::getOption('c');

            if ($c == '' || strstr($c,'F')) {
                $this->createConfig();
            }
            if ($c == '' || strstr($c,'C')) {
                $this->createController();
            }
            if ($c == '' || strstr($c,'L'))  {
                $this->createLibrary();
            }
            if ($c == '' || strstr($c,'M')) {
                $this->createModel();
            }
            if ($c == '' || strstr($c,'V')) {
                $this->createView();
            }
            if ($c == '' || strstr($c,'O')) {
                //$this->createOtherDirs();
            }
            //$this->updateAutoload();

            if(is_cli()) {
                CLI::write('Module created!');
                CLI::write('Try to browse to http://localhost/' . strtolower($this->module_name));
            }else {
                $this->session->setFlashdata('message', "Module created!");
            }
        }
        catch (\Exception $e)
        {
            if(is_cli()) {
                CLI::error($e);
            }else{
                $this->session->setFlashdata('error', $e);
            }
        }
    }

    // ... rest of the code ...
}
