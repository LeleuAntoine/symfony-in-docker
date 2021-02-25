<?php

namespace App\Command;

use App\Repository\CommentRepository;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HardDeleteCommand extends Command
{
    private $em;
    private $commentRepository;
    private $gameRepository;
    private $userRepository;

    protected function configure()
    {
        $this
            ->setName('app:update-softDelete')
            ->setDescription('Update soft delete to hard delete');
    }

    public function __construct(EntityManagerInterface $em, CommentRepository $commentRepository,
                                GameRepository $gameRepository, UserRepository $userRepository)
    {
        $this->commentRepository = $commentRepository;
        $this->gameRepository = $gameRepository;
        $this->userRepository = $userRepository;
        $this->em = $em;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->em->getFilters()->disable('softdeleteable');

        $comments = $this->commentRepository->findCommentsDeleteAt();
        $users = $this->userRepository->findUsersDeleteAt();
        $games = $this->gameRepository->findGamesDeleteAt();

        foreach ($users as $user) {
            foreach ($user->getComments() as $comment) {
                $user->removeComment($comment);
            }
            $card = $user->getCard();
            $card->setDeletedAt(new \DateTime('now'));
            $output->writeln('-> User '.$user->getEmail().' game has been deleted');

            $this->em->remove($card);
            $this->em->remove($user);
        }

        foreach ($comments as $comment) {
            $output->writeln('-> The '.$comment->getTitle().' comment has been deleted');
            $this->em->remove($comment);
        }

        foreach ($games as $game) {
            $output->writeln('-> The '.$game->getName().' game has been deleted');
            $this->em->remove($game);
        }

        $this->em->flush();

        $output->writeln('Commande réussie');

        return Command::SUCCESS;
    }
}
