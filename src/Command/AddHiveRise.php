<?php

namespace App\Command;

use App\Entity\HiveRise;
use App\Repository\HiveRiseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:add-hive-rise', description: 'Add default rises for hives', help: 'This command add rises for hives')]
class AddHiveRise
{
    public function __construct(private HiveRiseRepository $repo, private EntityManagerInterface $em)
    {
    
    }

    public function __invoke(OutputInterface $output): int
    {
        $output->writeln("This will populate the hive rises with standard values. If datas already there nothing will be done");
        $rises = $this->repo->findAll();
        if(!empty($rises)){
            $output->writeln('Some datas are presents, abort. Add others manually');
            return Command::SUCCESS;
        }

        for($i = 1; $i < 13; $i++) {
            $rise = new HiveRise();
            $count = $i < 10 ? '0' : '';
            $plural = $i < 2 ? '' : 's';
            $name = "Hausse {$count}{$i} cadre{$plural}";
            $rise->setName($name);
            $this->em->persist($rise);
        }
        $this->em->flush();
        $output->writeln('Datas added successfully');
        return Command::SUCCESS;
    }
}