<?php

namespace App\Service;

use App\Entity\Apiculteur;
use App\Entity\Datalogger;
use Exception;
use App\Entity\Hive;
use Editiel98\FileWriter\FileWriter;
use App\Exception\DataloggerFileException;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\File;

class DataloggerLoadFile
{
    public function isFileValid(File $file, int $version, string $signature): array
    {
        $ok = [];
        //Load config file from hive
        $header = [
            'signature' => $signature,
            'version' => $version
        ];
        //Get signature, version and Hive Id from data file
        $filePath = $file->getPathname();
        try {
            $fileHeaders = FileWriter::readHeaderFile($filePath, $header);
        } catch (Exception $e) {
            throw new DataloggerFileException($e->getMessage());
        }
        //Check all
        if ($signature !== $fileHeaders['signature']) {
            $ok[] = "Signature invalide";
        }
        if ($version !== $fileHeaders['version']) {
            $ok[] = "Version invalide";
        }
        return $ok;
    }

    public function loadDatas(string $fileName, Hive $hive, string $rootPath, Apiculteur $user): array | false
    {
        $file = new File($fileName, true);
        $configPath = $rootPath . $hive->getDataloggerName();
        $config = $this->loadConfig($configPath);

        $configHeader = $config['config'];
        $template = $config['template'];
        if (($configHeader['hive'] !== $hive->getName()) && ($configHeader['hiveId'] !== $hive->getIdentification())) {
            return false;
        }

        try {
            $errors = $this->isFileValid($file, $configHeader['version'], $configHeader['signature']);
            if (!empty($errors)) {
                $errorsString = implode(', ', $errors);
                throw new DataloggerFileException("Le fichier contient des valeurs erronées : {$errorsString}");
            }
            $header = [
                'signature' => $configHeader['signature'],
                'version' => $configHeader['version']
            ];

            $idLogger = $configHeader['identification'] ?? substr($hive->getIdentification(), 0, 20);

            $fileLoader = new FileWriter($fileName, $header);
            $datas = $fileLoader->readFile($fileName, $header, $template);

            return $this->convertDatas($datas, $hive, $user, $idLogger);
        } catch (Exception $e) {
            throw new DataloggerFileException($e->getMessage());
        }
    }

    private function loadConfig(string $filename): array
    {
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

    private function convertDatas(array $datas, Hive $hive, Apiculteur $user, string $idDatalogger): array
    {
        $values = [];
        $hiveId = $hive->getIdentification();
        $hiveName = $hive->getName();
        //Make entities
        foreach ($datas as $data) {
            if (($data['hive'] !== $hiveName) || ($data['hiveId'] !== $hiveId)) {
                continue; //Do something else ?
            }
            $extTemp = $this->getValue($data, 'extTemp');
            $intTemp =  $this->getValue($data, 'intTemp');
            $extHygro = $data['extHygro'] ?? null;
            $intHygro = $data['intHygro'] ?? null;
            $weight =  $this->getValue($data, 'weight');
            $logger = new Datalogger();
            $date = new DateTimeImmutable($data['time']);
            $logger->setDateTime($date)
                ->setHive($hive)
                ->setBeekeeper($user)
                ->setIdentification($idDatalogger)
                ->setExtHyrgo($extHygro)
                ->setIntHygro($intHygro)
                ->setExtTemp($extTemp)
                ->setIntTemp($intTemp)
                ->setWeight($weight);
            $values[] = $logger;
        }
        return $values;
    }

    private function getValue(array $values, string $key): ?float
    {
        if (array_key_exists($key, $values)) {
            $value = $values[$key];
            return round($value, 2);
        }
        return null;
    }
}
