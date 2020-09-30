<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Notification;


use Enhavo\Bundle\BackupBundle\Notification\Type\NotificationType;
use Enhavo\Component\Type\AbstractType;

class AbstractNotificationType extends AbstractType implements NotificationTypeInterface
{
    /** @var NotificationTypeInterface */
    protected $parent;

    public function notify(string $message, $resource, array $options = []): void
    {
        $this->parent->notify($resource, $options);
    }

    public static function getParentType(): ?string
    {
        return NotificationType::class;
    }
}
