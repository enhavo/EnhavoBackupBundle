<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Source\Type;

use Enhavo\Bundle\BackupBundle\Source\AbstractSourceType;
use Enhavo\Bundle\BackupBundle\Source\SourceCollection;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ifsnop\Mysqldump\Mysqldump;

class MysqldumpPHPSourceType extends AbstractSourceType
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

        if (!$dbPort) {
            $dbPort = 3306;
        }

        $dump = new Mysqldump(sprintf('mysql:host=%s;dbname=%s;port=%s)', $dbHost, $dbName, $dbPort), $dbUser, $dbPassword);
        $dump->start($target);
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
        return 'mysqldump_php';
    }
}
