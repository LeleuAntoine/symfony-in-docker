<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/subscription", name="subscription")
     */
    public function subscription(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder,
                                 UserRepository $userRepository, Request $request): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $usersEmail = $userRepository->findOneBy(['email' => $user->getEmail()]);

            if ($usersEmail){
                $this->addFlash('danger', 'Email déjà enregistré !');
                return $this->redirectToRoute('subscription');
            }

            if (method_exists($user, 'setPassword') && $user->getPlainPassword()) {
                $encodedPassword = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($encodedPassword);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a bien été créé !');

            return $this->redirectToRoute('home');
        }

        return $this->render('user/create_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
