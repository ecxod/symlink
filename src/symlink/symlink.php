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
    protected array $chartjs,
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
    protected array $ordner_mit_kringel;

    /**
     * 
     * @return void 
     */
    protected array $installedLibraries;


    /** 
     * @return void
     * @author Christian Eichert <c@zp1.net>
     * @version 1.0.0
     */
    function __construct()
    {
        // clearing array installedLibraries
        $this->installedLibraries = [];

        $this->ordner_mit_kringel = ["popperjs"];

        // TODO: da muss fuer jeden server eine option rein oder entsprechend konfigurieren
        if (isset($_ENV['BLACK_IP']) and strval($_SERVER["SERVER_ADDR"]) === trim($_ENV['BLACK_IP'])) {
            $this->documentroot = strval(value: $_SERVER['DOCUMENT_ROOT']);
        } else {
            $this->documentroot = "/httpdocs";
            die("UNDOCUMENTED IP");
        }

        $this->workspace = strval(value: $this->documentroot . DIRECTORY_SEPARATOR . '../');
        $this->doxygenfolder =  $this->documentroot . DIRECTORY_SEPARATOR . 'doxygen';
        $this->staticfolder =  $this->documentroot . DIRECTORY_SEPARATOR . 'static';

        if ($this->installedLibraries["twbs/bootstrap"] and $this->checkLibraryInstallation("twbs/bootstrap")) {
            $this->staticfolder_bs =  $this->staticfolder . DIRECTORY_SEPARATOR . 'bs';
        }

        $this->popperfolder =  $this->staticfolder . DIRECTORY_SEPARATOR . '@popperjs';

        if (is_dir(filename: $this->staticfolder) and !empty(realpath(path: $this->staticfolder))) {
            $this->check_env_and_create_folder_if_not_exists(env: 'FPOPPERJS');
            $this->check_env_and_create_folder_if_not_exists(env: 'FBS');
            $this->check_env_and_create_folder_if_not_exists(env: 'FCSS');
            $this->check_env_and_create_folder_if_not_exists(env: 'FPHCSS');
            $this->check_env_and_create_folder_if_not_exists(env: 'FJS');
            $this->check_env_and_create_folder_if_not_exists(env: 'FIMG');
        } else {
            die("BAD PROBLEM : COULD NOT FIND THE STATIC FOLDERS");
        }

        if (in_array("twbs/bootstrap", $this->installedLibraries) and $this->checkLibraryInstallation(library: "twbs/bootstrap")) {
            $this->dist = [
                'link' => $this->staticfolder_bs . DIRECTORY_SEPARATOR . 'dist',
                'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR . 'vendor/twbs/bootstrap/dist') . DIRECTORY_SEPARATOR
            ];
        } else {
            $this->dist = [];
        }

        if (in_array("twbs/bootstrap-icons", $this->installedLibraries) and $this->checkLibraryInstallation(library: "twbs/bootstrap-icons")) {
            $this->font = [
                'link' => $this->staticfolder_bs . DIRECTORY_SEPARATOR . 'font',
                'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'vendor/twbs/bootstrap-icons/font') . DIRECTORY_SEPARATOR
            ];
            $this->icons = [
                'link' => $this->staticfolder_bs . DIRECTORY_SEPARATOR . 'icons',
                'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'vendor/twbs/bootstrap-icons/icons') . DIRECTORY_SEPARATOR
            ];
        } else {
            $this->font = [];
            $this->icons = [];
        }

        if (in_array("jquery", $this->installedLibraries) and $this->checkLibraryInstallation(library: "jquery")) {
            $this->jquery = [
                'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'jquery',
                'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'node_modules/jquery/dist') . DIRECTORY_SEPARATOR
            ];
        } else {
            $this->jquery = [];
        }

        if (in_array("prismjs", $this->installedLibraries) and $this->checkLibraryInstallation(library: "prismjs")) {
            $this->prismjs = [
                'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'prismjs',
                'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'node_modules/prismjs') . DIRECTORY_SEPARATOR
            ];
        } else {
            $this->prismjs = [];
        }


        if (in_array("mathjax", $this->installedLibraries) and $this->checkLibraryInstallation(library: "mathjax")) {
            $this->mathjax = [
                'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'mathjax',
                'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'node_modules/mathjax') . DIRECTORY_SEPARATOR
            ];
        } else {
            $this->mathjax = [];
        }

        if (in_array("@popperjs", $this->installedLibraries) and $this->checkLibraryInstallation(library: "@popperjs")) {
            $this->popperjs = [
                'link' => $this->staticfolder . DIRECTORY_SEPARATOR . '@popperjs/core',
                'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'node_modules/@popperjs/core') . DIRECTORY_SEPARATOR
            ];
        } else {
            $this->popperjs = [];
        }

        if (in_array("tinymce", $this->installedLibraries) and $this->checkLibraryInstallation(library: "tinymce")) {
            $this->tinymce = [
                'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'tinymce',
                'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'node_modules/tinymce') . DIRECTORY_SEPARATOR
            ];
        } else {
            $this->tinymce = [];
        }

        if (in_array("chartjs", $this->installedLibraries) and $this->checkLibraryInstallation(library: "chartjs")) {
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

    function check_env_and_create_folder_if_not_exists(string|bool $env = null, int $permissions = 0755): void
    {
        if ($env === 'POPPERJS') {
            $env = '@POPPERJS';
        }
        if (isset($_ENV[$env]) and boolval($_ENV[$env]) === true) {
            $folder =  $this->staticfolder . DIRECTORY_SEPARATOR . ltrim(strtolower($env), 'f');
            $this->create_folder_if_not_exists(folder: $folder, permissions: $permissions);
        };
    }

    function create_symlink(array $link): void
    {

        if (is_link(filename: $link['link']) and readlink(path: $link['link']) ===  $link['target']) {
            return;
        }

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
                echo "can't write to $dirname <bR>";
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
        string $library = "twbs/bootstrap",
        string $composerFile = "composer.json", // relative path to the composer/package-lock file
        string $vendor = "vendor"
    ): bool {

        $composerFileArr = ["composer.json", "package.json"];
        $vendorArr = ["vendor", "node_modules"];

        if (in_array($vendor, $vendorArr) and in_array($composerFile, $composerFileArr)) {
            die("Can't check Libraries");
        }

        $vendorDir = "$vendor/$library";
        $isLibraryRequired = false;
        $isLibraryInstalled = false;

        // Check if Library is listed in composer.json
        if (file_exists($composerFile) and $composerFile === "composer.json") {
            $composerContent = json_decode(file_get_contents($composerFile), true);
            $isLibraryRequired = isset($composerContent['require'][$library]);
            // Check if Library is listed in package.json
        } elseif (file_exists($composerFile) and $composerFile === "package.json") {
            $composerContent = json_decode(file_get_contents($composerFile), true);
            $isLibraryRequired = isset($composerContent['dependencies'][$library]);
        } else {
            $isLibraryRequired = false;
        }

        // Check if the Bootstrap directory exists and is not empty
        $isLibraryInstalled = is_dir($vendorDir) && (new \FilesystemIterator($vendorDir))->valid();

        // conclusion
        if ($isLibraryRequired && $isLibraryInstalled) {
            if (function_exists('logg')) {
                logg("### $library is installed.");
            }
            addIfNotExists($this->installedLibraries, $library);
            return true;
        } else {
            if (function_exists('logg')) {
                logg("### $library is not installed.");
            }
            return false;
        }
    }
}
