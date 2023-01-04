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
namespace Mine\Command;

use Hyperf\Command\Annotation\Command;
use Mine\Helper\Str;
use Mine\MineCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class JwtCommand
 * @package System\Command
 */
#[Command]
class JwtCommand extends MineCommand
{
    /**
     * 生成JWT密钥命令
     * @var string|null
     */
    protected ?string $name = 'mine:jwt-gen';

    public function configure()
    {
        parent::configure();
        $this->setHelp('run "php bin/hyperf.php mine:gen-jwt" create the new jwt secret');
        $this->setDescription('MineAdmin system gen jwt command');
    }

    /**
     * @throws \Throwable
     */
    public function handle()
    {
        $jwtSecret = Str::upper($this->input->getOption('jwtSecret'));

        if (empty($jwtSecret)) {
            $this->line('Missing parameter <--jwtSecret < jwt secret name>>', 'error');
        }

        $envPath = BASE_PATH . '/.env';

        if (! file_exists($envPath)) {
            $this->line('.env file not is exists!', 'error');
        }

        $key = base64_encode(random_bytes(64));

        if (Str::contains(file_get_contents($envPath), $jwtSecret) === false) {
            file_put_contents($envPath, "\n{$jwtSecret}={$key}\n", FILE_APPEND);
        } else {
            file_put_contents($envPath, preg_replace(
                "~{$jwtSecret}\s*=\s*[^\n]*~",
                "{$jwtSecret}=\"{$key}\"",
                file_get_contents($envPath)
            ));
        }

        $this->info('jwt secret generator successfully:' . $key);

    }

    protected function getOptions(): array
    {
        return [
            ['jwtSecret', '', InputOption::VALUE_REQUIRED, 'Please enter the jwtSecret to be generated'],
        ];
    }


}