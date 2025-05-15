<?php

declare(strict_types=1);

namespace Terminal42\TusBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TusPhp\Cache\Cacheable;
use TusPhp\Tus\Server;

#[AsCommand('tus:expired', 'Remove expired uploads.')]
class ExpirationCommand extends Command
{
    public function __construct(
        #[Autowire('@terminal42_tus.cache')] private readonly Cacheable $cacheAdapter,
    ) {
        parent::__construct('tus:expired');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            '<info>Cleaning server resources</info>',
            '<info>=========================</info>',
            '',
        ]);

        $server = new Server($this->cacheAdapter);
        $deleted = $server->handleExpiration();

        if (empty($deleted)) {
            $output->writeln('<comment>Nothing to delete.</comment>');
        } else {
            foreach ($deleted as $key => $item) {
                $output->writeln('<comment>'.($key + 1).". Deleted {$item['name']} from ".\dirname((string) $item['file_path']).'</comment>');
            }
        }

        $output->writeln('');

        return Command::SUCCESS;
    }
}
