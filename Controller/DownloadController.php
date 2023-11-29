<?php

namespace Enhavo\Bundle\BackupBundle\Controller;

use Enhavo\Bundle\BackupBundle\Backup\BackupManager;
use Enhavo\Bundle\BackupBundle\Entity\BackupFile;
use Enhavo\Bundle\BackupBundle\Utility\FileHelper;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class DownloadController extends AbstractController
{
    /** @var BackupManager */
    private $backupManager;

    /** @var RepositoryInterface */
    private $fileRepository;

    /** @var FileHelper */
    private $fileHelper;

    /**
     * DownloadController constructor.
     * @param BackupManager $backupManager
     * @param RepositoryInterface $fileRepository
     * @param FileHelper $fileHelper
     */
    public function __construct(BackupManager $backupManager, RepositoryInterface $fileRepository, FileHelper $fileHelper)
    {
        $this->backupManager = $backupManager;
        $this->fileRepository = $fileRepository;
        $this->fileHelper = $fileHelper;
    }

    public function downloadAction(Request $request)
    {
        $id = $request->get('id');
        /** @var BackupFile $backupFile */
        $backupFile = $this->fileRepository->find($id);

        return new BinaryFileResponse($this->fileHelper->normalizeDirectory($backupFile->getPath()) . '/' . $backupFile->getName(),
            200, [], true,
            ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }

    public function create(Request $request): Response
    {
        $output = $this->backupManager->backup('default');
        return new Response();
    }
}
