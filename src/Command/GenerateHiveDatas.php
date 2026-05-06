<?php

namespace App\Command;

use App\Entity\Hive;
use App\Tool\PathMaker;
use App\Repository\HiveRepository;
use Editiel98\FileWriter\FileWriter;
use App\Exception\DataloggerFileException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(name: 'app:generate-datas', description: 'generate a logger file for the hive already configured', help: 'This command generate a logger file and config for the hive that has already a datalogger')]
class GenerateHiveDatas
{
    private SymfonyStyle $io;

    public function __construct(
        private HiveRepository $repo,
        #[Autowire('%kernel.project_dir%')] private string $projectDir,
        private readonly string $userFolderRoot,
    ) {}

    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $hive = $this->selectHive();
        if (null === $hive) {
            return Command::SUCCESS;
        }
        $this->io->section('Étape 1 — Chargement de la configuration');
        $infos = $this->generateConfig($hive);
        $config = $infos['config'];
        $template = $infos['template'];
        $this->io->section('Étape 2 — Création des fichiers de mesure');
        $folder = $this->io->ask("Dans quel répertoire du projet voulez vous stocker les fichiers ({$this->projectDir}) ?", 'datas');
        $nbSamples = 0;
        while ($nbSamples < 1) {
            $nbSamples = (int) $this->io->ask('Entrez le nombre de mesures simulées');
        }
        $samples = $this->generateDatas($template, $nbSamples, $hive); 
        $header = [
            'version' => $config['version'],
            'signature' => $config['signature']
        ];
        $this->writeSamples($samples, $header, $folder, $hive);
        return Command::SUCCESS;
    }

    private function selectHive(): ?Hive
    {
        $hives = $this->repo->findWithDatalogger();
        $this->io->table(['ID', 'NOM', 'IDENTIFICATION'], array_map(fn(Hive $h) => [
            $h->getId(),
            $h->getName(),
            $h->getIdentification()
        ], $hives));
        $hive = null;
        while (null === $hive) {
            $id = (int) $this->io->ask('Entrez le numéro de la ruche');
            $hive = array_find($hives, fn(Hive $h) => $h->getId() === $id);
            if ($hive === null) {
                $this->io->error("Aucune ruche trouvée avec le numéro $id, veuillez réessayer");
            }
        }
        $result = $this->io->confirm("Vous avez selectionné la ruche {$hive->getName()}, id: {$hive->getID()}. Etes vous sur ?");
        if (!$result) {
            $this->io->caution('Arrêt du traitement');
            return null;
        }
        return $hive;
    }

    private function generateConfig(Hive $hive): array
    {
        //TODO : check if works
        $filename = $this->userFolderRoot;
        $filename .= $hive->getDataloggerName();
        if (!file_exists($filename)) {
            throw new DataloggerFileException('Config file not found');
        }
        $json = file_get_contents($filename);
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            throw new DataloggerFileException('invalid config format');
        }
        if (!array_key_exists('config', $decoded) || !array_key_exists('template', $decoded)) {
            throw new DataloggerFileException('invalid config data format');
        }
        return $decoded;
    }

    private function generateDatas(array $config, int $nbSamples, Hive $hive): array
    {
        $this->io->text('Genération des données de mesures');
        $samples = [];
        for ($i = 0; $i < $nbSamples; $i++) {
            $sample = $this->generateSample($config, $hive);
            $samples[] = $sample;
        }
        return $samples;
    }

    private function writeSamples(array $samples, array $header, string $folder, Hive $hive): void
    {
        $this->io->text('Ecriture du fichier de données de mesures');
        $path = $this->projectDir . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR;
        $basePath = PathMaker::makeDataloggerFilePath($hive->getOwner(), $hive, $path);
        $filename = $hive->getIdentification() . '.bin';
        $filePath = $basePath . $filename;
        $fileWriter = new FileWriter($filePath, $header);
        $fileWriter->writeBinaryFile($samples);
        if (file_exists($filePath)) {
            $this->io->text("Fichier de configuration créé ici : {$filePath}");
        }
    }

    private function randomDate(): string
    {
        $year = rand(2022, 2026);
        $month = rand(1, 12);
        $day = rand(1, 28);
        $hour = rand(0, 23);
        $minutes = rand(0, 59);
        $fullHour = $hour > 9 ? "{$hour}" : "0{$hour}";
        $fullMonth = $month > 9 ? "{$month}" : "0{$month}";
        $fullDay = $day > 9 ? "{$day}" : "0{$day}";
        $fullMinutes = $minutes > 9 ? "{$minutes}" : "0{$minutes}";
        $date = "{$year}-{$fullMonth}-{$fullDay} {$fullHour}:{$fullMinutes}:00";
        return $date;
    }

    private function randomFloat(?bool $positive = false): float
    {
        $min = -10;
        if ($positive) {
            $min = 0;
        }
        $intPart = rand($min, 50);
        $decimal = rand(0, 100) / 100;
        return $intPart + $decimal;
    }

    private function generateSample(array $template, Hive $hive): array
    {
        $generators =[
            'string' => fn(string $key)=>match($key) {
                'hive' => $hive->getName(),
                'hiveId' => $hive->getIdentification(),
                'time' => $this->randomDate()
                },
            'int' => fn() =>random_int(0,35),
            'float' => fn() =>$this->randomFloat()
        ];
        $sample = [];
       foreach($template as $key=>$type) {
        $sample[] = $generators[$type]($key);
       }
        return $sample;
    }
}
