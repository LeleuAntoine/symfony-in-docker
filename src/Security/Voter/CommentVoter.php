<?php

namespace App\Security\Voter;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    public const COMMENT_EDIT = 'COMMENT_EDIT';
    public const COMMENT_DELETE = 'COMMENT_DELETE';

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::COMMENT_EDIT, self::COMMENT_DELETE]) && $subject instanceof Comment;
    }

    /**
     * @param string $attribute
     * @param Comment $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }
        switch ($attribute) {
            case self::COMMENT_EDIT:
            case self::COMMENT_DELETE:
                return $subject->getUser()->isEqualTo($user);
            default:
                return false;
        }
    }
}
