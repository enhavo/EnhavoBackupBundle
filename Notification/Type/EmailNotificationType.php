<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Notification\Type;


use Enhavo\Bundle\AppBundle\Mailer\MailerManager;
use Enhavo\Bundle\AppBundle\Mailer\Message;
use Enhavo\Bundle\AppBundle\Template\TemplateManager;
use Enhavo\Bundle\BackupBundle\Notification\AbstractNotificationType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailNotificationType extends AbstractNotificationType
{
    /** @var MailerManager */
    private $mailerManager;

    /** @var TemplateManager */
    private $templateManager;

    /**
     * EmailNotificationType constructor.
     * @param MailerManager $mailerManager
     * @param TemplateManager $templateManager
     */
    public function __construct(MailerManager $mailerManager, TemplateManager $templateManager)
    {
        $this->mailerManager = $mailerManager;
        $this->templateManager = $templateManager;
    }


    public function notify(string $message, $resource, array $options = []): void
    {
        $message = $this->mailerManager->createMessage();
        $message->setFrom($options['from']);
        $message->setSenderName($options['name']);
        $message->setTo($options['to']);
        $message->setSubject($options['subject']);
        $message->setTemplate($options['template']);
        $message->setContentType($options['content_type']);
        $message->setContext([
            'resource' => $resource,
            'message' => $message,
        ]);
        $this->mailerManager->sendMessage($message);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'name' => 'enhavo',
            'subject' => 'Backup Notification for "{{ resource.name }}"',
            'template' => 'EnhavoBackupBundle:mail/notification:email.txt.twig',
            'content_type' => Message::CONTENT_TYPE_PLAIN,
        ]);
        $resolver->setRequired([
            'from',
            'to',
        ]);
    }

    public static function getName(): ?string
    {
        return 'email';
    }
}
