<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Command;


use Enhavo\Bundle\BackupBundle\Backup\BackupManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BackupCommand extends Command
{
    use LockableTrait;

    /** @var BackupManager */
    private $backupManager;

    /**
     * BackupCommand constructor.
     * @param BackupManager $backupManager
     */
    public function __construct(BackupManager $backupManager)
    {
        parent::__construct();
        $this->backupManager = $backupManager;
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('enhavo:backup')
            ->setDescription('Execute given backup strategy or clean storages')
            ->addArgument('backup', InputArgument::REQUIRED, 'Name of the backup to execute')
            ->addArgument('operation', InputArgument::OPTIONAL, '', 'backup')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln('Skip backup.. Command is already running in another process.');
            return 1;
        }

        try {
            $backup = $input->getArgument('backup');
            $operation = $input->getArgument('operation');
            if ($operation === 'cleanup') {
                $this->backupManager->cleanup($backup);

            } else {
                $this->backupManager->backup($backup);
            }
        } catch (\Exception $exception) {
            $output->writeln($exception->getMessage());
            return 1;
        }

        $this->release();

        return 0;
    }
}
