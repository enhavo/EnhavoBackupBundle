<?php
/*
 * @author blutze-media
 * @since 2020-09-28
 */

namespace Enhavo\Bundle\BackupBundle\Notification;


use Enhavo\Component\Type\AbstractContainerType;

class Notification extends AbstractContainerType
{
    /** @var NotificationTypeInterface */
    protected $type;

    public function notify(string $message, $resource)
    {
        $this->type->notify($message, $resource, $this->options);
    }
}
