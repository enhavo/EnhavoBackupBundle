<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Notification\Type;


use Enhavo\Bundle\AppBundle\Mail\MailManager;
use Enhavo\Bundle\AppBundle\Template\TemplateManager;
use Enhavo\Bundle\BackupBundle\Notification\AbstractNotificationType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailNotificationType extends AbstractNotificationType
{
    /** @var MailManager */
    private $mailManager;

    /** @var TemplateManager */
    private $templateManager;

    /**
     * EmailNotificationType constructor.
     * @param MailManager $mailManager
     * @param TemplateManager $templateManager
     */
    public function __construct(MailManager $mailManager, TemplateManager $templateManager)
    {
        $this->mailManager = $mailManager;
        $this->templateManager = $templateManager;
    }


    public function notify(string $message, $resource, array $options = []): void
    {
        $message = $this->mailManager->createMessage(
            $options['from'], $options['name'], $options['to'], $options['subject'],
            $this->templateManager->getTemplate($options['template']), [
                'resource' => $resource,
                'message' => $message,
            ], [], $options['content_type']
        );
        $this->mailManager->sendMessage($message);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'name' => 'enhavo',
            'subject' => 'Backup Notification for "{{ resource.name }}"',
            'template' => 'EnhavoBackupBundle:mail/notification:email.txt.twig',
            'content_type' => MailManager::CONTENT_TYPE_PLAIN,
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
