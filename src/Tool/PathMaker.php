<?php

namespace App\Tool;

use App\Entity\Apiary;
use App\Entity\Hive;
use App\Entity\Apiculteur;

class PathMaker
{
    /**
     * Will create and return the relative path where to store datalogger config file
     * 
     * @param Apiculteur $user 
     * @param Hive $hive
     * @param string $basePath : path to upload directory
     * @return string return the realtive path with the trailing DIRECTORY SEPARATOR
     */
    public static function makeDataloggerConfigPath(Apiculteur $user, Hive $hive, string $basePath): string
    {
        $path =  $user->getId() . DIRECTORY_SEPARATOR;
        $path .= 'datalogger' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
        $path .= $hive->getId() . DIRECTORY_SEPARATOR;
        $fullPath = $basePath . $path;
        self::makeFolder($fullPath);
        return $path;
    }

    /**
     *  Will create and return the absolute path where to store datalogger data files
     * 
     * @param Apiculteur $user
     * @param Hive $hive
     * @param string $basePath : path to upload directory
     * @return string return the whole path with the trailing DIRECTORY SEPARATOR
     */
    public static function makeDataloggerFilePath(Apiculteur $user, Hive $hive, string $basePath): string
    {
        $fullPath = $basePath . $user->getId() . DIRECTORY_SEPARATOR;
        $fullPath .= 'datalogger' . DIRECTORY_SEPARATOR . 'datas' . DIRECTORY_SEPARATOR;
        $fullPath .= $hive->getId() . DIRECTORY_SEPARATOR;
        self::makeFolder($fullPath);
        return $fullPath;
    }

    /**
     *  Will create and return the relative path where to store hive statistic files
     *
     * @param Apiculteur $user
     * @param Hive $hive
     * @param string $typeDoc
     * @param string $basePath
     * @return string return the relative path with the trailing DIRECTORY SEPARATOR
     */
    public static function makeUserHiveStatPath(Apiculteur $user, Hive $hive, string $typeDoc, string $basePath): string
    {
        $path = $user->getId() . DIRECTORY_SEPARATOR;
        $path .= 'stats' . DIRECTORY_SEPARATOR . $typeDoc . DIRECTORY_SEPARATOR;
        $path .= $hive->getId() . DIRECTORY_SEPARATOR;
        $fullPath = $basePath . $path;
        self::makeFolder($fullPath);
        return $path;
    }

    /**
     * Will create and return the relative path where to store users's apiary declaration files
     *
     * @param Apiculteur $user
     * @param string $basePath
     * @return string return the relative path with the trailing DIRECTORY SEPARATOR
     */
    public static function makeApiaryDeclarationPath(Apiculteur $user, string $basePath): string
    {
        $path = $user->getId() . DIRECTORY_SEPARATOR;
        $path .= 'documents' . DIRECTORY_SEPARATOR;
        $fullPath = $basePath . $path;
        self::makeFolder($fullPath);
        return $path;
    }

    /**
     * Will create and return the folder where to store apiary cartography file
     *
     * @param Apiculteur $user
     * @param Apiary $apiary
     * @param string $basePath
     * @return string return the relative path with trailing DIRECTORY SEPARATOR
     */
    public static function makeCartoApiaryPath(Apiculteur $user, Apiary $apiary, string $basePath): string
    {
        $path = $user->getId() . DIRECTORY_SEPARATOR;
        $path .= 'carto' . DIRECTORY_SEPARATOR;
        $fullPath = $basePath . $path;
        self::makeFolder($fullPath);
        return $path;
    }

    /**
     * Will make admin folder to upload honey pictures and return relative path
     *
     * @param string $basePath
     * @return string : relative path to honey folder with trailing DIRECTORY_SEPARATOR
     */
    public static function makeHoneyFolder(string $basePath): string
    {
        $path = 'admin' . DIRECTORY_SEPARATOR;
        $path .= 'honeys' . DIRECTORY_SEPARATOR;
        $fullPath = $basePath . $path;
        self::makeFolder($fullPath);
        return $path;
    }

    /**
     * Will make admin folder to upload hives pictures and return relative path
     *
     * @param string $basePath
     * @return string : relative path to hives folder with trailing DIRECTORY_SEPARATOR
     */
    public static function makeAdminHivesFolder(string $basePath): string
    {
        $path = 'admin' . DIRECTORY_SEPARATOR;
        $path .= 'hives' . DIRECTORY_SEPARATOR;
        $fullPath = $basePath . $path;
        self::makeFolder($fullPath);
        return $path;
    }

    public static function makeStatPicturePath(Apiculteur $user, Hive $hive,  string $basePath): string
    {
        $path = $user->getId() . DIRECTORY_SEPARATOR;
        $path .= 'stats' . DIRECTORY_SEPARATOR . 'pictures' . DIRECTORY_SEPARATOR;
        $path .= $hive->getId() . DIRECTORY_SEPARATOR;
        $fullPath = $basePath . $path;
        self::makeFolder($fullPath);
        return $path;
    }

    public static function makeDataloggerPicturePath(Apiculteur $user, Hive $hive,  string $basePath): string
    {
        $path = $user->getId() . DIRECTORY_SEPARATOR;
        $path .= 'stats' . DIRECTORY_SEPARATOR . 'datalogger' . DIRECTORY_SEPARATOR;
        $path .= $hive->getId() . DIRECTORY_SEPARATOR;
        $fullPath = $basePath . $path;
        self::makeFolder($fullPath);
        return $path;
    }

    public static function makeHarvestPicturePath(Apiculteur $user, Hive $hive, string $basePath): string
    {
        $path = $user->getId() . DIRECTORY_SEPARATOR;
        $path .= 'stats' . DIRECTORY_SEPARATOR . 'harvests' . DIRECTORY_SEPARATOR;
        $path .= $hive->getId() . DIRECTORY_SEPARATOR;
        $fullPath = $basePath . $path;
        self::makeFolder($fullPath);
        return $path;
    }

    public static function makeHiveResultPath(Apiculteur $user, Hive $hive, string $basePath): string
    {
        $path = $user->getId() . DIRECTORY_SEPARATOR;
        $path .= 'stats' . DIRECTORY_SEPARATOR . 'results' . DIRECTORY_SEPARATOR;
        $path .= $hive->getId() . DIRECTORY_SEPARATOR;
        $fullPath = $basePath . $path;
        self::makeFolder($fullPath);
        return $path;
    }

    public static function makeApiaryStatsPath(Apiary $apiary, string $basePath): string
    {
        $user = $apiary->getOwner();
        $path = $user->getId() . DIRECTORY_SEPARATOR;
        $path .= 'stats' . DIRECTORY_SEPARATOR . 'apiary' . DIRECTORY_SEPARATOR;
        $path .= $apiary->getId() . DIRECTORY_SEPARATOR;
        $fullPath = $basePath . $path;
        self::makeFolder($fullPath);
        return $path;
    }

    public static function makeApiaryFullCartoPath(Apiary $apiary, string $basePath): string
    {
        $user = $apiary->getOwner();
        $path = $user->getId() . DIRECTORY_SEPARATOR;
        $path .= 'carto' . DIRECTORY_SEPARATOR . 'apiary' . DIRECTORY_SEPARATOR;
        $path .= $apiary->getId() . DIRECTORY_SEPARATOR;
        $fullPath = $basePath . $path;
        self::makeFolder($fullPath);
        return $path;
    }


    private static function makeFolder(string $fullPath): void
    {
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0744, true);
        }
    }

    public static function getCleanPaths(Apiculteur $user): array
    {
        $returnArray = [];
        //Documents
        $documentPath = $user->getId() . DIRECTORY_SEPARATOR;
        $documentPath .= 'documents' . DIRECTORY_SEPARATOR;
        $returnArray['documents'] = $documentPath;
        //Dataloggers datas
        $DataloggerPath =  $user->getId() . DIRECTORY_SEPARATOR;
        $DataloggerPath .= 'datalogger' . DIRECTORY_SEPARATOR . 'datas' . DIRECTORY_SEPARATOR;
        $returnArray['datalogger'] = $DataloggerPath;
        //Stats
        $statsPath = $user->getId() . DIRECTORY_SEPARATOR;
        $statsPath .= 'stats' . DIRECTORY_SEPARATOR;
        $returnArray['stats'] = $statsPath;
        return $returnArray;
    }
}
