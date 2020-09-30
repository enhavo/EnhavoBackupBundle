<?php
/*
 * @author blutze-media
 * @since 2020-09-25
 */

namespace Enhavo\Bundle\BackupBundle\Form\Type;

use Enhavo\Bundle\BackupBundle\Entity\Backup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BackupType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Backup::class
        ));
    }

    public function getBlockPrefix()
    {
        return 'backup_backup';
    }
}
