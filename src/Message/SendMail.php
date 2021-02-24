<?php

namespace App\Message;

use App\DTO\ContactDTO;

class SendMail
{
    private ContactDTO $contact;

    public function __construct(ContactDTO $contact)
    {
        $this->contact = $contact;
    }

    public function sendContactEmail(ContactDTO $contact): ContactDTO
    {
        $this->contact = $contact;
        return $this->contact;
    }
}