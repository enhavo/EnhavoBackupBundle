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
        $mailerMessage = $this->mailerManager->createMessage();
        $mailerMessage->setFrom($options['from']);
        $mailerMessage->setSenderName($options['name']);
        $mailerMessage->setTo($options['to']);
        $mailerMessage->setSubject($options['subject']);
        $mailerMessage->setTemplate($options['template']);
        $mailerMessage->setContentType($options['content_type']);
        $mailerMessage->setContext([
            'resource' => $resource,
            'message' => $message,
        ]);
        $this->mailerManager->sendMessage($mailerMessage);
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
