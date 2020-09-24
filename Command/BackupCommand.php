<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Command;


use Enhavo\Bundle\BackupBundle\Backup\BackupManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BackupCommand extends Command
{
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
            ->addArgument('strategy', InputArgument::REQUIRED, 'Name of the backup strategy to execute')
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
        $strategy = $input->getArgument('strategy');
        $operation = $input->getArgument('operation');
        if ($operation === 'cleanup') {
            $this->backupManager->cleanup($strategy);

        } else {
            $this->backupManager->backup($strategy);
        }

    }
}
