<?php

declare(strict_types=1);

namespace Ecxod\Symlink;

use \FilesystemIterator;
use function Ecxod\Funktionen\{m, logg, addIfNotExists};

/** 
 * IMPORTANT : 
 * To grant write and execute permissions on the /raid/home/christian/wdrive/buchungssatz/public/static 
 * directory to the www-data user (as root)
 * setfacl -m u:www-data:rwX /raid/home/christian/wdrive/buchungssatz/public/static
 * 
 * install jquery
 * christian@black:/raid/home/christian/wdrive/buchungssatz $ npm install jquery
 * 
 * @package symlink 
 * @author Christian Eichert <c@zp1.net>
 * @version 1.0.0
 */
class symlink
{
    /**
     * Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @var array
     */
    protected array
        $chartjs,
        $dist,
        $font,
        $icons,
        $jquery,
        $prismjs,
        $popperjs,
        $mathjax,
        $tinymce;

    /**
     * Das sind string-werte der ordner in den die symlinks erstellt werden sollen
     * @var string
     */
    protected string
        $workspace,
        $documentroot,
        $doxygenfolder,
        $staticfolder,
        $staticfolder_bs,
        $popperfolder;

    /**
     * Das sind ordner die mit einem @ beginnen sollen wegen kompatibilität mit npm
     * @var array
     */
    protected array
        $ordner_mit_kringel;

    /** 
     * @return void
     * @author Christian Eichert <c@zp1.net>
     * @version 1.0.0
     */
    public function __construct()
    {
        $this->documentroot = "";

        // TODO: was sind Ordner mit Kringel ?
        $this->ordner_mit_kringel = ["popperjs"];

        // TODO: da muss fuer jeden server eine option rein oder entsprechend konfigurieren
        if (isset($_ENV['BLACK_IP']) and strval($_SERVER["SERVER_ADDR"]) === trim($_ENV['BLACK_IP'])) {
            $this->documentroot = realpath(strval(value: $_SERVER['DOCUMENT_ROOT']));
            // echo $this->documentroot;
        } elseif (!empty($_SERVER["SERVER_ADDR"]) and in_array(needle: $_SERVER["SERVER_ADDR"], haystack: explode(separator: ",", string: $_ENV["SERVER_ARR"]), strict: true)) {
            $this->documentroot = realpath(strval(value: $_SERVER['DOCUMENT_ROOT']));
        } else {
            die("UNDOCUMENTED IP");
        }
        if (empty($this->documentroot)) {
            die("UNDOCUMENTED ROOT");
        }

        $this->workspace = realpath(path: strval(value: $this->documentroot . DIRECTORY_SEPARATOR . '../'));
        $this->doxygenfolder = $this->documentroot . DIRECTORY_SEPARATOR . 'doxygen';
        $this->staticfolder = $this->documentroot . DIRECTORY_SEPARATOR . 'static';
        $this->staticfolder_bs = $this->staticfolder . DIRECTORY_SEPARATOR . 'bs';

        $this->create_folder_if_not_exists(folder: $this->doxygenfolder);
        $this->create_folder_if_not_exists(folder: $this->staticfolder);

        // TODO: es gibt ordner in env die sollten von einer function erzeugt werden
        $this->check_and_create_folder_if_not_exists(folder: '@popperjs', library: "@popperjs", composerFile: "package.json", vendor: "node_modules");
        $this->check_and_create_folder_if_not_exists(folder: "bs", library: "twbs/bootstrap", composerFile: "composer.json", vendor: "vendor");
        $this->check_and_create_folder_if_not_exists(folder: "css");
        $this->check_and_create_folder_if_not_exists(folder: 'phcss');
        $this->check_and_create_folder_if_not_exists(folder: 'js');
        $this->check_and_create_folder_if_not_exists(folder: 'img');

        $this->dist = ($this->checkLibraryInstallation(library: "twbs/bootstrap", composerFile: "composer.json", vendor: "vendor")) ? [
            'link' => $this->staticfolder_bs . DIRECTORY_SEPARATOR . 'dist',
            'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR . 'vendor/twbs/bootstrap/dist') . DIRECTORY_SEPARATOR
        ] : [];

        $this->font = ($this->checkLibraryInstallation(library: "twbs/bootstrap-icons", composerFile: "composer.json", vendor: "vendor")) ? [
            'link' => $this->staticfolder_bs . DIRECTORY_SEPARATOR . 'font',
            'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR . 'vendor/twbs/bootstrap-icons/font') . DIRECTORY_SEPARATOR
        ] : [];

        $this->icons = ($this->checkLibraryInstallation(library: "twbs/bootstrap-icons", composerFile: "composer.json", vendor: "vendor")) ? [
            'link' => $this->staticfolder_bs . DIRECTORY_SEPARATOR . 'icons',
            'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR . 'vendor/twbs/bootstrap-icons/icons') . DIRECTORY_SEPARATOR
        ] : [];

        $this->jquery = ($this->checkLibraryInstallation(library: "jquery", composerFile: "package.json", vendor: "node_modules")) ? [
            'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'jquery',
            'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR . 'node_modules/jquery/dist') . DIRECTORY_SEPARATOR
        ] : [];

        $this->prismjs = ($this->checkLibraryInstallation(library: "prismjs", composerFile: "package.json", vendor: "node_modules")) ? [
            'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'prismjs',
            'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR . 'node_modules/prismjs') . DIRECTORY_SEPARATOR
        ] : [];

        $this->mathjax = ($this->checkLibraryInstallation(library: "mathjax", composerFile: "package.json", vendor: "node_modules")) ? [
            'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'mathjax',
            'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR . 'node_modules/mathjax') . DIRECTORY_SEPARATOR
        ] : [];

        $this->popperjs = ($this->checkLibraryInstallation(library: "@popperjs", composerFile: "package.json", vendor: "node_modules")) ? [
            'link' => $this->staticfolder . DIRECTORY_SEPARATOR . '@popperjs/core',
            'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR . 'node_modules/@popperjs/core') . DIRECTORY_SEPARATOR
        ] : [];

        $this->tinymce = ($this->checkLibraryInstallation(library: "tinymce", composerFile: "package.json", vendor: "node_modules")) ? [
            'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'tinymce',
            'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR . 'node_modules/tinymce') . DIRECTORY_SEPARATOR
        ] : [];

        $this->chartjs = ($this->checkLibraryInstallation(library: "chartjs", composerFile: "package.json", vendor: "node_modules")) ? [
            'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'chartjs',
            'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR . 'node_modules/chartjs') . DIRECTORY_SEPARATOR
        ] : [];

        (empty($this->dist) ? null : $this->create_symlink(link: $this->dist));
        (empty($this->font) ? null : $this->create_symlink(link: $this->font));
        (empty($this->icons) ? null : $this->create_symlink(link: $this->icons));
        (empty($this->jquery) ? null : $this->create_symlink(link: $this->jquery));
        (empty($this->mathjax) ? null : $this->create_symlink(link: $this->mathjax));
        (empty($this->prismjs) ? null : $this->create_symlink(link: $this->prismjs));
        (empty($this->popperjs) ? null : $this->create_symlink(link: $this->popperjs));
        (empty($this->tinymce) ? null : $this->create_symlink(link: $this->tinymce));
        (empty($this->chartjs) ? null : $this->create_symlink(link: $this->chartjs));
    }

    /**
     * @param string $folder 
     * @param string|null $library 
     * @param string|null $composerFile 
     * @param string|null $vendor 
     * @param int $permissions 
     * @return void 
     * @author Christian Eichert <c@zp1.net>
     * @version 1.0.0
     */
    public function check_and_create_folder_if_not_exists(
        string $folder,
        string $library = null,
        string $composerFile = null,
        string $vendor = null,
        int $permissions = 0755
    ): void {

        if ($folder === 'POPPERJS') {
            $folder = '@POPPERJS';
        }

        /** Wir machen den Folder weil es eine Library ist */
        if (!empty($folder) and !empty($library) and !empty($composerFile) and !empty($vendor) and $this->checkLibraryInstallation($library, $composerFile, $vendor)) {
            $completefolder = $this->staticfolder . DIRECTORY_SEPARATOR . ltrim(string: strtolower(string: $folder), characters: 'f');
            $this->create_folder_if_not_exists(folder: $completefolder, permissions: $permissions);
            /** Wir machen den Ordner sowieso auch wenn keine Library */
        } elseif (!empty($folder) and empty($library) and empty($composerFile) and empty($vendor)) {
            $completefolder = $this->staticfolder . DIRECTORY_SEPARATOR . ltrim(string: strtolower(string: $folder), characters: 'f');
            $this->create_folder_if_not_exists(folder: $completefolder, permissions: $permissions);
        };
    }

    /**
     * @param array $link 
     * @return void 
     * @author Christian Eichert <c@zp1.net>
     * @version 1.0.0
     */
    public function create_symlink(
        array $link
    ): void {

        // if (is_link(filename: $link['link']) and readlink(path: $link['link']) ===  $link['target']) {
        //     return;
        // }

        if (
            // wenn es noch keinen link gibt oder wenn der link nicht auf target zielt
            !is_link(filename: $link['link'])
            and
            // wenn es aber ein target gibt
            (!empty(realpath(path: $link['target'])) and realpath(path: $link['target']) !== '/')

        ) {
            try {
                symlink(target: $link['target'], link: $link['link']);
            } catch (\Throwable $e) {
                logg($e);
                \Sentry\captureException($e);
            }
        }
    }

    /** The method does 2 things :
     * - create a folder if not exists
     * - rename the _folder_ in _@folder_ if he is the ordner_mit_kringel array
     * @param string $folder 
     * @param int $permissions 
     * @return void 
     * @author Christian Eichert <c@zp1.net>
     * @version 1.0.0
     */
    public function create_folder_if_not_exists(
        string $folder = "",
        int $permissions = 0755
    ): void {
        $dirname = dirname($folder);
        foreach ($this->ordner_mit_kringel as $elem) {
            $folder = strval(value: $this->str_replace_last(search: "/replace: $elem", replace: "/@$elem", subject: $folder));
            // wenn oben kein dirname war, probieren wir noch einmal
            $dirname ??= dirname(path: $folder);
        }
        // does the folder exist? if yes skip
        if (!is_dir(filename: $folder) or empty(realpath(path: $folder))) {
            // ist the parent writable ?
            if (!empty($dirname) and is_dir(filename: $dirname) and is_writable(filename: $dirname)) {
                try {
                    mkdir(directory: $folder, permissions: $permissions, recursive: true);
                } catch (\Throwable $exception) {
                    \Sentry\captureException($exception);
                }
            } else {
                error_log(message: "can't write to $dirname <bR>");
                die("FILESYSTEM CRASHED");
            }
        }
    }


    /** Replace the last occurence only
     * @param string $search 
     * @param string $replace 
     * @param string|array $subject 
     * @return string|array 
     * @author Christian Eichert <c@zp1.net>
     * @version 1.0.0
     */
    public function str_replace_last(
        string $search,
        string $replace,
        array|string $subject
    ) {
        $pos = strrpos(haystack: $subject, needle: $search);
        return ($pos !== false) ?
            substr_replace(string: $subject, replace: $replace, offset: $pos, length: strlen($search)) :
            $subject;
    }

    /** 
     * Checks if a PHP Library is installed in **vendor** and is also required in **composer.json**  
     * returns *true* if installed and *false* if not installed
     * 
     * @param string $library 
     * @param string $composerFile 
     * @param string $vendor 
     * @return bool 
     * @author Christian Eichert <c@zp1.net>
     * @version 1.0.0
     */
    public function checkLibraryInstallation(
        string $library,
        string $composerFile, // relative path to the composer/package-lock file
        string $vendor
    ): bool {

        $composerFileArr = ["composer.json", "package.json"];
        $vendorArr = ["vendor", "node_modules"];

        if (!in_array($composerFile, $composerFileArr)) {
            die("Can't check Libraries, composer($composerFile) not in composerFileArr(" . implode(separator: " ", array: $composerFileArr) . ")");
        }
        if (!in_array($vendor, $vendorArr)) {
            die("Can't check Libraries, vendor($vendor) not in vendorArr(" . implode(separator: " ", array: $vendorArr) . ")");
        }

        $vendorDir = "$this->workspace/$vendor/$library";
        $isLibraryRequired = false;
        $isLibraryInstalled = false;

        if (file_exists("$this->workspace/$composerFile") and $composerFile === "composer.json") {
            $composerContent = json_decode(json: file_get_contents(filename: "$this->workspace/$composerFile"), associative: true);
            $isLibraryRequired = isset($composerContent['require'][$library]);
        } elseif (file_exists("$this->workspace/$composerFile") and $composerFile === "package.json") {
            $composerContent = json_decode(json: file_get_contents(filename: "$this->workspace/$composerFile"), associative: true);
            $isLibraryRequired = isset($composerContent['dependencies'][$library]);
        } else {
            $isLibraryRequired = false;
        }

        // Check if the Bootstrap directory exists and is not empty
        $isLibraryInstalled = is_dir(filename: $vendorDir) && (new FilesystemIterator(directory: "/$vendorDir", flags: FilesystemIterator::SKIP_DOTS))->valid();

        // conclusion
        if ($isLibraryRequired && $isLibraryInstalled) {
            // echo "### $library is installed.<br>";
            return true;
        } else {
            // echo "### $library is not installed.<br>";
            return false;
        }
    }
    
    /**
     * Check if the vendor exists and is readable
     * @param string $vendor 
     * @return bool 
     * @author Christian Eichert <c@zp1.net>
     * @version 1.0.0
     */
    public static function detect_vendor(string $vendor = null): bool
    {
        $vendor ??= \strval($_SERVER['VENDOR']);
        $vendor ??= \strval(realpath($_SERVER['DOCUMENT_ROOT'] . '/vendor'));
        if(is_readable(filename: realpath(path: $vendor))) {
            $_SERVER['VENDOR_EXISTS'] = true;
            return true;
        }
        else {
            $_SERVER['VENDOR_EXISTS'] = false;
            return false;
        }
    }

   /**
     * return all installed pakets of a framework, or false if none
     * 
     * @param string $subfolder default : ecxod
     * @param string $vendor default : $_SERVER['VENDOR']
     * @return array|bool|string
     * @author Christian Eichert <c@zp1.net>
     * @version 1.0.0
     */
    public function detect_framework_components($subfolder = "ecxod", string $vendor = null, string $output="array"): array|bool|string
    {
        $vendor ??= \strval($_SERVER['VENDOR']);
        $vendor ??= \strval(realpath($_SERVER['DOCUMENT_ROOT'] . '/vendor'));

        $vendorfolder_array = ["vendor", "node_modules"];
        $outputarray = ["array","json","csv"];


        foreach($vendorfolder_array as $folder) {
            if(self::detect_vendor(vendor: $vendor) and str_contains(haystack: $vendor, needle: $folder)) {
                $subfolderpath = realpath(path: $_SERVER['VENDOR'] . DIRECTORY_SEPARATOR . $subfolder);

                if(is_readable(filename: $vendor) and is_readable(filename: $subfolderpath)) {
                    // Alle Dateien und Ordner einlesen
                    $result = scandir(directory: $subfolderpath, sorting_order: SCANDIR_SORT_ASCENDING) ?? [];
                    // wenn nichts ausser . und .. dann leere array (kein fehler kein false kein scheiss)
                    $directories = array_values(
                        array_diff(
                            is_array(value: $result) ? $result : [],
                            ['..', '.']
                        )
                    );
                    // Ausgangsformat erzeugen
                    if(in_array(needle: $output,haystack: $outputarray) and gettype($directories) === 'array') {
                        if($output ==="array"){
                            return $directories;
                        }elseif($output === "json"){
                            return json_encode(value: $directories);
                        }elseif($output === "csv"){
                            return implode(separator: ",",array: $directories);
                        }
                    }
                }
            }
        }
        return false;
    }

}
