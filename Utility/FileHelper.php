<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Utility;


use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

class FileHelper
{
    /** @var Filesystem */
    private $fileSystem;

    /** @var Finder */
    private $finder;

    /** @var KernelInterface */
    private $appKernel;

    /** @var string */
    private $tmpDir;

    /**
     * FileHelper constructor.
     * @param Filesystem $fileSystem
     * @param Finder $finder
     * @param KernelInterface $appKernel
     * @param string $tmpDir
     */
    public function __construct(Filesystem $fileSystem, KernelInterface $appKernel, string $tmpDir)
    {
        $this->fileSystem = $fileSystem;
        $this->appKernel = $appKernel;
        $this->tmpDir = $tmpDir;
        $this->finder = new Finder();
    }

    public function normalizeDirectory(string $dir): string
    {
        if (substr($dir, 0, 1) !== '/') {
            $dir = $this->appKernel->getProjectDir() . '/' . $dir;
        }

        return $dir;
    }

    public function mkdir($dir): string
    {
        $this->fileSystem->mkdir($dir);

        return $dir;
    }

    public function mkTmpDir($dir): string
    {
        $this->mkdir($this->normalizeDirectory($this->tmpDir));
        return $this->mkdir($this->normalizeDirectory($this->tmpDir) . '/' . $dir);
    }

    public function rmdir($dir): void
    {
        if ($this->fileSystem->exists($dir)) {
            $this->fileSystem->remove($dir);
        }
    }

    public function listRecursive($path): \RecursiveIteratorIterator
    {
        return new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS));
    }

    public function listFiles($path): \FilesystemIterator
    {
        return new \FilesystemIterator($path, \FilesystemIterator::SKIP_DOTS);
    }

    public function remove($files): void
    {
        $this->fileSystem->remove($files);
    }
    
    public function copy($from, $to): void
    {
        $this->fileSystem->copy($from, $to);
    }
}
