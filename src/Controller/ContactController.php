<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use App\Message\SendMail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request, SendMail $mailer): Response
    {
        $contact = new ContactDTO();

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mailer->sendContactEmail($contact);

            $this->addFlash('success', 'Votre email a bien été envoyé !');

            return $this->redirectToRoute('contact');
        }

        return $this->render('Contact/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
