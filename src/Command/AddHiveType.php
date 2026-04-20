<?php

namespace App\Command;

use App\Entity\HiveKind;
use App\Repository\HiveKindRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:add-hive-type', description: 'Add default type for hives', help: 'This command add kind of hives')]
class AddHiveType
{
    public function __construct(private HiveKindRepository $repo, private EntityManagerInterface $em) {}

    public function __invoke(OutputInterface $output): int
    {
        $output->writeln("This will populate the hive types with standard values. If datas already there nothing will be done");
        $kinds = $this->repo->findAll();
        if(!empty($kinds)){
            $output->writeln('Some datas are presents, abort. Add others manually');
            return Command::SUCCESS;
        }
        $defaultKinds = [
            'Autre' => 'autre',
            'Dadant' => 'dadant',
            'Ruchette' => 'ruchette',
            'Warré' => 'warre',
            'Langstroth' => 'langstroth'
        ];
        $storePath = 'admin' . DIRECTORY_SEPARATOR . 'hives' . DIRECTORY_SEPARATOR;
        foreach($defaultKinds as $name =>$picture) {
            $hiveKind = new HiveKind();
            $hiveKind->setName($name);
            $path = $storePath . $picture;
            $hiveKind->setPicture($path);
            $this->em->persist($hiveKind);
        }
        $this->em->flush();
        $output->writeln('Datas added successfully');
        return Command::SUCCESS;
    }
}