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
    protected array $dist;
    protected array $font;
    protected array $icons;
    protected array $jquery;
    protected array $prismjs;
    protected array $popperjs;
    protected array $mathjax;
    protected string $documentroot;
    protected string $doxygenfolder;
    protected string $staticfolder;

    function __construct()
    {
        // /var/projekte/webroot/buchungssatz/public/static
        $this->documentroot = strval(value: realpath(path: $_SERVER['DOCUMENT_ROOT']));
        $this->doxygenfolder =  $this->documentroot . DIRECTORY_SEPARATOR . 'doxygen';
        $this->staticfolder =  $this->documentroot . DIRECTORY_SEPARATOR . 'static';
        $this->create_folder_if_not_exists(folder: $this->doxygenfolder);
        $this->create_folder_if_not_exists(folder: $this->staticfolder);

        if (!is_dir(filename: $this->staticfolder) and empty(realpath(path: $this->staticfolder))) {
            $this->check_env_and_create_folder_if_not_exists(env: 'bs');       // folder => link {dist, font, icons}
            $this->check_env_and_create_folder_if_not_exists(env: 'css');      // manual use
            $this->check_env_and_create_folder_if_not_exists(env: 'phcss');    // manual use
            $this->check_env_and_create_folder_if_not_exists(env: 'js');       // manual use
            $this->check_env_and_create_folder_if_not_exists(env: 'img');      // manual use
            $this->check_env_and_create_folder_if_not_exists(env: 'mathjax');  // link
            $this->check_env_and_create_folder_if_not_exists(env: 'popperjs'); // link
            $this->check_env_and_create_folder_if_not_exists(env: 'prismjs');  // link
        } else {
            die("BAD PROBLEM : COULD NOT FIND THE STATIC FOLDERS");
        }

        // die('\$this->static=' . $this->static);

        $this->dist = [
            'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'bs/dist',
            'target' => realpath(path: $_SERVER['DOCUMENT_ROOT'] . '/../vendor/twbs/bootstrap/dist') . DIRECTORY_SEPARATOR
        ];
        // print_r($this->bs);die();
        $this->font = [
            'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'bs/font',
            'target' => realpath(path: $_SERVER['DOCUMENT_ROOT'] . '/../vendor/twbs/bootstrap-icons/font') . DIRECTORY_SEPARATOR
        ];
        $this->icons = [
            'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'bs/icons',
            'target' => realpath(path: $_SERVER['DOCUMENT_ROOT'] . '/../vendor/twbs/bootstrap-icons/icons') . DIRECTORY_SEPARATOR
        ];
        $this->jquery = [
            'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'jquery',
            'target' => realpath(path: $_SERVER['DOCUMENT_ROOT'] . '/../node_modules/jquery/dist') . DIRECTORY_SEPARATOR
        ];
        $this->prismjs = [
            'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'prismjs',
            'target' => realpath(path: $_SERVER['DOCUMENT_ROOT'] . '/../node_modules/prismjs') . DIRECTORY_SEPARATOR
        ];
        $this->mathjax = [
            'link' => $this->staticfolder . DIRECTORY_SEPARATOR . 'mathjax',
            'target' => realpath(path: $_SERVER['DOCUMENT_ROOT'] . '/../node_modules/mathjax') . DIRECTORY_SEPARATOR
        ];
        $this->popperjs = [
            'link' => $this->staticfolder . DIRECTORY_SEPARATOR . '@popperjs/core',
            'target' => realpath(path: $_SERVER['DOCUMENT_ROOT'] . '/../node_modules/@popperjs/core') . DIRECTORY_SEPARATOR
        ];

        ($_ENV['dist'] ? $this->create_symlink(link: $this->dist) : null);
        ($_ENV['font'] ? $this->create_symlink(link: $this->font) : null);
        ($_ENV['icons'] ? $this->create_symlink(link: $this->icons) : null);
        ($_ENV['jquery'] ? $this->create_symlink(link: $this->jquery) : null);
        ($_ENV['mathjax'] ? $this->create_symlink(link: $this->mathjax) : null);
        ($_ENV['prismjs'] ? $this->create_symlink(link: $this->prismjs) : null);
        ($_ENV['popperjs'] ? $this->create_symlink(link: $this->popperjs) : null);
    }

    function check_env_and_create_folder_if_not_exists(string|bool $env = null, int $permissions = 0755): void
    {
        if (isset($_ENV[$env]) and $_ENV[$env] === true) {
            $folder =  $this->staticfolder . DIRECTORY_SEPARATOR . $env;
            $this->create_folder_if_not_exists(folder: $folder, permissions: $permissions);
        }
    }

    function create_symlink(array $link): void
    {
        if (
            // wenn es noch keinen link gibt
            !is_link(filename: $link['link'])
            and
            // wenn es aber ein target gibt
            (!empty(realpath(path: $link['target'])) and realpath(path: $link['target']) !== '/')
            and
            // wenn der link nicht auf target zielt
            readlink(path: $link['link']) !==  $link['target']
        ) {
            try {
                symlink(target: $link['target'], link: $link['link']);
            } catch (\Throwable $e) {
                \Sentry\captureException($e);
            }
        }
    }

    function create_folder_if_not_exists(string $folder, int $permissions = 0755): void
    {

        if (!is_dir(filename: $folder) and empty(realpath(path: $folder))) {

            if (!is_writable(filename: $_SERVER['DOCUMENT_ROOT'])) {
                die('can\'t write to DOCUMENT_ROOT');
            } else {
                try {
                    mkdir(directory: $folder, permissions: $permissions, recursive: true);
                } catch (\Throwable $exception) {
                    \Sentry\captureException($exception);
                }
            }
        }
    }
}
