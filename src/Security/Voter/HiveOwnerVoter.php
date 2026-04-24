<?php

namespace App\Security\Voter;

use App\Entity\Hive;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class HiveOwnerVoter extends Voter
{
    public const OWN = 'own';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::OWN])
            && $subject instanceof Hive;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            $vote?->addReason('The user must be logged in to access this resource.');

            return false;
        }

         /** @var Hive $hive */
        $hive = $subject;
        $apiary = $hive->getApiary();
        $result = $user === $apiary->getBeekeeper();
        return $result;
    }
}
