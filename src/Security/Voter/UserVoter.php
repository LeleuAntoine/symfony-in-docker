<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const COMMENT_POST = 'COMMENT_POST';

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::COMMENT_POST]) && $subject instanceof User;
    }

    /**
     * @param string $attribute
     * @param User $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token):bool
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::COMMENT_POST:
                return true;
            default:
                return false;
        }
    }
}
