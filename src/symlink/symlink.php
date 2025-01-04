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
    protected array $chartjs;
    protected array $dist;
    protected array $font;
    protected array $icons;
    protected array $jquery;
    protected array $prismjs;
    protected array $popperjs;
    protected array $mathjax;
    protected array $tinymce;
    protected array $vendormodule;
    protected array $nodemodule;
    protected array $projektarr;

    /**
     * Das sind string-werte der ordner in den die symlinks erstellt werden sollen
     * @var string
     */
    protected string $workspace;
    protected string $documentroot;
    protected string $doxygenfolder;
    protected string $staticfolder;
    protected string $staticfolder_bs;
    protected string $popperfolder;

    /**
     * Das sind ordner die mit einem @ beginnen sollen wegen kompatibilität mit npm
     * @var array
     */
    protected array $ordner_mit_kringel;

    /**
     * Das sind string-werte der ordner in den die symlinks erstellt werden sollen
     * @return string
     */
    public function getWorkspace(): string
    {
        return $this->workspace;
    }

    /**
     * Das sind string-werte der ordner in den die symlinks erstellt werden sollen
     * @param string $workspace Das sind string-werte der ordner in den die symlinks erstellt werden sollen
     * @return self
     */
    public function setWorkspace(string $workspace): self
    {
        $this->workspace = $workspace;
        return $this;
    }

    /**
     * Das sind string-werte der ordner in den die symlinks erstellt werden sollen
     * @return string
     */
    public function getDocumentroot(): string
    {
        return $this->documentroot;
    }

    /**
     * Das sind string-werte der ordner in den die symlinks erstellt werden sollen
     * @param string $documentroot Das sind string-werte der ordner in den die symlinks erstellt werden sollen
     * @return self
     */
    public function setDocumentroot(string $documentroot): self
    {
        $this->documentroot = $documentroot;
        return $this;
    }

    /**
     * Das sind string-werte der ordner in den die symlinks erstellt werden sollen
     * @return string
     */
    public function getDoxygenfolder(): string
    {
        return $this->doxygenfolder;
    }

    /**
     * Das sind string-werte der ordner in den die symlinks erstellt werden sollen
     * @param string $doxygenfolder Das sind string-werte der ordner in den die symlinks erstellt werden sollen
     * @return self
     */
    public function setDoxygenfolder(string $doxygenfolder): self
    {
        $this->doxygenfolder = $doxygenfolder;
        return $this;
    }

    /**
     * Das sind string-werte der ordner in den die symlinks erstellt werden sollen
     * @return string
     */
    public function getStaticfolder(): string
    {
        return $this->staticfolder;
    }

    /**
     * Das sind string-werte der ordner in den die symlinks erstellt werden sollen
     * @param string $staticfolder Das sind string-werte der ordner in den die symlinks erstellt werden sollen
     * @return self
     */
    public function setStaticfolder(string $staticfolder): self
    {
        $this->staticfolder = $staticfolder;
        return $this;
    }

    /**
     * Das sind string-werte der ordner in den die symlinks erstellt werden sollen
     * @return string
     */
    public function getStaticfolderBs(): string
    {
        return $this->staticfolder_bs;
    }

    /**
     * Das sind string-werte der ordner in den die symlinks erstellt werden sollen
     * @param string $staticfolder_bs Das sind string-werte der ordner in den die symlinks erstellt werden sollen
     * @return self
     */
    public function setStaticfolderBs(string $staticfolder_bs): self
    {
        $this->staticfolder_bs = $staticfolder_bs;
        return $this;
    }

    /**
     * Das sind string-werte der ordner in den die symlinks erstellt werden sollen
     * @return string
     */
    public function getPopperfolder(): string
    {
        return $this->popperfolder;
    }

    /**
     * Das sind string-werte der ordner in den die symlinks erstellt werden sollen
     * @param string $popperfolder Das sind string-werte der ordner in den die symlinks erstellt werden sollen
     * @return self
     */
    public function setPopperfolder(string $popperfolder): self
    {
        $this->popperfolder = $popperfolder;
        return $this;
    }

    /**
     * Das sind ordner die mit einem @ beginnen sollen wegen kompatibilität mit npm
     * @return array
     */
    public function getOrdnerMitKringel(): array
    {
        return $this->ordner_mit_kringel;
    }

    /**
     * Das sind ordner die mit einem @ beginnen sollen wegen kompatibilität mit npm
     * @param array $ordner_mit_kringel Das sind ordner die mit einem @ beginnen sollen wegen kompatibilität mit npm
     * @return self
     */
    public function setOrdnerMitKringel(array $ordner_mit_kringel): self
    {
        $this->ordner_mit_kringel = $ordner_mit_kringel;
        return $this;
    }

    /**
     * Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @return array
     */
    public function getChartjs(): array
    {
        return $this->chartjs;
    }

    /**
     * Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @param array $chartjs Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @return self
     */
    public function setChartjs(array $chartjs): self
    {
        $this->chartjs = $chartjs;
        return $this;
    }

    /**
     * Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @return array
     */
    public function getDist(): array
    {
        return $this->dist;
    }

    /**
     * Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @param array $dist Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @return self
     */
    public function setDist(array $dist): self
    {
        $this->dist = $dist;
        return $this;
    }

    /**
     * Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @return array
     */
    public function getFont(): array
    {
        return $this->font;
    }

    /**
     * Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @param array $font Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @return self
     */
    public function setFont(array $font): self
    {
        $this->font = $font;
        return $this;
    }

    /**
     * Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @return array
     */
    public function getIcons(): array
    {
        return $this->icons;
    }

    /**
     * Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @param array $icons Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @return self
     */
    public function setIcons(array $icons): self
    {
        $this->icons = $icons;
        return $this;
    }

    /**
     * Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @return array
     */
    public function getJquery(): array
    {
        return $this->jquery;
    }

    /**
     * Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @param array $jquery Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @return self
     */
    public function setJquery(array $jquery): self
    {
        $this->jquery = $jquery;
        return $this;
    }

    /**
     * Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @return array
     */
    public function getPrismjs(): array
    {
        return $this->prismjs;
    }

    /**
     * Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @param array $prismjs Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @return self
     */
    public function setPrismjs(array $prismjs): self
    {
        $this->prismjs = $prismjs;
        return $this;
    }

    /**
     * Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @return array
     */
    public function getPopperjs(): array
    {
        return $this->popperjs;
    }

    /**
     * Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @param array $popperjs Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @return self
     */
    public function setPopperjs(array $popperjs): self
    {
        $this->popperjs = $popperjs;
        return $this;
    }

    /**
     * Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @return array
     */
    public function getMathjax(): array
    {
        return $this->mathjax;
    }

    /**
     * Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @param array $mathjax Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @return self
     */
    public function setMathjax(array $mathjax): self
    {
        $this->mathjax = $mathjax;
        return $this;
    }

    /**
     * Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @return array
     */
    public function getTinymce(): array
    {
        return $this->tinymce;
    }

    /**
     * Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @param array $tinymce Das sind die Arrays der Ordner die die _link_ und _target_ Werte der Symlinks enthalten
     * @return self
     */
    public function setTinymce(array $tinymce): self
    {
        $this->tinymce = $tinymce;
        return $this;
    }

    /**
     * @return array
     */
    public function getVendormodules(): array
    {
        return $this->vendormodule;
    }

    /**
     * @param array $vendormodule 
     * @return self
     */
    public function setVendormodules(array $vendormodule): self
    {
        $this->vendormodule = $vendormodule;
        return $this;
    }

    /**
     * @return array
     */
    public function getNodemodules(): array
    {
        return $this->nodemodule;
    }

    /**
     * @param array $nodemodule 
     * @return self
     */
    public function setNodemodules(array $nodemodule): self
    {
        $this->nodemodule = $nodemodule;
        return $this;
    }

    /**
     * @return array
     */
    public function getProjektarr(): array
    {
        return $this->projektarr;
    }

    /**
     * @param array $projektarr 
     * @return self
     */
    public function setProjektarr(array $projektarr): self
    {
        $this->projektarr = $projektarr;
        return $this;
    }

















    /** 
     * @return void
     * @author Christian Eichert <c@zp1.net>
     * @version 1.0.0
     */
    public function __construct()
    {

        if(empty($_SERVER['SYMLINK']))
        {
            die("ERROR: UNDOCUMENTED SYMLINK");
        }
        elseif(is_array(json_decode(json: $_SERVER['SYMLINK'], associative: true, flags: JSON_OBJECT_AS_ARRAY)))
        {
            $projektarr = [];
            foreach(json_decode(json: $_SERVER['SYMLINK'], associative: true, flags: JSON_OBJECT_AS_ARRAY) as $key => $val)
            {
                if(!empty($val))
                {
                    $projektarr += [ $key ];
                }
            }
            if(empty($projektarr))
            {
                error_log("ERROR: EMPTY SYMLINK (we will make no symlinks)");
            }
            $this->setProjektarr(projektarr: $projektarr);
        }
        else
        {
            die("ERROR: SOMEOTHERSHIT in SYMLINK");
        }


        // TODO: was sind Ordner mit Kringel ?
        $this->setOrdnerMitKringel(ordner_mit_kringel: [ "popperjs" ]);

        if(empty($_SERVER['DOCUMENT_ROOT']))
        {
            die("ERROR: UNDOCUMENTED DOCUMENT_ROOT");
        }

        if(empty($_SERVER['WORKSPACE']))
        {
            die("ERROR: UNDOCUMENTED WORKSPACE");
        }

        $this->setDocumentroot(documentroot: realpath(path: strval(value: $_SERVER['DOCUMENT_ROOT'])));
        $this->setWorkspace(workspace: realpath(path: strval(value: $_SERVER['WORKSPACE'])));

        $this->setDoxygenfolder(doxygenfolder: realpath(path: $this->getDocumentroot() . DIRECTORY_SEPARATOR . 'doxygen'));
        $this->setStaticfolder(staticfolder: realpath(path: $this->getDocumentroot() . DIRECTORY_SEPARATOR . 'static'));
        $this->setStaticfolderBs(staticfolder_bs: realpath(path: $this->getDocumentroot() . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . 'bs'));

        // Das sind "brauch man immer" Ordner
        $this->create_folder_if_not_exists(folder: $this->getDoxygenfolder());
        $this->create_folder_if_not_exists(folder: $this->getStaticfolder());
        $this->create_folder_if_not_exists(folder: $this->getStaticfolderBs());

        // TODO: es gibt ordner in env die sollten von einer function erzeugt werden
        $this->check_and_create_folder_if_not_exists(folder: '@popperjs', library: "@popperjs", vendor: "node_modules");
        $this->check_and_create_folder_if_not_exists(folder: "bs", library: "twbs/bootstrap", vendor: "vendor");
        $this->check_and_create_folder_if_not_exists(folder: "css");
        $this->check_and_create_folder_if_not_exists(folder: 'phcss');
        $this->check_and_create_folder_if_not_exists(folder: 'js');
        $this->check_and_create_folder_if_not_exists(folder: 'img');

        $this->setVendormodules(vendormodule: $this->detect_framework_components(vendor: "vendor"));
        $this->setNodemodules(nodemodule: $this->detect_framework_components(vendor: "node_modules"));




        /**
         "twbs/bootstrap"
         */
        if(
            // physisch vorhanden
            \in_array(needle: "twbs/bootstrap", haystack: $this->getVendormodules()) and
            // gewuenscht in .env
            \in_array(needle: "twbs/bootstrap", haystack: $this->getProjektarr())
        )
        {
            $this->setDist($this->checkLibraryInstallation(library: "twbs/bootstrap", vendor: "vendor") ? [
                'link'   => $this->makeLink(folder: 'bs', subfolder: 'dist'),
                'target' => $this->makeTarget(vendor: 'vendor', library: 'twbs/bootstrap-icons', folder: 'dist'),
            ] : []);
            empty($this->getDist()) ? null : $this->create_symlink(link: $this->getDist());
        }

        /**
         "twbs/bootstrap-icons"
         */
        if(
            \in_array(needle: "twbs/bootstrap-icons", haystack: $this->getVendormodules()) and
            \in_array(needle: "twbs/bootstrap-icons", haystack: $this->getProjektarr())
        )
        {
            $this->setFont(
                font: $this->checkLibraryInstallation(library: "twbs/bootstrap-icons", vendor: "vendor") ? [
                    'link'   => $this->makeLink(folder: 'bs', subfolder: 'font'),
                    'target' => $this->makeTarget(vendor: 'vendor', library: 'twbs/bootstrap-icons', folder: 'font'),
                ] : []
            );
            empty($this->getFont()) ? null : $this->create_symlink(link: $this->getFont());

            $this->setIcons($this->checkLibraryInstallation(library: "twbs/bootstrap-icons", vendor: "vendor") ? [
                'link'   => $this->makeLink(folder: 'bs', subfolder: 'icons'),
                'target' => $this->makeTarget(vendor: 'vendor', library: 'twbs/bootstrap-icons', folder: 'icons'),
            ] : []);
            empty($this->getIcons()) ? null : $this->create_symlink(link: $this->getIcons());
        }

        /**
         "jquery"
         */
        if(
            \in_array(needle: "jquery", haystack: $this->getNodemodules()) and
            \in_array(needle: "jquery", haystack: $this->getProjektarr())
        )
        {
            $this->setJquery(
                jquery: $this->checkLibraryInstallation(library: "jquery", vendor: "node_modules") ? [
                    'link'   => $this->makeLink(folder: 'jquery'),
                    'target' => $this->makeTarget(vendor: 'node_modules', library: 'jquery', folder: 'dist'),
                ] : []
            );
            empty($this->getJquery()) ? null : $this->create_symlink(link: $this->getJquery());
        }

        /**
         "prismjs"
         */
        if(
            \in_array(needle: "prismjs", haystack: $this->getNodemodules()) and
            \in_array(needle: "prismjs", haystack: $this->getProjektarr())
        )
        {
            $this->setPrismjs(
                prismjs: $this->checkLibraryInstallation(library: "prismjs", vendor: "node_modules") ? [
                    'link'   => $this->makeLink(folder: 'prismjs'),
                    'target' => $this->makeTarget(vendor: 'node_modules', library: 'prismjs'),
                ] : []
            );
            empty($this->prismjs) ? null : $this->create_symlink(link: $this->prismjs);
        }

        /**
         "mathjax"
         */
        if(
            \in_array(needle: "mathjax", haystack: $this->getNodemodules()) and
            \in_array(needle: "mathjax", haystack: $this->getProjektarr())
        )
        {
            $this->setMathjax(
                mathjax: $this->checkLibraryInstallation(library: "mathjax", vendor: "node_modules") ? [
                    'link'   => $this->makeLink(folder: 'mathjax'),
                    'target' => $this->makeTarget(vendor: 'node_modules', library: 'mathjax'),
                ] : []
            );
            empty($this->getMathjax()) ? null : $this->create_symlink(link: $this->getMathjax());
        }

        /**
         "popperjs"
         */
        if(
            \in_array(needle: "@popperjs", haystack: $this->getNodemodules()) and
            \in_array(needle: "@popperjs", haystack: $this->getProjektarr())
        )
        {
            $this->setPopperjs(
                popperjs: $this->checkLibraryInstallation(library: "@popperjs", vendor: "node_modules") ? [
                    'link'   => $this->makeLink(folder: '@popperjs', subfolder: 'core'),
                    'target' => $this->makeTarget(vendor: 'node_modules', library: '@popperjs', folder: 'core'),
                ] : []
            );
            empty($this->getPopperjs()) ? null : $this->create_symlink(link: $this->getPopperjs());
        }

        /**
         "tinymce"
         */
        if(
            \in_array(needle: "tinymce", haystack: $this->getNodemodules()) and
            \in_array(needle: "tinymce", haystack: $this->getProjektarr())
        )
        {
            $this->setTinymce(
                tinymce: $this->checkLibraryInstallation(library: "tinymce", vendor: "node_modules") ? [
                    'link'   => $this->makeLink(folder: 'tinymce'),
                    'target' => $this->makeTarget(vendor: 'node_modules', library: 'tinymce'),
                ] : []
            );
            empty($this->getTinymce()) ? null : $this->create_symlink(link: $this->getTinymce());
        }

        /**
         "chartjs"
         */
        if(
            \in_array(needle: "chartjs", haystack: $this->getNodemodules()) and
            \in_array(needle: "chartjs", haystack: $this->getProjektarr())
        )
        {
            $this->setChartjs(
                chartjs: $this->checkLibraryInstallation(library: "chartjs", vendor: "node_modules") ? [
                    'link'   => $this->makeLink(folder: 'chartjs'),
                    'target' => $this->makeTarget(vendor: 'node_modules', library: 'chartjs'),
                ] : []
            );
            empty($this->getChartjs()) ? null : $this->create_symlink(link: $this->getChartjs());
        }





    }

    /** makes the string that contains the Link 
     * @param string $folder 
     * @param string|null $subfolder 
     * @return string 
     * @author Christian Eichert <c@zp1.net>
     * @version 1.0.0
     */
    protected function makeLink(string $folder, string $subfolder = null): string
    {
        if(empty($folder))
            die('ERROR: ' . __CLASS__ . "/" . __METHOD__ . ":" . __LINE__);

        return realpath(
            path: $this->getStaticfolder() .
            DIRECTORY_SEPARATOR . $folder .
            (empty($subfolder) ? '' : DIRECTORY_SEPARATOR . $subfolder)
        );
    }

    /** makes string that contains the target for the link
     * @param string $vendor 
     * @param string $library 
     * @param string|null $folder 
     * @return string 
     * @author Christian Eichert <c@zp1.net>
     * @version 1.0.0
     */
    protected function makeTarget(string $vendor, string $library, string $folder = null)
    {
        if(empty($vendor) or empty($library))
        {
            die('ERROR: ' . __CLASS__ . "/" . __METHOD__ . ":" . __LINE__);
        }

        if(str_contains(haystack: $library, needle: '/'))
        {
            $library_explode = explode('/', $library);
            if(!empty($library_explode[0]))
            {
                $author = $library_explode[0];
                $library = $library_explode[1];
            }
            else
            {
                $author = '';
                $library = $library_explode[1];
            }
        }

        return realpath(
            path: $this->getWorkspace() .
            DIRECTORY_SEPARATOR . $vendor .
            (empty($author) ? '' : DIRECTORY_SEPARATOR . $author) .
            DIRECTORY_SEPARATOR . $library .
            (empty($folder) ? '' : DIRECTORY_SEPARATOR . $folder)
        ) . DIRECTORY_SEPARATOR;
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
        string $vendor = null,
        int $permissions = 0755
    ): void {

        if($folder === 'POPPERJS')
        {
            $folder = '@POPPERJS';
        }

        /** Wir machen den Folder weil es eine Library ist */
        if(!empty($folder) and !empty($library) and !empty($vendor) and $this->checkLibraryInstallation(library: $library, vendor: $vendor))
        {
            $completefolder = $this->staticfolder . DIRECTORY_SEPARATOR . ltrim(string: strtolower(string: $folder), characters: 'f');
            $this->create_folder_if_not_exists(folder: $completefolder, permissions: $permissions);
            /** Wir machen den Ordner sowieso auch wenn keine Library */
        }
        elseif(!empty($folder) and empty($library) and empty($vendor))
        {
            $completefolder = $this->staticfolder . DIRECTORY_SEPARATOR . ltrim(string: strtolower(string: $folder), characters: 'f');
            $this->create_folder_if_not_exists(folder: $completefolder, permissions: $permissions);
        }
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

        if(
            // wenn es noch keinen link gibt oder wenn der link nicht auf target zielt
            !is_link(filename: $link['link'])
            and
                // wenn es aber ein target gibt
            (!empty(realpath(path: $link['target'])) and realpath(path: $link['target']) !== '/')

        )
        {
            try
            {
                symlink(target: $link['target'], link: $link['link']);
            }
            catch (\Throwable $e)
            {
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
        foreach($this->ordner_mit_kringel as $elem)
        {
            $folder = strval(value: $this->str_replace_last(search: "/replace: $elem", replace: "/@$elem", subject: $folder));
            // wenn oben kein dirname war, probieren wir noch einmal
            $dirname ??= dirname(path: $folder);
        }
        // does the folder exist? if yes skip
        if(!is_dir(filename: $folder) or empty(realpath(path: $folder)))
        {
            // ist the parent writable ?
            if(!empty($dirname) and is_dir(filename: $dirname) and is_writable(filename: $dirname))
            {
                try
                {
                    mkdir(directory: $folder, permissions: $permissions, recursive: true);
                }
                catch (\Throwable $exception)
                {
                    \Sentry\captureException($exception);
                }
            }
            else
            {
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
        string $vendor
    ): bool {

        $vendorArr = [ "vendor", "node_modules" ];

        if(!in_array($vendor, $vendorArr))
        {
            die("Can't check Libraries, vendor($vendor) not in vendorArr(" . implode(separator: " ", array: $vendorArr) . ")");
        }

        if($vendor == "vendor")
        {
            $composerFile = "composer.json";
        }

        if($vendor == "node_modules")
        {
            $composerFile = "package.json";
        }

        $vendorDir = "$this->workspace/$vendor/$library";
        $isLibraryRequired = false;
        $isLibraryInstalled = false;

        if(file_exists("$this->workspace/$composerFile") and $composerFile === "composer.json")
        {
            $composerContent = json_decode(json: file_get_contents(filename: "$this->workspace/$composerFile"), associative: true);
            $isLibraryRequired = isset($composerContent['require'][$library]);
        }
        elseif(file_exists("$this->workspace/$composerFile") and $composerFile === "package.json")
        {
            $composerContent = json_decode(json: file_get_contents(filename: "$this->workspace/$composerFile"), associative: true);
            $isLibraryRequired = isset($composerContent['dependencies'][$library]);
        }
        else
        {
            $isLibraryRequired = false;
        }

        // Check if the Bootstrap directory exists and is not empty
        $isLibraryInstalled = is_dir(filename: $vendorDir) && (new FilesystemIterator(directory: "/$vendorDir", flags: FilesystemIterator::SKIP_DOTS))->valid();

        // conclusion
        if($isLibraryRequired && $isLibraryInstalled)
        {
            // echo "### $library is installed.<br>";
            return true;
        }
        else
        {
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

        if(is_readable(filename: realpath(path: $vendor)))
        {
            $_SERVER['VENDOR_EXISTS'] = true;
            return true;
        }
        else
        {
            $_SERVER['VENDOR_EXISTS'] = false;
            return false;
        }
    }

    /**
     * return all installed pakets of a framework, or false if none
     * 
     * zB alle composer module:      detect_framework_components(vendor:"vendor") => composer, ecxod, etc ...
     * zB alle komponenten in ecxod: detect_framework_components(subfolder:"ecxod", vendor:"vendor") => symlink, funktionen etc ...
     * zB alle node module:          detect_framework_components(vendor:"node_modules") => jquery etc ...
     * 
     * @param string $subfolder default : ecxod
     * @param string $vendor default : $_SERVER['VENDOR']
     * @return array|bool|string
     * @author Christian Eichert <c@zp1.net>
     * @version 1.0.0
     */
    public function detect_framework_components($library = "", string $vendor = null, string $output = "array")
    {
        $vendor ??= \strval($_SERVER['VENDOR']);
        $vendor ??= \strval(realpath($_SERVER['DOCUMENT_ROOT'] . '/vendor'));

        // enthaelt die relativen pfade der vendoren
        $vendorfolder_array = [ "vendor", "node_modules" ];
        // das resultat wird als arr, json oder cvs ausgegeben
        $outputarray = [ "array", "json", "csv" ];


        $project = "";

        // library in node_modules is like project
        if($vendor === 'node_modules')
        {
            if(!empty($library))
            {
                $project = $library;
            }
        }
        // library in vendor is like author/project
        if($vendor === 'vendor')
        {
            if(!empty($library))
            {
                $explode_library = explode(separator: '/', string: $library, limit: 2);
                if(!empty($explode_library))
                {
                    if(!empty($explode_library[0]))
                        // wenn im vendor library nicht leer ist author und project immer gegeben
                        $project = $explode_library[0];
                }
            }
        }


        foreach($vendorfolder_array as $vendorname)
        {

            if(
                self::detect_vendor(vendor: $vendor) and
                // wir pruefen ob der vendorname im vendorpfad ist
                str_contains(haystack: $vendor, needle: $vendorname)
            )
            {
                $subfolderpath =
                    (empty($library)) ?
                    realpath(path: $_SERVER['VENDOR']) :
                    realpath(path: $_SERVER['VENDOR'] . DIRECTORY_SEPARATOR . $project);

                if(is_readable(filename: $vendor) and is_readable(filename: $subfolderpath))
                {
                    // Alle Dateien und Ordner in eine Array einlesen
                    $result = scandir(directory: $subfolderpath, sorting_order: SCANDIR_SORT_ASCENDING) ?? [];

                    // wenn nichts ausser . und .. dann leere array (kein fehler kein false kein scheiss)
                    $directories = array_values(
                        array_diff(
                            is_array(value: $result) ? $result : [],
                            [ '..', '.' ]
                        )
                    );

                    // Ausgangsformat erzeugen
                    if(in_array(needle: $output, haystack: $outputarray) and gettype($directories) === 'array')
                    {
                        if($output === "array")
                        {
                            return $directories;
                        }
                        elseif($output === "json")
                        {
                            return json_encode(value: $directories);
                        }
                        elseif($output === "csv")
                        {
                            return implode(separator: ",", array: $directories);
                        }
                    }
                }
            }
        }
        return false;
    }







}
