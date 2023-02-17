<?php

namespace App\Security;

use App\Entity\News;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class NewsVoter extends Voter
{
    const VIEW = 'view';
    const DELETE = 'delete';

    protected function supports(string $attribute, $subject): bool
    {
        return $subject instanceof News && in_array($attribute, [self::VIEW, self::DELETE]);
    }

    public function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_MODERATOR', $user->getRoles())) {
            return true;
        }

        return false;
    }
}
