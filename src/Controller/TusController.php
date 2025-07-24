<?php

declare(strict_types=1);

namespace Terminal42\TusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use TusPhp\Cache\Cacheable;
use TusPhp\Tus\Server;

#[Route('/_terminal42_tus/{key}/', name: 'terminal42_tus_post')]
#[Route('/_terminal42_tus/{key}/{token?}', name: 'terminal42_tus', requirements: ['token' => '.+'])]
class TusController extends AbstractController
{
    public function __construct(
        #[Autowire('@terminal42_tus.cache')] private readonly Cacheable $cacheAdapter,
        private readonly Filesystem $filesystem,
        #[Autowire(param: 'terminal42_tus.upload_dir')] private readonly string $uploadDir,
    ) {
    }

    public function __invoke(string $key): Response
    {
        $uploadDir = Path::join($this->uploadDir, $key);

        if (!$this->filesystem->exists($uploadDir)) {
            throw new NotFoundHttpException();
        }

        $server = new Server($this->cacheAdapter);
        $server->setApiPath($this->generateUrl('terminal42_tus', ['key' => $key]));
        $server->setUploadDir($uploadDir);

        return $server->serve();
    }
}
