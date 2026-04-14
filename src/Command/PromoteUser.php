<?php

namespace App\Command;

use App\Repository\ApiculteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;


#[AsCommand(name: 'app:promote-user', description: 'Promotes a user to admin', help: 'This command add user define by login to admin group')]
class PromoteUser
{
    public function __construct(private ApiculteurRepository $repo, private EntityManagerInterface $em)
    {
    
    }


    public function __invoke(#[Argument('The username of the user.')] string $login, SymfonyStyle $io): int
    {
        $user = $this->repo->findOneBy(['login' => $login]);
        if(!$user) {
            $io->error("User {$login} does not exist");
            return Command::FAILURE;
        }
        $user->setRoles(['ROLE_ADMIN']);
        $this->em->flush();
        $io->success("User {$login} has been promoted to admin");
        return Command::SUCCESS;
    }
}