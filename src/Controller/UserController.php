<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/subscription", name="subscription")
     */
    public function subscription(Request $request, UserManager $userManager): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $save = $userManager->save($user);

            if (!$save){
                $this->addFlash('danger', 'Veuillez renseigner votre moyen de paiement');

                return $this->redirectToRoute('subscription');
            }

            $this->addFlash('success', 'Votre compte a bien été créé !');

            return $this->redirectToRoute('home');
        }

        return $this->render('user/create_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
