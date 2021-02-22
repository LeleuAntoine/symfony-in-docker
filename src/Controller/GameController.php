<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Game;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    private const NUMBER_HOME_MOST_POPULAR = 8;
    private const NUMBER_HOME_LAST_ADDED = 8;
    private const NUMBER_MOST_POPULAR = 16;
    private const NUMBER_LAST_ADDED = 16;
    private const DEFAULT_VALUE = 1;
    private const NUMBER_CARD_PER_PAGE = 8;

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="home")
     */
    public function home(GameRepository $gameRepository): Response
    {
        $MostPopulars = $gameRepository->findGamesMostPopular(self::NUMBER_HOME_MOST_POPULAR);
        $lastGames = $gameRepository->findLastGameAdded(self::NUMBER_HOME_LAST_ADDED);

        $response = $this->render('games/home.html.twig', [
            'lastGames' => $lastGames,
            'mostPopulars' => $MostPopulars,
        ]);
        $response->setPublic();
        $response->setMaxAge(180);

        return $response;
    }

    /**
     * @Route("/most-popular", name="most_popular")
     */
    public function mostPopular(GameRepository $gameRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $games = $paginator->paginate(
            $gameRepository->findGamesMostPopular(self::NUMBER_MOST_POPULAR),
            $request->query->getInt('page', self::DEFAULT_VALUE),
            self::NUMBER_CARD_PER_PAGE
        );

        return $this->render('games/most_popular.html.twig', [
            'games' => $games,
        ]);
    }

    /**
     * @Route("/last-added", name="last_added")
     */
    public function lastAdded(GameRepository $gameRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $games = $paginator->paginate(
            $gameRepository->findLastGameAdded(self::NUMBER_LAST_ADDED),
            $request->query->getInt('page', self::DEFAULT_VALUE),
            self::NUMBER_CARD_PER_PAGE
        );

        return $this->render('games/last_game_add.html.twig', [
            'games' => $games,
        ]);
    }

    /**
     * @Route("/game/{game<[0-9]+>}", name="game")
     */
    public function gameView(Game $game, CommentRepository $commentRepository, Request $request): Response
    {
        $newComment = new Comment();
        $newComment->setGame($game);
        $comments = $commentRepository->findComments($game->getId());

        $form = $this->createForm(CommentType::class, $newComment);
        $newComment->setUser($this->getUser());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($newComment);
            $this->em->flush();

            $this->addFlash('success', 'Commentaire créé avec succès');

            return $this->redirectToRoute('game', ['game' => $game->getId()]);
        }

        return $this->render('games/game.html.twig', [
            'comments' => $comments,
            'game' => $game,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/game/{game<[0-9]+>}/update/comment{comment<[0-9]+>}", name="update_comment")
     */
    public function updateComment(Game $game, Comment $comment, CommentRepository $commentRepository, Request $request): Response
    {
        $comments = $commentRepository->findComments($game->getId());

        $form = $this->createForm(CommentType::class, $comment);
        $comment->setUser($this->getUser());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($comment);
            $this->em->flush();

            $this->addFlash('warning', 'Commentaire modifié avec succès');

            return $this->redirectToRoute('game', ['game' => $game->getId()]);
        }

        return $this->render('games/game.html.twig', [
            'comments' => $comments,
            'game' => $game,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/game/{game<[0-9]+>}/delete/comment{comment<[0-9]+>}", name="delete_comment")
     */
    public function deleteComment(Game $game, Comment $comment): Response
    {
        if ($comment->getUser() != $this->getUser()){
            $this->addFlash('danger', 'Vous n\'avez pas les droits nécessaires');

            return $this->redirectToRoute('game', ['game' => $game->getId()]);
        }
        $comment->setDeletedAt(new \DateTime('now'));
        $this->em->persist($comment);
        $this->em->flush();

        $this->addFlash('danger', 'Commentaire supprimé avec succès');

        return $this->redirectToRoute('game', ['game' => $game->getId()]);
    }

    /**
     * @Route("/list-of-games", name="list_of_games")
     */
    public function listOfGames(GameRepository $gameRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $games = $paginator->paginate(
            $gameRepository->findAllQueryBuilder(),
            $request->query->getInt('page', self::DEFAULT_VALUE),
            self::NUMBER_CARD_PER_PAGE
        );

        return $this->render('games/list_of_games.html.twig', [
            'games' => $games,
        ]);
    }
}
