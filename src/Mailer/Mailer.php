<?php

namespace App\Mailer;

use App\DTO\ContactDTO;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class Mailer
{
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendContactEmail(ContactDTO $contact): void
    {
        $mail = (new Email())
            ->From($contact->getEmail())
            ->to('exemple@email.fr')
            ->text($contact->getMessage());

        $this->mailer->send($mail);
    }
}
