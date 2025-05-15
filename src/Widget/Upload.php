<?php

declare(strict_types=1);

namespace Terminal42\TusBundle\Widget;

use Contao\System;
use Contao\UploadableWidgetInterface;
use Contao\Widget;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;

class Upload extends Widget implements UploadableWidgetInterface
{
    /**
     * @var bool
     */
    protected $blnSubmitInput = true;

    /**
     * @var string
     */
    protected $strTemplate = 'be_widget';

    public function generate()
    {
        $container = System::getContainer();

        $GLOBALS['TL_JAVASCRIPT'][] = $container->get('assets.packages')->getUrl('tus.js', 'terminal42_tus');
        $GLOBALS['TL_CSS'][] = $container->get('assets.packages')->getUrl('tus.css', 'terminal42_tus');

        $key = (string) Uuid::v1();

        (new Filesystem())->mkdir(Path::join(System::getContainer()->getParameter('terminal42_tus.upload_dir'), $key));

        return $container->get('twig')->render('@Contao/be_tus.html.twig', [
            'name' => $this->name,
            'key' => $key,
            'endpoint' => $container->get('router')->generate('tus_post', ['key' => $key], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
    }

    protected function validator($varInput)
    {
        return $varInput;
    }
}
