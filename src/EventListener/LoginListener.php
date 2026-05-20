<?php

namespace App\EventListener;

use App\Service\UserConnected;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: LoginSuccessEvent::class)]
class LoginListener
{
    public function __construct(private string $userFolderRoot, private UserConnected $tools) {}

    public function __invoke(LoginSuccessEvent $event)
    {
        $user = $event->getUser();
        $this->tools->cleanUp($user, $this->userFolderRoot);
    }
}
