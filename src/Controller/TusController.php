<?php

declare(strict_types=1);

namespace Terminal42\TusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\Routing\Attribute\Route;
use TusPhp\Cache\Cacheable;
use TusPhp\Tus\Server;

#[Route('/_terminal42_tus/{token?}', name: 'terminal42_tus', requirements: ['token' => '.+'])]
class TusController extends AbstractController
{
    public function __construct(
        private readonly UriSigner $uriSigner,
        #[Autowire('@terminal42_tus.cache')] private readonly Cacheable $cacheAdapter,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[Autowire(param: 'terminal42_tus.upload_dir')] private readonly string $uploadDir,
    ) {
    }

    public function __invoke(Request $request, string|null $token = null): Response
    {
        if (!$token && !$this->uriSigner->checkRequest($request)) {
            return new Response('URI must be signed.', Response::HTTP_FORBIDDEN);
        }

        if ($request->query->getInt('expires', PHP_INT_MAX) < time()) {
            return new Response('URI has expired.', Response::HTTP_FORBIDDEN);
        }

        $server = new Server($this->cacheAdapter);
        $server->setDispatcher($this->eventDispatcher);
        $server->setApiPath($this->generateUrl('terminal42_tus'));
        $server->setUploadDir($this->uploadDir);

        return $server->serve();
    }
}
