<?php

namespace App\Service\Stats;

use App\Entity\Hive;
use App\Tool\Results;
use App\Dto\HarvestDto;
use App\Tool\PathMaker;
use App\Dto\DataloggerDto;
use App\Entity\Apiculteur;
use App\Entity\Datalogger;
use App\Entity\Archive\Visit;
use App\Tool\Graph\LineDrawer;
use App\Tool\Graph\HarvestGraph;
use App\Repository\HarvestRepository;
use App\Repository\DataloggerRepository;
use App\Repository\Archive\VisitsRepository;
use App\Entity\Archive\Datalogger as ArchiveDatalogger;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use App\Repository\Archive\HarvestRepository as ArchiveHarvestRepository;
use App\Repository\Archive\DataloggerRepository as ArchiveDataloggerRepository;

class HiveResults
{
    public function __construct(
        private HarvestRepository $harvestRepo,
        private DataloggerRepository $dlRepo,
        private VisitsRepository $archives,
        private ArchiveHarvestRepository $harvestArchives,
        private ArchiveDataloggerRepository $archiveDl,
        #[Autowire('%kernel.project_dir%/public/uploads/')] private string $uploadDirectory
    ) {}

    public function getHiveStats(Hive $hive, Apiculteur $user, string $rootPath): string
    {
        $relativePath = PathMaker::makeHiveResultPath($user, $hive, $rootPath);
        $fullPath = $rootPath . $relativePath;
        $filename = 'results.pdf';
        $pdf = new Results('L');
        $this->initPdf($pdf);
        $pdf->addPage();
        $this->headerPdf($pdf, $hive);

        $harvests = $this->harvestRepo->findByHiveBeekeeperDate($user, $hive);
        $harvestArchives = $this->harvestArchives->findByHiveBeekeeperDate($user, $hive);
        $harvests = array_merge($harvests, $harvestArchives);
        if (0 < count($harvests)) {
            $totalHarvestsWeight = $this->getTotalWeight($harvests);
            $totalWeightByType = $this->calcWeigthByType($harvests);
            $harvestDrawer = new HarvestGraph($this->uploadDirectory);
            $harvestFilename = $harvestDrawer->drawGraph($harvests, $fullPath, $rootPath, $hive->getName());
            $picturePath = $fullPath . $harvestFilename;
            $this->writeHarvests($pdf, $hive, $totalHarvestsWeight, $totalWeightByType, $picturePath);
        }
        $archives = $this->getArchives($hive, $user);
        $visits = $hive->getVisits()->toArray();
        $count = count($visits) + count($archives);
        if (0 < $count) {
            $visitsDispatched = $this->dispatchVisits($hive->getVisits()->toArray());
            $tempVisit = $visitsDispatched['temperature'];
            $tempArchives = $archives['temperature'];
            $tempVisit = array_merge($tempVisit, $tempArchives);
            usort($tempVisit, fn($a, $b) => $a['date'] <=> $b['date']);
            if (0 < count($tempVisit)) {
                $title = "Relevé des températures de la ruche {$hive->getName()}";
                $picturename = 'temp.png';
                $this->writeSingleGraph($pdf, $title, $tempVisit, $fullPath . $picturename);
            }

            $hygroVisit = $visitsDispatched['hygro'];
            $hygroArchives = $archives['hygro'];
            $hygroVisit = array_merge($hygroVisit, $hygroArchives);
            usort($hygroVisit, fn($a, $b) => $a['date'] <=> $b['date']);
            if (0 < count($hygroVisit)) {
                $title = "Relevé de l'hygrométrie de la ruche {$hive->getName()} depuis les fiches de visite";
                $picturename = "hygro.png";
                $this->writeSingleGraph($pdf, $title, $hygroVisit, $fullPath . $picturename);
            }

            $weightVisit = $visitsDispatched['weight'];
            $weightArchive = $archives['weight'];
            $weightVisit = array_merge($weightVisit, $weightArchive);
            usort($weightVisit, fn($a, $b) => $a['date'] <=> $b['date']);
            if (0 < count($weightVisit)) {
                $title = "Relevé de poids de la ruche {$hive->getName()} depuis les fiches de visite";
                $picturename = "weight.png";
                $this->writeSingleGraph($pdf, $title, $weightVisit, $fullPath . $picturename);
            }
        }
        /**@var Datalogger[] */
        $dataloggersDatas = $this->dlRepo->findByHiveAndBeekeeper($hive, $user);
        $archivesDlInfos = $this->archiveDl->findByHiveAndBeekeeper($hive, $user);
        $dataloggersInfos = $this->mergeDataloggers($dataloggersDatas, $archivesDlInfos);
        if (0 < count($dataloggersInfos)) {
            $dlAverages = $this->getAverageDl($dataloggersInfos);
            $dlDispatched = $this->dispatchDl($dataloggersInfos);
            $from = $dataloggersInfos[0]->dateTime->format('d/m/Y');
            $to = end($dataloggersInfos)->dateTime->format('d/m/Y');
            $this->writeDataloggerAverage($pdf, $dlAverages, $from, $to);
            if (0 < count($dlDispatched['weight'])) {
                $title = 'Relevé du poids via le datalogger de la ruche';
                $picturename = "dlweight.png";
                $this->writeSingleGraph($pdf, $title, $dlDispatched['weight'], $fullPath . $picturename);
            }
            if (0 < count($dlDispatched['tempExt'])) {
                $picturename = "dltemp.png";
                $path = $fullPath . $picturename;
                $this->writeDataloggerMultiLine($pdf, "Relevé de la température via le datalogger de la ruche", $dlDispatched['tempExt'], "température extérieure",  $dlDispatched['tempInt'], "température intérieure", $path);
            }
            if (0 < count($dlDispatched['hygroExt'])) {
                $picturename = "dlhygro.png";
                $path = $fullPath . $picturename;
                $this->writeDataloggerMultiLine($pdf, "Relevé de l'hygrométrie via le datalogger de la ruche", $dlDispatched['hygroExt'], "hygrométrie extérieure",  $dlDispatched['hygroInt'], "hygrométrie intérieure", $path);
            }
        }
        $pdfPath = $fullPath . $filename;
        $pdf->Output('F', $pdfPath);
        return $relativePath . $filename;
    }

    /**
     * Merge logs from BD with dataas from archives
     *
     * @param Datalogger[] $logs
     * @param ArchiveDatalogger[] $archives
     * @return DataloggerDto[]
     */
    private function mergeDataloggers(array $logs, array $archives): array
    {
        $returnArray = [];
        foreach ($logs as $log) {
            $dto = DataloggerDto::fromLogs($log);
            $returnArray[] = $dto;
        }
        foreach ($archives as $archive) {
            $dto = DataloggerDto::fromArchive($archive);
            $returnArray[] = $dto;
        }
        usort($returnArray, fn($a, $b) => $a->dateTime <=> $b->dateTime);
        return $returnArray;
    }

    /**
     * Split archives visits values in an array sort by type and date
     *
     * @param Hive hive
     * @param Apiculteur $user
     * @return array{weight: array{date: \DateTimeImmutable, value: mixed}, 
     * temperature: array{date: \DateTimeImmutable, value: mixed}, 
     * hygro: array{date: \DateTimeImmutable, value: mixed}}
     */
    private function getArchives(Hive $hive, Apiculteur $user): array
    {
        $archives = $this->archives->findByHiveAndBeekeeper($hive, $user);
        $weightArray = [];
        $hygroArray = [];
        $tempArray = [];
        /** @var Visit[] $archives */
        foreach ($archives as $archive) {
            $date = $archive->getDate();
            $weight = $archive->getWeight();
            $temp = $archive->getTemperature();
            $hygro = $archive->getHygrometry();
            if (null !== $weight) {
                $weightArray[] = ['date' => $date, 'value' => $weight];
            }
            if (null !== $temp) {
                $tempArray[] = ['date' => $date, 'value' => $temp];
            }
            if (null !== $hygro) {
                $hygroArray[] = ['date' => $date, 'value' => $hygro];
            }
        }
        return [
            'weight' => $weightArray,
            'temperature' => $tempArray,
            'hygro' => $hygroArray
        ];
    }

    private function initPdf(Results $pdf,): void
    {
        $pdf->AliasNbPages();
        $pdf->setAuthor('Editiel98');
        $pdf->setCreator('Gestion Rucher');
        $pdf->SetTitle(html_entity_decode('Bilan de la ruche'));
        $pdf->setDocTitle('Bilan de la ruche');
    }

    private function headerPdf(Results $pdf, Hive $hive): void
    {
        $pdf->subtitle($hive->getDate()->format('d/m/Y'), date('d/m/Y'));
        $pdf->setHiveInfos($hive);
    }

    private function writeHarvests(Results $pdf, Hive $hive, float $totalWeight, array $weights, string $picturePath): void
    {
        $pdf->setHarvests($totalWeight, $weights);
        $pdf->addPage();
        $title = "Récolte de la ruche {$hive->getName()}";
        $pdf->setGraph($picturePath, $title, 20);
    }

    /**
     * Write the pdf part for a graph
     *
     * @param Results $pdf
     * @param string $title
     * @param array<int, array{date: \DateTimeImmutable, value: mixed}> $values
     * @param string $fullPath
     * @return void
     */
    private function writeSingleGraph(Results $pdf, string $title, array $values, string $fullPath): void
    {
        $pdf->AddPage();
        $from = $values[0]['date']->format('d/m/Y');
        $to = end($values)['date']->format('d/m/Y');
        $pdf->Subtitle($from, $to);
        $this->drawGraphOneLine($title, $values, $fullPath);
        $pdf->setGraph($fullPath, $title);
    }

    private function writeDataloggerMultiLine(Results $pdf, string $title, array $line1, string $legend1, array $line2, string $legend2, string $path)
    {
        $pdf->AddPage();
        $from = $line1[0]['date']->format('d/m/Y');
        $to = end($line1)['date']->format('d/m/Y');
        $pdf->Subtitle($from, $to);
        if (0 <= count($line2)) {
            $this->drawGraphTwoLines($title, $line1, $legend1, $line2, $legend2, $path);
        } else {
            $this->drawGraphOneLine("{$title} : {$legend1}", $line1, $path);
        }
        $pdf->setGraph($path, $title);
    }

    private function drawGraphTwoLines(string $title, array $valuesLine1, string $legend1, array $valuesLine2,  string $legend2, string $path): void
    {
        if (file_exists($path)) {
            unlink($path);
        }
        $graph = new LineDrawer();
        $graph->drawGraphTwoLines($title, $valuesLine1, $legend1, $valuesLine2, $legend2);
        $graph->save($path);
    }

    private function writeDataloggerAverage(Results $pdf, array $averages, string $from, string $to): void
    {
        $pdf->addPage();
        $pdf->dataloggerInfos($from, $to, $averages['identifications']);
        $pdf->addPage();
        $pdf->Subtitle($from, $to);
        if (null !== $averages['extHygro']) {
            $extHygro = number_format($averages['extHygro'], 2);
        } else {
            $extHygro = '-';
        }
        if (null !== $averages['intHygro']) {
            $intHygro = number_format($averages['intHygro'], 2);
        } else {
            $intHygro = '-';
        }
        if (null !== $averages['extTemp']) {
            $extTemp = number_format($averages['extTemp'], 2);
        } else {
            $extTemp = '-';
        }
        if (null !== $averages['intTemp']) {
            $intTemp = number_format($averages['intTemp'], 2);
        } else {
            $intTemp = '-';
        }
        if (null !== $averages['weight']) {
            $weight = number_format($averages['weight'], 2);
        } else {
            $weight = '-';
        }
        $pdf->setAverage($weight, $extTemp, $intTemp, $extHygro, $intHygro);
    }

    private function drawGraphOneLine(string $title, array $values, string $path): void
    {
        if (file_exists($path)) {
            unlink($path);
        }
        $graph = new LineDrawer();
        $graph->drawGraphOneLine($title, $values);
        $graph->save($path);
    }

    /**
     * get total weight for the hive
     *
     * @param HarvestDto[] $harvests
     * @return float
     */
    private function getTotalWeight(array $harvests): float
    {
        $total = 0;
        foreach ($harvests as $harvest) {
            $total += $harvest->weight;
        }
        return $total;
    }

    /**
     * get total weight for the hive by types
     *
     * @param HarvestDto[] $harvests
     * @return array<string, float>
     */
    private function calcWeigthByType(array $harvests): array
    {
        $returnArray = [];
        foreach ($harvests as $harvest) {
            $type = $harvest->type;
            if (!key_exists($type, $returnArray)) {
                $returnArray[$type] = $harvest->weight;
            } else {
                $returnArray[$type] += $harvest->weight;
            }
        }
        return $returnArray;
    }

    /**
     * Split visits values in an array sort by type and date
     *
     * @param Visit[] $visits
     * @return array{weight: array{date: \DateTimeImmutable, value: mixed}, 
     * temperature: array{date: \DateTimeImmutable, value: mixed}, 
     * hygro: array{date: \DateTimeImmutable, value: mixed}}
     */
    private function dispatchVisits(array $visits): array
    {
        $resultArray = [];
        usort($visits, fn($a, $b) => $a->getDate() <=> $b->getDate());
        foreach ($visits as $visit) {
            if (null !== $visit->getWeight()) {
                $resultArray['weight'][] = array('date' => $visit->getDate(), 'value' => $visit->getWeight());
            }
            if (null !== $visit->getTemperature()) {
                $resultArray['temperature'][] = array('date' => $visit->getDate(), 'value' => $visit->getTemperature());
            }
            if (null !== $visit->getHygrometry()) {
                $resultArray['hygro'][] = array('date' => $visit->getDate(), 'value' => $visit->getHygrometry());
            }
        }
        //retourner les tableaux
        return $resultArray;
    }

    /**
     * Get all average values from dataloggers
     *
     * @param DataloggerDto[] $dataloggers
     * @return array
     */
    private function getAverageDl(array $dataloggers): array
    {
        $resultArray = [];
        $identification = [];
        $averageExtHygro = 0;
        $averageIntHygro = 0;
        $averageWeight = 0;
        $averageExtTemp = 0;
        $averageIntTemp = 0;
        $quantityExtHygro = 0;
        $quantityIntHygro = 0;
        $quantityExtTemp = 0;
        $quantityIntTemp = 0;
        $quantityWeight = 0;
        foreach ($dataloggers as $log) {
            $identification[] = $log->identification;
            if (null !== $log->weight) {
                $averageWeight += $log->weight;
                $quantityWeight++;
            }
            if (null !== $log->extHygro) {
                $averageExtHygro += $log->extHygro;
                $quantityExtHygro++;
            }
            if (null !== $log->intHygro) {
                $averageIntHygro += $log->intHygro;
                $quantityIntHygro++;
            }
            if (null !== $log->extTemp) {
                $averageExtTemp += $log->extTemp;
                $quantityExtTemp++;
            }
            if (null !== $log->intTemp) {
                $averageIntTemp += $log->intTemp;
                $quantityIntTemp++;
            }
        }
        if (0 < $quantityExtHygro) {
            $resultArray['extHygro'] = round($averageExtHygro / $quantityExtHygro, 2);
        } else {
            $resultArray['extTemp'] = null;
        }

        if (0 < $quantityIntHygro) {
            $resultArray['intHygro'] = round($averageIntHygro / $quantityIntHygro, 2);
        } else {
            $resultArray['intHygro'] = null;
        }
        if (0 < $quantityExtTemp) {
            $resultArray['extTemp'] = round($averageExtTemp / $quantityExtTemp, 2);
        } else {
            $resultArray['extTemp'] = null;
        }
        if (0 < $quantityIntTemp) {
            $resultArray['intTemp'] = round($averageIntTemp / $quantityIntTemp, 2);
        } else {
            $resultArray['intTemp'] = null;
        }
        if (0 < $quantityWeight) {
            $resultArray['weight'] = round($averageWeight / $quantityWeight, 2);
        } else {
            $resultArray['weight'] = null;
        }
        $resultArray['identifications'] = array_unique($identification, SORT_STRING);
        return $resultArray;
    }

    /**
     * Transform Dataloggers info into arrays (sensor value-date)
     *
     * @param DataloggerDto[] $dataloggers
     * @return array{weight: array{date: \DateTimeImmutable, value: mixed},
     *  tempExt: array{date: \DateTimeImmutable, value: mixed},
     *  tempInt: array{date: \DateTimeImmutable, value: mixed},
     *  hygroExt: array{date: \DateTimeImmutable, value: mixed},
     *  hygroInt: array{date: \DateTimeImmutable, value: mixed},
     *  }
     */
    private function dispatchDl(array $dataloggers): array
    {
        $returnArray = [];
        usort($dataloggers, fn($a, $b) => $a->dateTime <=> $b->dateTime);
        foreach ($dataloggers as $datalogger) {
            if (null !== $datalogger->extTemp) {
                $returnArray['tempExt'][] = array('date' => $datalogger->dateTime, 'value' => $datalogger->extTemp);
            }
            if (null !== $datalogger->intTemp) {
                $returnArray['tempInt'][] = array('date' => $datalogger->dateTime, 'value' => $datalogger->intTemp);
            }
            if (null !== $datalogger->extHygro) {
                $returnArray['hygroExt'][] = array('date' => $datalogger->dateTime, 'value' => $datalogger->extHygro);
            }
            if (null !== $datalogger->intHygro) {
                $returnArray['hygroInt'][] = array('date' => $datalogger->dateTime, 'value' => $datalogger->intHygro);
            }
            if (null !== $datalogger->weight) {
                $returnArray['weight'][] = array('date' => $datalogger->dateTime, 'value' => $datalogger->weight);
            }
        }
        return $returnArray;
    }
}
