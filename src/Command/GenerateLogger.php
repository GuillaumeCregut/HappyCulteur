<?php

namespace App\Command;

use App\Entity\Hive;
use App\Service\ConfigMaker;
use App\Dto\DataloggerConfigDto;
use App\Repository\HiveRepository;
use App\Tool\PathMaker;
use Editiel98\FileWriter\FileWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(name: 'app:generate-logger', description: 'generate a logger file for the hive', help: 'This command generate a logger file and config for the hive')]
class GenerateLogger
{
    private SymfonyStyle $io;

    public function __construct(
        private HiveRepository $repo,
        #[Autowire('%kernel.project_dir%')] private string $projectDir,
        private ConfigMaker $maker,
        private ParameterBagInterface $params
    ) {}

    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $hive = $this->selectHive();
        if (null === $hive) {
            return Command::SUCCESS;
        }
        $this->io->section('Étape 1 — Création de la configuration');
        $config = $this->generateConfig();
        $folder = $this->io->ask("Dans quel répertoire du projet voulez vous stocker les fichiers ({$this->projectDir}) ?", 'datas');
        $this->generateConfigFile($config, $hive, $folder);
        $this->io->section('Étape 2 — Création des fichiers de mesure');
        $nbSamples = 0;
        while ($nbSamples < 1) {
            $nbSamples = (int) $this->io->ask('Entrez le nombre de mesures simulées');
        }
        $samples = $this->generateDatas($config, $nbSamples, $hive);

        $header = [
            'version' => (int)$config['version'],
            'signature' => $config['signature']
        ];
        $this->writeSamples($samples, $header, $folder, $hive);
        return Command::SUCCESS;
    }

    private function selectHive(): ?Hive
    {
        $hives = $this->repo->findAll();
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

    private function generateConfig(): array
    {
        $this->io->text('Récolte des informations sur le datalogger');
        $tempExt = $this->io->confirm("Le système dispose t'il d'une sonde de température extérieure ?");
        $tempInt = $this->io->confirm("Le système dispose t'il d'une sonde de température intérieure ?");
        $hygroInt = $this->io->confirm("Le système dispose t'il d'une sonde d'hygrométie intérieure ?");
        $hygroExt = $this->io->confirm("Le système dispose t'il d'une sonde d'hygrométie extérieure ?");
        $weight = $this->io->confirm("Le système dispose t'il d'une sonde de poids ?");
        $freq = 0;
        while ($freq < 1 || $freq > 24) {
            $freq = (int) $this->io->ask('Entrez la fréquence de mesure (entre 1 et 24)');
        }
        $this->io->text('Création du dto');
        $version = $this->params->get('app.datalogger.version');
        $signature = $this->params->get('app.datalogger.signature');
        $config = [
            'frequency' => $freq,
            'intTemp' => $tempInt,
            'intHygro' => $hygroInt,
            'extTemp' => $tempExt,
            'extHygro' => $hygroExt,
            'weight' => $weight,
            'signature' => $signature,
            'version' => $version,
        ];
        return $config;
    }

    private function generateConfigFile(array $config, Hive $hive, string $folder): void
    {
        $dto = DataloggerConfigDto::fromArray($config);
        $this->io->text('Création du fichier de configuration');

        $user = $hive->getOwner();
        $path = $this->projectDir . DIRECTORY_SEPARATOR . $folder;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $path .= DIRECTORY_SEPARATOR;
        $fileConfig = $this->maker->makeConfig($hive, $dto, $path, $user);
        if (file_exists("{$path}{$fileConfig}")) {
            $this->io->text("Fichier de configuration créé ici : {$path}{$fileConfig}");
        }
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
        if (!is_dir($basePath)) {
            mkdir($basePath, 0777, true);
        }
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

    private function generateSample(array $config, Hive $hive): array
    {
        $sample = [
            'hive' => $hive->getName(),
            'hiveId' => $hive->getIdentification()
        ];
        $sample['time'] = $this->randomDate();
        if ($config['extHygro']) {
            $sample[] = rand(20, 90);
        }
        if ($config['intHygro']) {
            $sample[] = rand(20, 90);
        }
        if ($config['intTemp']) {
            $sample[] = $this->randomFloat();
        }
        if ($config['extTemp']) {
            $sample[] = $this->randomFloat();
        }
        if ($config['weight']) {
            $sample[] = $this->randomFloat(true);
        }
        return $sample;
    }
}
