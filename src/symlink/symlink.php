<?php

declare(strict_types=1);

namespace Ecxod\Symlink;

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
    protected string $documentroot,
        $doxygenfolder,
        $staticfolder,
        $staticfolder_bs,
        $popperfolder;

    /**
     * Das ist das unteste vereichnis im git/arbeitsbereich bereich
     * @var string
     */
    protected string $workspace;

    /**
     * Das sind ordner die mit einem @ beginnen sollen wegen kompatibilität mit npm
     * @var array
     */
    protected array $ordner_mit_kringel;


    function __construct()
    {

        $this->ordner_mit_kringel = ["popperjs"];

        if (isset($_ENV['BLACK_IP']) and strval($_SERVER["SERVER_ADDR"]) === trim($_ENV['BLACK_IP'])) {
            $this->documentroot = strval(value: $_SERVER['DOCUMENT_ROOT']);
        } else {
            $this->documentroot = "/httpdocs";
            die("UNDOCUMENTED IP");
        }

        $this->workspace = strval(value: $this->documentroot . DIRECTORY_SEPARATOR . '../');
        $this->doxygenfolder =  $this->documentroot . DIRECTORY_SEPARATOR . 'doxygen';
        $this->staticfolder =  $this->documentroot . DIRECTORY_SEPARATOR . 'static';
        $this->staticfolder_bs =  $this->staticfolder . DIRECTORY_SEPARATOR . 'bs';
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

        $this->dist = [
            'link' => $this->staticfolder_bs . DIRECTORY_SEPARATOR . 'dist',
            'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR . 'vendor/twbs/bootstrap/dist') . DIRECTORY_SEPARATOR
        ];
        $this->font = [
            'link' => $this->staticfolder_bs . DIRECTORY_SEPARATOR . 'font',
            'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'vendor/twbs/bootstrap-icons/font') . DIRECTORY_SEPARATOR
        ];
        $this->icons = [
            'link' => $this->staticfolder_bs . DIRECTORY_SEPARATOR . 'icons',
            'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'vendor/twbs/bootstrap-icons/icons') . DIRECTORY_SEPARATOR
        ];
        $this->jquery = [
            'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'jquery',
            'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'node_modules/jquery/dist') . DIRECTORY_SEPARATOR
        ];
        $this->prismjs = [
            'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'prismjs',
            'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'node_modules/prismjs') . DIRECTORY_SEPARATOR
        ];
        $this->mathjax = [
            'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'mathjax',
            'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'node_modules/mathjax') . DIRECTORY_SEPARATOR
        ];
        $this->popperjs = [
            'link' => $this->staticfolder . DIRECTORY_SEPARATOR . '@popperjs/core',
            'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'node_modules/@popperjs/core') . DIRECTORY_SEPARATOR
        ];
        $this->tinymce = [
            'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'tinymce',
            'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'node_modules/tinymce') . DIRECTORY_SEPARATOR
        ];
        $this->chartjs = [
            'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'chartjs',
            'target' => realpath(path: $this->workspace . DIRECTORY_SEPARATOR  . 'node_modules/chartjs') . DIRECTORY_SEPARATOR
        ];

        ($_ENV['FDIST'] ? $this->create_symlink(link: $this->dist) : null);
        ($_ENV['FFONT'] ? $this->create_symlink(link: $this->font) : null);
        ($_ENV['FICONS'] ? $this->create_symlink(link: $this->icons) : null);
        ($_ENV['FJQUERY'] ? $this->create_symlink(link: $this->jquery) : null);
        ($_ENV['FMATHJAX'] ? $this->create_symlink(link: $this->mathjax) : null);
        ($_ENV['FPRISMJS'] ? $this->create_symlink(link: $this->prismjs) : null);
        ($_ENV['FPOPPERJS'] ? $this->create_symlink(link: $this->popperjs) : null);
        ($_ENV['FTINYMCE'] ? $this->create_symlink(link: $this->tinymce) : null);
        ($_ENV['FCHARTJS'] ? $this->create_symlink(link: $this->chartjs) : null);
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
}
