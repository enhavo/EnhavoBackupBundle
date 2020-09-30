<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Notification\Type;


use Enhavo\Bundle\BackupBundle\Notification\NotificationTypeInterface;
use Enhavo\Component\Type\AbstractType;

class NotificationType extends AbstractType implements NotificationTypeInterface
{
    public function notify(string $message, $resource, array $options = []): void
    {
    }

    public static function getName(): ?string
    {
        return 'notification';
    }
}
