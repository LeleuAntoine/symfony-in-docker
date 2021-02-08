<?php

namespace App\Controller;

use App\Form\ContactType;
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
    public function contact(Request $request, \Swift_Mailer $mailer): Response
    {

        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            $mail = (new \Swift_Message('Nouveau Contact'))
                ->setFrom($contact['email'])
                ->setTo('exemple@email.fr')
                ->setBody(
                    $this->renderView(
                        'Contact/contact_mail.html.twig', compact('contact')
                    ),
                    'text/html'
                );
            $mailer->send($mail);

            return $this->redirectToRoute('home');
        }

        return $this->render('Contact/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
