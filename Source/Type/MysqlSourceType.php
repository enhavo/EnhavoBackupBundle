<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Source\Type;


use Enhavo\Bundle\BackupBundle\Source\AbstractSourceType;
use Enhavo\Bundle\BackupBundle\Source\SourceCollection;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Process\Process;

class MysqlSourceType extends AbstractSourceType
{
    public function collect(array $options): SourceCollection
    {
        $collection = new SourceCollection();
        $collection->setDate(new \DateTime());
        $tmpDir = $this->fileHelper->mkTmpDir(uniqid());
        $collection->setRoot($tmpDir);
        $collection->addCleanupFile($tmpDir);

        $target = $tmpDir . '/' . $options['output_file'];
        $this->dump($options['url'], $target);
        $collection->addFile($target);

        return $collection;
    }

    private function dump(string $url, string $target)
    {
        $dbUser = (urldecode(parse_url($url, PHP_URL_USER)));
        $dbPassword = (urldecode(parse_url($url, PHP_URL_PASS)));
        $dbName = (urldecode(substr(parse_url($url, PHP_URL_PATH), 1)));
        $dbHost = (urldecode(parse_url($url, PHP_URL_HOST)));
        $dbPort = (urldecode(parse_url($url, PHP_URL_PORT)));

        $command = ['mysqldump'];
        if ($dbUser) {
            $command[] = sprintf('-u %s', $dbUser);
        }
        if ($dbPassword) {
            $command[] = sprintf('-p%s', $dbPassword);
        }
        if ($dbHost) {
            $command[] = sprintf('-h%s', $dbHost);
        }
        if ($dbPort) {
            $command[] = sprintf('--port=%s', $dbPort);
        }
        $command[] = $dbName;
        $command[] = sprintf('> %s', $target);


        $command = implode(' ', $command);
        $process = new Process($command);
        $process->run();

        if ($process->isSuccessful()) {
            $result = $process->getOutput();
        } else {
            $result = $process->getErrorOutput();
        }

        return $result;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'output_file' => 'database.sql',
        ]);
        $resolver->setRequired([
            'url',
        ]);
    }

    public static function getName(): ?string
    {
        return 'mysql';
    }
}
