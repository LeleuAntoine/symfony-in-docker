<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\GameRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    private const NUMBER_HOME_MOST_POPULAR = 4;
    private const NUMBER_HOME_LAST_ADDED = 4;
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

        return $this->render('games/home.html.twig', [
            'lastGames' => $lastGames,
            'MostPopulars' => $MostPopulars,
        ]);
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

        if (!$games) {
            $this->createNotFoundException();
        }

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

        if (!$games) {
            $this->createNotFoundException();
        }

        return $this->render('games/last_game_add.html.twig', [
            'games' => $games,
        ]);
    }

    /**
     * @Route("/game/{game<[0-9]+>}", name="game")
     */
    public function gameView(Game $game): Response
    {
        return $this->render('games/game.html.twig', [
            'game' => $game,
        ]);
    }

    /**
     * @Route("/list-of-games", name="list_of_games")
     */
    public function listOfGames(GameRepository $gameRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $games = $paginator->paginate(
            $gameRepository->findAll(),
            $request->query->getInt('page', self::DEFAULT_VALUE),
            self::NUMBER_CARD_PER_PAGE
        );

        if (!$games) {
            $this->createNotFoundException();
        }

        return $this->render('games/list_of_games.html.twig', [
            'games' => $games,
        ]);
    }
}