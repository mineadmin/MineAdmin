<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

declare(strict_types=1);
namespace Mine\Command\Migrate;

use Hyperf\Command\Annotation\Command;
use Hyperf\Database\Commands\Migrations\TableGuesser;
use Hyperf\Database\Commands\Seeders\BaseCommand;
use Hyperf\Utils\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Throwable;

/**
 * Class MineMigrate
 * @package System\Command\Migrate
 */
#[Command]
class MineMigrate extends BaseCommand
{

    protected $module;
    /**
     * The migration creator instance.
     *
     * @var MineMigrationCreator
     */
    protected $creator;

    /**
     * Create a new migration install command instance.
     * @param MineMigrationCreator $creator
     */
    public function __construct(MineMigrationCreator $creator)
    {

        parent::__construct('mine:migrate-gen');
        $this->setDescription('Generate a new MineAdmin module migration file');
        $this->creator = $creator;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // It's possible for the developer to specify the tables to modify in this
        // schema operation. The developer may also specify if this table needs
        // to be freshly created so we can create the appropriate migrations.
        $name = 'create_' . Str::snake(trim($this->input->getArgument('name'))).'_table';

        $this->module = $this->input->getOption('module');

        if (empty($this->module)) {
            $this->error("<--module module_name> required");
            exit;
        }

        $this->module = ucfirst($this->module);

        $table = $this->input->getOption('table');

        $create = $this->input->getOption('create') ?: false;

        // If no table was given as an option but a create option is given then we
        // will use the "create" option as the table name. This allows the devs
        // to pass a table name into this option as a short-cut for creating.
        if (! $table && is_string($create)) {
            $table = $create;

            $create = true;
        }

        // Next, we will attempt to guess the table name if this the migration has
        // "create" in the name. This will allow us to provide a convenient way
        // of creating migrations that create new tables for the application.
        if (! $table) {
            [$table, $create] = TableGuesser::guess($name);
        }

        // Now we are ready to write the migration out to disk. Once we've written
        // the migration out, we will dump-autoload for the entire framework to
        // make sure that the migrations are registered by the class loaders.
        $this->writeMigration($name, $table, $create);
    }

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the migration'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['module', '-M', InputOption::VALUE_REQUIRED, 'Please enter the module to be generated'],
            ['create', null, InputOption::VALUE_OPTIONAL, 'The table to be created'],
            ['table', null, InputOption::VALUE_OPTIONAL, 'The table to migrate'],
            ['path', null, InputOption::VALUE_OPTIONAL, 'The location where the migration file should be created'],
            ['realpath', null, InputOption::VALUE_NONE, 'Indicate any provided migration file paths are pre-resolved absolute paths'],
        ];
    }

    /**
     * Write the migration file to disk.
     * @param string $name
     * @param string|null $table
     * @param bool $create
     */
    protected function writeMigration(string $name, ?string $table, bool $create): void
    {
        try {
            $file = pathinfo($this->creator->create(
                $name,
                $this->getMigrationPath(),
                $table,
                $create
            ), PATHINFO_FILENAME);
            $this->info("<info>[INFO] Created Migration:</info> {$file}");
        } catch (Throwable $e) {
            $this->error("<error>[ERROR] Created Migration:</error> {$e->getMessage()}");
        }
    }

    /**
     * Get migration path (either specified by '--path' option or default location).
     *
     * @return string
     */
    protected function getMigrationPath(): string
    {
        return BASE_PATH . '/app/' . ucfirst($this->module) . '/Database/Migrations';
    }

    /**
     * Determine if the given path(s) are pre-resolved "real" paths.
     */
    protected function usingRealPath(): bool
    {
        return $this->input->hasOption('realpath') && $this->input->getOption('realpath');
    }
}
