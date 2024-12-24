<?php

declare(strict_types=1);

namespace Ecxod\Symlink;

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
    function __construct()
    {
        $this->documentroot = "";

        // TODO: was sind Ordner mit Kringel ?
        $this->ordner_mit_kringel = ["popperjs"];

        // TODO: da muss fuer jeden server eine option rein oder entsprechend konfigurieren
        if (isset($_ENV['BLACK_IP']) and strval($_SERVER["SERVER_ADDR"]) === trim($_ENV['BLACK_IP'])) {
            $this->documentroot = realpath(strval(value: $_SERVER['DOCUMENT_ROOT']));
            // echo $this->documentroot;
        } elseif (!empty($_SERVER["SERVER_ADDR"]) and in_array(needle: $_SERVER["SERVER_ADDR"], haystack: explode(separator: ",", string: $_SERVER["SERVER_ARR"]), strict: true)) {
            $this->documentroot = realpath(strval(value: $_SERVER['DOCUMENT_ROOT']));
        } else {
            die("UNDOCUMENTED IP");
        }
        if (empty($this->documentroot)) {
            die("UNDOCUMENTED ROOT");
        }

        $this->workspace = realpath(strval(value: $this->documentroot . DIRECTORY_SEPARATOR . '../'));
        $this->doxygenfolder =  $this->documentroot . DIRECTORY_SEPARATOR . 'doxygen';
        $this->staticfolder =  $this->documentroot . DIRECTORY_SEPARATOR . 'static';
        $this->staticfolder_bs =  $this->staticfolder . DIRECTORY_SEPARATOR . 'bs';

        $this->create_folder_if_not_exists($this->doxygenfolder);
        $this->create_folder_if_not_exists($this->staticfolder);

        // TODO: es gibt ordner in env die sollten von einer function erzeugt werden
        $this->check_and_create_folder_if_not_exists(folder: '@popperjs', library: "@popperjs", composerFile: "package.json", vendor: "node_modules");
        $this->check_and_create_folder_if_not_exists(folder: "bs", library: "twbs/bootstrap", composerFile: "composer.json", vendor: "vendor");
        $this->check_and_create_folder_if_not_exists(folder: "css");
        $this->check_and_create_folder_if_not_exists(folder: 'phcss');
        $this->check_and_create_folder_if_not_exists(folder: 'js');
        $this->check_and_create_folder_if_not_exists(folder: 'img');

        if ($this->checkLibraryInstallation(library: "twbs/bootstrap", composerFile: "composer.json", vendor: "vendor")) {
            $this->dist = [
                'link' => $this->staticfolder_bs . DIRECTORY_SEPARATOR . 'dist',
                'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR . 'vendor/twbs/bootstrap/dist') . DIRECTORY_SEPARATOR
            ];
        } else {
            error_log("twbs/bootstrap is not in vendor\n");
            $this->dist = [];
        }

        if ($this->checkLibraryInstallation(library: "twbs/bootstrap-icons", composerFile: "composer.json", vendor: "vendor")) {
            $this->font = [
                'link' => $this->staticfolder_bs . DIRECTORY_SEPARATOR . 'font',
                'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'vendor/twbs/bootstrap-icons/font') . DIRECTORY_SEPARATOR
            ];
            $this->icons = [
                'link' => $this->staticfolder_bs . DIRECTORY_SEPARATOR . 'icons',
                'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'vendor/twbs/bootstrap-icons/icons') . DIRECTORY_SEPARATOR
            ];
        } else {
            error_log("twbs/bootstrap-icons is not in vendor");
            $this->font = [];
            $this->icons = [];
        }

        if ($this->checkLibraryInstallation(library: "jquery", composerFile: "package.json", vendor: "node_modules")) {
            $this->jquery = [
                'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'jquery',
                'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'node_modules/jquery/dist') . DIRECTORY_SEPARATOR
            ];
        } else {
            $this->jquery = [];
        }

        if ($this->checkLibraryInstallation(library: "prismjs", composerFile: "package.json", vendor: "node_modules")) {
            $this->prismjs = [
                'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'prismjs',
                'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'node_modules/prismjs') . DIRECTORY_SEPARATOR
            ];
        } else {
            $this->prismjs = [];
        }

        if ($this->checkLibraryInstallation(library: "mathjax", composerFile: "package.json", vendor: "node_modules")) {
            $this->mathjax = [
                'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'mathjax',
                'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'node_modules/mathjax') . DIRECTORY_SEPARATOR
            ];
        } else {
            $this->mathjax = [];
        }

        if ($this->checkLibraryInstallation(library: "@popperjs", composerFile: "package.json", vendor: "node_modules")) {
            $this->popperjs = [
                'link' => $this->staticfolder . DIRECTORY_SEPARATOR . '@popperjs/core',
                'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'node_modules/@popperjs/core') . DIRECTORY_SEPARATOR
            ];
        } else {
            $this->popperjs = [];
        }

        if ($this->checkLibraryInstallation(library: "tinymce", composerFile: "package.json", vendor: "node_modules")) {
            $this->tinymce = [
                'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'tinymce',
                'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'node_modules/tinymce') . DIRECTORY_SEPARATOR
            ];
        } else {
            $this->tinymce = [];
        }

        if ($this->checkLibraryInstallation(library: "chartjs", composerFile: "package.json", vendor: "node_modules")) {
            $this->chartjs = [
                'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'chartjs',
                'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'node_modules/chartjs') . DIRECTORY_SEPARATOR
            ];
        } else {
            $this->chartjs = [];
        }

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

    function check_and_create_folder_if_not_exists(
        string $folder,
        string $library = NULL,
        string $composerFile = NULL,
        string $vendor = NULL,
        int $permissions = 0755
    ): void {

        if ($folder === 'POPPERJS') {
            $folder = '@POPPERJS';
        }

        /** Wir machen den Folder weil es eine Library ist */
        if (!empty($folder) and !empty($library) and !empty($composerFile) and !empty($vendor) and $this->checkLibraryInstallation($library, $composerFile, $vendor)) {
            $completefolder =  $this->staticfolder . DIRECTORY_SEPARATOR . ltrim(strtolower($folder), 'f');
            $this->create_folder_if_not_exists(folder: $completefolder, permissions: $permissions);
            /** Wir machen den Ordner sowieso auch wenn keine Library */
        } elseif (!empty($folder) and empty($library) and empty($composerFile) and empty($vendor)) {
            $completefolder =  $this->staticfolder . DIRECTORY_SEPARATOR . ltrim(strtolower($folder), 'f');
            $this->create_folder_if_not_exists(folder: $completefolder, permissions: $permissions);
        };
    }

    function create_symlink(array $link): void
    {

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
     */
    function create_folder_if_not_exists(string $folder = "", int $permissions = 0755): void
    {
        $dirname = dirname($folder);
        foreach ($this->ordner_mit_kringel as $elem) {
            $folder = strval($this->str_replace_last("/$elem", "/@$elem", $folder));
            // wenn oben kein dirname war, probieren wir noch einmal
            $dirname ??= dirname($folder);
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
                error_log("can't write to $dirname <bR>");
                die("FILESYSTEM CRASHED");
            }
        }
    }


    /** Replace the last occurence only
     * @param string $search 
     * @param string $replace 
     * @param string|array $subject 
     * @return string|array 
     */
    function str_replace_last(string $search, string $replace, array|string $subject): array|string
    {
        $pos = strrpos(haystack: $subject, needle: $search);
        if ($pos !== false) {
            $subject = substr_replace(string: $subject, replace: $replace, offset: $pos, length: strlen($search));
        }
        return $subject;
    }

    /** 
     * Checks if a PHP Library is installed in **vendor** and is also required in **composer.json**  
     * returns *true* if installed and *false* if not installed
     * 
     * @param string $library 
     * @param string $composerFile 
     * @param string $vendor 
     * @return bool 
     */
    function checkLibraryInstallation(
        string $library,
        string $composerFile, // relative path to the composer/package-lock file
        string $vendor
    ): bool {

        $composerFileArr = ["composer.json", "package.json"];
        $vendorArr = ["vendor", "node_modules"];

        if (!in_array($composerFile, $composerFileArr)) {
            die("Can't check Libraries, composer($composerFile) not in composerFileArr(" . implode(" ", $composerFileArr) . ")");
        }
        if (!in_array($vendor, $vendorArr)) {
            die("Can't check Libraries, vendor($vendor) not in vendorArr(" . implode(" ", $vendorArr) . ")");
        }

        $vendorDir = "$this->workspace/$vendor/$library";
        $isLibraryRequired = false;
        $isLibraryInstalled = false;

        if (file_exists("$this->workspace/$composerFile") and $composerFile === "composer.json") {
            $composerContent = json_decode(file_get_contents("$this->workspace/$composerFile"), true);
            $isLibraryRequired = isset($composerContent['require'][$library]);
        } elseif (file_exists("$this->workspace/$composerFile") and $composerFile === "package.json") {
            $composerContent = json_decode(file_get_contents("$this->workspace/$composerFile"), true);
            $isLibraryRequired = isset($composerContent['dependencies'][$library]);
        } else {
            $isLibraryRequired = false;
        }

        // Check if the Bootstrap directory exists and is not empty
        $isLibraryInstalled = is_dir($vendorDir) && (new \FilesystemIterator("/$vendorDir"))->valid();

        // conclusion
        if ($isLibraryRequired && $isLibraryInstalled) {
            // echo "### $library is installed.<br>";
            return true;
        } else {
            // echo "### $library is not installed.<br>";
            return false;
        }
    }
}
