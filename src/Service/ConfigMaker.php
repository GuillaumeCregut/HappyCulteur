<?php

namespace App\Service;

use App\Entity\Hive;
use App\Dto\DataloggerConfigDto;
use App\Entity\Apiculteur;
use App\Exception\DataloggerFileException;
use App\Tool\PathMaker;

class ConfigMaker
{
    public function makeConfig(Hive $hive, DataloggerConfigDto $dto, string $path, Apiculteur $user): string
    {
        $fullPath = PathMaker::makeDataloggerConfigPath($user->getId(), $hive->getId(), $path);
        if(!is_dir($fullPath)) {
            mkdir($fullPath, 0644, true);
        }
        $fullPath .= 'config.json';
        $returnpath = str_replace($path,'',$fullPath);
        $config = (array) $dto;
        $config['hive'] = $hive->getName();
        $config['identification'] = $hive->getIdentification(); //TODO: Change this
        $config['hiveId'] = $hive->getIdentification();
        $template = [];
        $template['hive'] = 'string';
        $template['hiveId'] = 'string';
        $template['time'] = 'string';
        if($dto->extHygro) {
            $template['extHygro'] = 'int';
        }
        if($dto->intHygro) {
            $template['intHygro'] = 'int';
        }
        if($dto->intTemp) {
            $template['intTemp'] = 'float';
        }
        if($dto->extTemp) {
            $template['extTemp'] = 'float';
        }
        if($dto->weight) {
            $template['weight'] = 'float';
        }
        $content = [
            'config' =>$config,
            'template'=>$template
        ];
        $encoded = json_encode($content);
        if(!file_put_contents($fullPath,$encoded)) {
            throw new DataloggerFileException('Config file not written');
        }
        return $returnpath;
    }

    public function loadConfig(Hive $hive, string $path): DataloggerConfigDto
    {
        $config = $hive->getDataloggerName();
        $fullPath = $path .$config;
        if(!file_exists($fullPath)) {
            throw new DataloggerFileException('Config file not found');
        }
        $json = file_get_contents($fullPath);
        $decoded = json_decode($json, true);
        if(!is_array($decoded)) {
            throw new DataloggerFileException('invalid config format');
        }
        $configFromFile = $decoded['config'];
        $dto = DataloggerConfigDto::fromArray($configFromFile);
        return $dto;
    }
}