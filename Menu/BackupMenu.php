<?php
/*
 * @author blutze-media
 * @since 2020-09-25
 */

namespace Enhavo\Bundle\BackupBundle\Menu;

use Enhavo\Bundle\AppBundle\Menu\Menu\BaseMenu;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BackupMenu extends BaseMenu
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'icon' => 'backup',
            'label' => 'backup.label.backup',
            'translation_domain' => 'EnhavoBackupBundle',
            'route' => 'enhavo_backup_backup_index',
            'role' => 'ROLE_ENHAVO_BACKUP_BACKUP_INDEX'
        ]);
    }

    public function getType()
    {
        return 'backup_backup';
    }
}
