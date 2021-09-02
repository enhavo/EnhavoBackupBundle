<?php

namespace Enhavo\Bundle\BackupBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class BackupController extends AbstractController
{
    /** @var string */
    private $apiKey;

    /**
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }


    public function create(KernelInterface $kernel, Request $request): Response
    {
        if ($request->get('apiKey') !== $this->apiKey) {
            throw $this->createAccessDeniedException('Incorrect API Key');
        }

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'enhavo:backup',
            'backup' => $request->get('backup', 'default'),
        ]);

        $output = new BufferedOutput();
        $application->run($input, $output);

        $content = $output->fetch();

        return new Response($content, 200, [
            'content-type' => 'text/plain'
        ]);
    }
}
