<?php

namespace App\Security\Voter;

use App\Entity\Apiary;
use App\Entity\Apiculteur;
use LogicException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class ApiaryVoter extends Voter
{
    public const OWN = 'own';
    public const BELONG = 'belong';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::OWN, self::BELONG])
            && $subject instanceof Apiary;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            $vote?->addReason('The user must be logged in to access this resource.');

            return false;
        }

        /** @var Apiary $apiary */
        $apiary = $subject;
        return match ($attribute) {
            self::OWN => $this->own($apiary, $user),
            self::BELONG => $this->belong($apiary, $user),
            default => throw new LogicException('This code should not be reached !')
        };
    }

    private function own(Apiary $apiary, Apiculteur $user): bool
    {
        return $user === $apiary->getOwner();
    }

    private function belong(Apiary $apiary, Apiculteur $user): bool
    {
        return $user === $apiary->getOwner();
    }
}
