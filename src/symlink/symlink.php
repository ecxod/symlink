<?php

declare(strict_types=1);

namespace Ecxod\Symlink;

use \FilesystemIterator;
use \JsonException;
use \RuntimeException;
use \Throwable;
use function Ecxod\Funktionen\{m, logg, addIfNotExists};


class symlink
{

    protected string $composer_json;
    protected string $package_json;
    protected string $symlink_json;
    protected string $symlink_example_json;

    /** 
     * @return void
     * @author Christian Eichert <c@zp1.net>
     * @version 1.0.0
     */
    public function __construct()
    {
        $this->composer_json = 'composer.json';

        $this->package_json = 'package.json';

        $this->symlink_json = 'symlink.json';

        $this->symlink_example_json = 'symlink-example.json';

        $this->create_symlink_example();


    }


    protected function getWorkspace()
    {
        $workspace = realpath($_SERVER['DOCUMENT_ROOT'] . "/..");
        if($workspace === false)
        {
            throw new RuntimeException("Ungültiger Workspace-Pfad");
        }
        $_ENV['WORKSPACE'] ??= $workspace;
        return $workspace;
    }





    protected function get_object_from_json(string $jsonfile)
    {

        $json= $this->getWorkspace() . DIRECTORY_SEPARATOR . $jsonfile;

        if(!file_exists($json) || !is_readable($json))
        {
            throw new RuntimeException("$jsonfile nicht gefunden oder nicht lesbar");
        }
        $string = file_get_contents($json);
        if($string === false)
        {
            throw new RuntimeException("Konnte $jsonfile nicht lesen");
        }
        $obj = json_decode($string, true, 512, JSON_INVALID_UTF8_SUBSTITUTE);
        if(json_last_error() !== JSON_ERROR_NONE)
        {
            throw new JsonException("Ungültiges JSON in $jsonfile: " . json_last_error_msg());
        }
        return $obj;
    }





    protected function create_symlink_example()
    {
        try
        {

            // Pfade zu den JSON-Dateien
            // $composer_json_absolute            = $this->getWorkspace() . DIRECTORY_SEPARATOR . 'composer.json';
            //$package_json_absolute             = $this->getWorkspace() . DIRECTORY_SEPARATOR . 'package.json';
            $symlink_example_existent = $this->getWorkspace() . DIRECTORY_SEPARATOR . 'symlink-example.json';

            // // Prüfe und lese composer.json
            // if(!file_exists($composer_json_absolute) || !is_readable($composer_json_absolute))
            // {
            //     throw new RuntimeException("composer.json nicht gefunden oder nicht lesbar");
            // }
            // $composer_content = file_get_contents($composer_json_absolute);
            // if($composer_content === false)
            // {
            //     throw new RuntimeException("Konnte composer.json nicht lesen");
            // }
            // $composer_obj = json_decode($composer_content, true, 512, JSON_INVALID_UTF8_SUBSTITUTE);
            // if(json_last_error() !== JSON_ERROR_NONE)
            // {
            //     throw new JsonException("Ungültiges JSON in composer.json: " . json_last_error_msg());
            // }

            $composer_obj = $this->get_object_from_json($this->composer_json);

            // // Prüfe und lese package.json
            // if(!file_exists($package_json_absolute) || !is_readable($package_json_absolute))
            // {
            //     throw new RuntimeException("package.json nicht gefunden oder nicht lesbar");
            // }
            // $package_content = file_get_contents($package_json_absolute);
            // if($package_content === false)
            // {
            //     throw new RuntimeException("Konnte package.json nicht lesen");
            // }
            // $package_obj = json_decode($package_content, true, 512, JSON_INVALID_UTF8_SUBSTITUTE);
            // if(json_last_error() !== JSON_ERROR_NONE)
            // {
            //     throw new JsonException("Ungültiges JSON in package.json: " . json_last_error_msg());
            // }

            $package_obj = $this->get_object_from_json($this->package_json);




            // Prüfe erforderliche Schlüssel
            if(!isset($composer_obj['require'], $package_obj['dependencies']))
            {
                throw new RuntimeException("Erforderliche Schlüssel 'require' oder 'dependencies' fehlen");
            }

            // Setze alle Werte in require und dependencies auf false
            $require_modified      = array_map(fn($value) => false, $composer_obj['require']);
            $dependencies_modified = array_map(fn($value) => false, $package_obj['dependencies']);

            // Erstelle kombiniertes JSON-Datenarray
            $symlink_data = [ 
                'require'      => $require_modified,
                'dependencies' => $dependencies_modified,
            ];

            // Prüfe, ob symlink-example.json existiert und aktuell ist
            $symlink_example_existent_object = null;
            if(file_exists($symlink_example_existent) && is_readable($symlink_example_existent))
            {
                $symlink_example_existent_content = file_get_contents($symlink_example_existent);
                if($symlink_example_existent_content === false)
                {
                    throw new RuntimeException("Konnte symlink-example.json nicht lesen");
                }
                $symlink_example_existent_object = json_decode($symlink_example_existent_content, true, 512, JSON_INVALID_UTF8_SUBSTITUTE);
                if(json_last_error() !== JSON_ERROR_NONE)
                {
                    throw new JsonException("Ungültiges JSON in symlink-example.json: " . json_last_error_msg());
                }
                // Vergleiche die Daten direkt, um doppelte Kodierung zu vermeiden
                if($symlink_data === $symlink_example_existent_object)
                {
                    return; // Datei ist aktuell
                }
            }

            // Prüfe Schreibrechte
            $symlink_dir = dirname($symlink_example_existent);
            if(!is_writable($symlink_dir))
            {
                throw new RuntimeException("Verzeichnis für symlink-example.json ist nicht beschreibbar");
            }



            // Kodiere die Daten nur einmal und schreibe die Ausgabedatei
            $symlink_json = json_encode($symlink_data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
            $symlink_json = str_replace('\/', '/', $symlink_json);

            if(file_put_contents($symlink_example_existent, $symlink_json) === false)
            {
                throw new RuntimeException("Konnte symlink-example.json nicht schreiben");
            }
        }
        catch ( Exception $e )
        {
            error_log("Fehler: " . $e->getMessage());
            exit("Ein Fehler ist aufgetreten: " . $e->getMessage());
        }
    }






    public function setSymlinksFromJson($jsonString, $baseSourcePath)
    {
        // JSON dekodieren
        $data = json_decode($jsonString, true);

        if($data === null)
        {
            throw new Exception("Ungültiges JSON-Format");
        }

        // Durch die erste Ebene iterieren (z.B. "node_modules", "vendor")
        foreach($data as $rootKey => $rootValue)
        {
            // Durch die zweite Ebene iterieren (Tiefe 2)
            if(is_array($rootValue))
            {
                foreach($rootValue as $key => $value)
                {
                    // Prüfen, ob der Wert mit "public" beginnt
                    if(is_string($value) && strpos($value, 'public') === 0)
                    {
                        // Quellpfad: Kombination aus Base-Pfad, rootKey und key
                        $sourcePath = $baseSourcePath . DIRECTORY_SEPARATOR . $rootKey . DIRECTORY_SEPARATOR . $key;
                        // Zielpfad: Der Wert aus dem JSON
                        $targetPath = $value;

                        // Prüfen, ob die Quelle existiert
                        if(!file_exists($sourcePath))
                        {
                            echo "Quelle existiert nicht: $sourcePath\n";
                            continue;
                        }

                        // Prüfen, ob der Zielpfad bereits existiert
                        if(file_exists($targetPath))
                        {
                            echo "Ziel existiert bereits: $targetPath\n";
                            continue;
                        }

                        // Verzeichnis für den Zielpfad erstellen, falls nötig
                        $targetDir = dirname($targetPath);
                        if(!is_dir($targetDir))
                        {
                            mkdir($targetDir, 0755, true);
                        }

                        // Symbolischen Link erstellen
                        if(symlink($sourcePath, $targetPath))
                        {
                            echo "Symlink erstellt: $sourcePath -> $targetPath\n";
                        }
                        else
                        {
                            echo "Fehler beim Erstellen des Symlinks: $sourcePath -> $targetPath\n";
                        }
                    }
                }
            }
        }
    }












}
