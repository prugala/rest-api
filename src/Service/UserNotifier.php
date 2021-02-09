<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserNotifier
{
    public function __construct(private NotifierInterface $notifier, private TranslatorInterface $translator) {}

    public function register(User $user): void
    {
        $notification = (new Notification($this->translator->trans('user.register.email.title'), ['email']))
            ->content($this->translator->trans('user.register.email.content', [
                '%token%' => $user->getToken(),
                '%id%' => $user->getId()->toString(),
            ]))
            ->importance(Notification::IMPORTANCE_MEDIUM);;

        $recipient = new Recipient($user->getEmail());

        $this->notifier->send($notification, $recipient);
    }
}
