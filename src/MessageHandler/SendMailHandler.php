<?php

namespace App\MessageHandler;

use App\Message\SendMail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class SendMailHandler implements MessageHandlerInterface
{
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function __invoke(SendMail $sendMail)
    {
        $mail = (new Email())
            ->From($sendMail->getEmail())
            ->to('exemple@email.fr')
            ->text($sendMail->getMessage());

        dd($mail);
    }
}