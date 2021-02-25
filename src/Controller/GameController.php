<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Game;
use App\Form\CommentType;
use App\Manager\CommentManager;
use App\Repository\GameRepository;
use App\Security\Voter\CommentVoter;
use App\Security\Voter\UserVoter;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
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

    /**
     * @Route("/game/{game<[0-9]+>}", name="game")
     * @Entity("game", expr="repository.findWithCommentsAndUsers(game)")
     */
    public function gameView(Game $game, CommentManager $commentManager, Request $request): Response
    {
        $user = $this->getUser();

        $newComment = new Comment();
        $newComment->setGame($game);

        $form = $this->createForm(CommentType::class, $newComment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted(UserVoter::COMMENT_POST, $user);

            $newComment->setUser($this->getUser());

            $commentManager->save($newComment);

            $this->addFlash('success', 'Commentaire créé avec succès');

            return $this->redirectToRoute('game', ['game' => $game->getId()]);
        }

        return $this->render('games/game.html.twig', [
            'game' => $game,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/game/{game<[0-9]+>}/comment/{comment<[0-9]+>}/update", name="update_comment")
     * @Entity("game", expr="repository.findWithCommentsAndUsers(game)")
     */
    public function updateComment(Game $game, Comment $comment, CommentManager $commentManager, Request $request): Response
    {
        $this->denyAccessUnlessGranted(CommentVoter::COMMENT_EDIT, $comment);

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->getUser());

            $commentManager->save($comment);

            $this->addFlash('warning', 'Commentaire modifié avec succès');

            return $this->redirectToRoute('game', ['game' => $game->getId()]);
        }

        return $this->render('games/game.html.twig', [
            'game' => $game,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/game/{game<[0-9]+>}/delete/comment{comment<[0-9]+>}", name="delete_comment")
     */
    public function deleteComment(Game $game, Comment $comment, CommentManager $commentManager): Response
    {
        $this->denyAccessUnlessGranted(CommentVoter::COMMENT_DELETE, $comment);

        $commentManager->delete($comment);

        $this->addFlash('danger', 'Commentaire supprimé avec succès');

        return $this->redirectToRoute('game', ['game' => $game->getId()]);
    }
}
