<?php

declare(strict_types=1);

namespace Terminal42\TusBundle\Widget;

use Contao\System;
use Contao\UploadableWidgetInterface;
use Contao\Widget;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

    public function generate(): string
    {
        $container = System::getContainer();

        $GLOBALS['TL_JAVASCRIPT'][] = $container->get('assets.packages')->getUrl('tus.js', 'terminal42_tus');
        $GLOBALS['TL_CSS'][] = $container->get('assets.packages')->getUrl('tus.css', 'terminal42_tus');

        $uriSigner = $container->get('uri_signer');
        $endpoint = $container->get('router')->generate('terminal42_tus', ['expires' => strtotime('+1 hour')], UrlGeneratorInterface::ABSOLUTE_URL);

        return $container->get('twig')->render('@Contao/backend/terminal42_tus_upload.html.twig', [
            'name' => $this->name,
            'endpoint' => $uriSigner->sign($endpoint),
        ]);
    }

    protected function validator($varInput)
    {
        return $varInput;
    }
}
