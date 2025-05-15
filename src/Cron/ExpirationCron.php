<?php

declare(strict_types=1);

namespace Terminal42\TusBundle\Cron;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TusPhp\Cache\Cacheable;
use TusPhp\Tus\Server;

// CronJob tag is added by bundle extension
class ExpirationCron
{
    public function __construct(
        #[Autowire('@terminal42_tus.cache')] private readonly Cacheable $cacheAdapter,
    ) {
    }

    public function __invoke(): void
    {
        $server = new Server($this->cacheAdapter);
        $server->handleExpiration();
    }
}
