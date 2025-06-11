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
    protected const require      = "require";
    protected const dependencies = "dependencies";

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
        $this->composer_json        = 'composer.json';
        $this->package_json         = 'package.json';
        $this->symlink_json         = 'symlink.json';
        $this->symlink_example_json = 'symlink-example.json';

        // erzeugen die example json
        $this->create_symlink_example();
        // erzeugen die Links
        $this->setSymlinksFromJson();

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


    protected function get_json_from_json(string $jsonfile)
    {
        $json = $this->getWorkspace() . DIRECTORY_SEPARATOR . $jsonfile;
        if(!file_exists($json) || !is_readable($json))
        {
            echo "Bitte erstellen Sie : $jsonfile\n";
            throw new RuntimeException("$jsonfile nicht gefunden oder nicht lesbar");
        }
        return $json;
    }

    protected function get_string_from_json(string $json, string $jsonfile)
    {
        $string = file_get_contents($json);
        if($string === false)
        {
            throw new RuntimeException("Konnte $jsonfile nicht lesen");
        }
        return $string;
    }

    protected function get_obj_from_json(mixed $string, string $jsonfile)
    {
        $obj = json_decode($string, true, 512, JSON_INVALID_UTF8_SUBSTITUTE);
        if(json_last_error() !== JSON_ERROR_NONE)
        {
            throw new JsonException("Ungültiges JSON in $jsonfile: " . json_last_error_msg());
        }
        return $obj;
    }

    protected function get_object(string $jsonfile)
    {
        $json   = $this->get_json_from_json($jsonfile);
        $string = $this->get_string_from_json($json, $jsonfile);
        $obj    = $this->get_obj_from_json($string, $jsonfile);
        return $obj;
    }




    protected function create_symlink_example()
    {
        try
        {
            $composer_obj = $this->get_object($this->composer_json);
            $package_obj  = $this->get_object($this->package_json);

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
            $symlink_example_object   = null;
            $symlink_example_filename = $this->getWorkspace() . DIRECTORY_SEPARATOR . $this->symlink_example_json;

            if(file_exists($symlink_example_filename) && is_readable($symlink_example_filename))
            {
                $symlink_example_content = file_get_contents($symlink_example_filename);
                if($symlink_example_content === false)
                {
                    throw new RuntimeException("Konnte $this->symlink_example_json nicht lesen");
                }

                $symlink_example_object = json_decode($symlink_example_content, true, 512, JSON_INVALID_UTF8_SUBSTITUTE);
                if(json_last_error() !== JSON_ERROR_NONE)
                {
                    throw new JsonException("Ungültiges JSON in $this->symlink_example_json: " . json_last_error_msg());
                }
                // Vergleiche die Daten direkt, um doppelte Kodierung zu vermeiden
                if($symlink_data === $symlink_example_object)
                {
                    return; // Datei ist aktuell
                }
            }

            // Prüfe Schreibrechte
            $symlink_dir = dirname($symlink_example_filename);
            if(!is_writable($symlink_dir))
            {
                throw new RuntimeException("Verzeichnis für $this->symlink_example_json ist nicht beschreibbar");
            }



            // Kodiere die Daten nur einmal und schreibe die Ausgabedatei
            $symlink_json = json_encode($symlink_data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
            $symlink_json = str_replace('\/', '/', $symlink_json);

            if(file_put_contents($symlink_example_filename, $symlink_json) === false)
            {
                throw new RuntimeException("Konnte $this->symlink_example_json nicht schreiben");
            }
        }
        catch ( \Exception $e )
        {
            error_log("Fehler: " . $e->getMessage());
            exit("Ein Fehler ist aufgetreten: " . $e->getMessage());
        }
    }



    public function setSymlinksFromJson()
    {
        $symlink_text = $this->get_json_from_json($this->symlink_json);

        // Check if Source exists
        if(!file_exists($this->get_json_from_json($this->symlink_json)))
            echo "Bitte erstellen Sie : $this->symlink_json\n";

        $symlink_content = $this->get_string_from_json($symlink_text, $this->symlink_json);
        $symlink_object  = $this->get_obj_from_json($symlink_content, $this->symlink_json);
        if($symlink_object === null)
            echo "Ungueltiges JSON-Format: $this->symlink_json\n";

        foreach($symlink_object as $Key1 => $Value1)
        {
            if(is_array($Value1))
            {
                foreach($Value1 as $Key2 => $Value2)
                {
                    if(is_array($Value2))
                    {
                        foreach($Value2 as $Key3 => $Value3)
                        {
                            $prefix = "public/";
                            // Prüfen, ob der Wert mit "public" beginnt
                            if(is_string($Value3) && strpos($Value3, $prefix) === 0)
                            {
                                // Quellpfad: Kombination aus Base-Pfad, rootKey und key
                                $sourcePath = $this->getWorkspace() . DIRECTORY_SEPARATOR . $Key1 . DIRECTORY_SEPARATOR . $Key2 . DIRECTORY_SEPARATOR . $Key3;
                                // Zielpfad: Der Wert aus dem JSON
                                $targetPath = $Value3;
                                // wir erzeugen kein zweites public ;-)
                                $targetPath_ohne_prefix = substr($targetPath, strlen($prefix));

                                // Prüfen, ob die Quelle existiert
                                if(!empty($targetPath_ohne_prefix) and !file_exists($sourcePath))
                                {
                                    // echo "Quelle existiert nicht: $sourcePath\n";
                                    continue;
                                }

                                // Prüfen, ob der Zielpfad bereits existiert
                                if(empty($targetPath_ohne_prefix) or file_exists($targetPath_ohne_prefix))
                                {
                                    // echo "Ziel existiert bereits: $targetPath\n";
                                    continue;
                                }

                                // Verzeichnis für den Zielpfad erstellen, falls nötig
                                $targetDir = dirname($targetPath_ohne_prefix);
                                if(!is_dir($targetDir))
                                {
                                    mkdir($targetDir, 0777, true);
                                }

                                // Symbolischen Link erstellen
                                if(!empty($targetPath_ohne_prefix) and symlink($sourcePath, $targetPath_ohne_prefix))
                                {
                                    // echo "Symlink erstellt: $sourcePath -> $targetPath\n";
                                    continue;
                                }
                                else
                                {
                                    //echo "Fehler beim Erstellen des Symlinks: $sourcePath -> $targetPath\n";
                                    continue;
                                }

                            }
                        }
                    }
                }
            }
        }
    }
}
