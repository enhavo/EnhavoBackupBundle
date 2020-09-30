<?php
/*
 * @author blutze-media
 * @since 2020-09-28
 */

namespace Enhavo\Bundle\BackupBundle\Notification;


interface NotificationTypeInterface
{

    public function notify(string $message, $resource, array $options = []): void;

}
