<?php
/**
 * This file is part of MeTools.
 *
 * MeTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeTools.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeTools\Shell;

use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use MeTools\Console\Shell;
use MeTools\Core\Plugin;

/**
 * Executes some tasks to make the system ready to work
 */
class InstallShell extends Shell
{
    /**
     * Configuration files to be copied
     * @var array
     */
    protected $config = [];

    /**
     * Assets for which create symbolic links.
     * Full path for each font
     * @var array
     */
    protected $fonts = [];

    /**
     * Assets for which create symbolic links.
     * The key must be relative to `vendor/` and the value must be relative
     *  to `webroot/vendor/`
     * @var array
     */
    protected $links = [];

    /**
     * Suggested packages to install by Composer
     * @var array
     */
    protected $packages = [];

    /**
     * Paths to be created and made writable
     * @var array
     */
    protected $paths = [];

    /**
     * Construct
     * @param \Cake\Console\ConsoleIo|null $io An io instance
     * @uses $config
     * @uses $fonts
     * @uses $links
     * @uses $packages
     * @uses $paths
     */
    public function __construct(\Cake\Console\ConsoleIo $io = null)
    {
        parent::__construct($io);

        //Configuration files to be copied
        $this->config = [
            'MeTools.recaptcha',
        ];

        //Assets for which create symbolic links (full paths)
        $this->fonts = [
            'fortawesome/font-awesome/fonts/fontawesome-webfont.eot',
            'fortawesome/font-awesome/fonts/fontawesome-webfont.ttf',
            'fortawesome/font-awesome/fonts/fontawesome-webfont.woff',
            'fortawesome/font-awesome/fonts/fontawesome-webfont.woff2',
        ];

        //Assets for which create symbolic links
        $this->links = [
            'eonasdan/bootstrap-datetimepicker/build' => 'bootstrap-datetimepicker',
            'components/jquery' => 'jquery',
            'components/moment/min' => 'moment',
            'fortawesome/font-awesome' => 'font-awesome',
            'newerton/fancy-box/source' => 'fancybox',
        ];

        //Suggested packages to install by Composer
        $this->packages = [
            'components/jquery:^3.1',
            'eonasdan/bootstrap-datetimepicker:4.*',
            'fortawesome/font-awesome',
            'newerton/fancy-box:dev-master',
        ];

        //Paths to be created and made writable
        $this->paths = [
            LOGS,
            TMP,
            TMP . 'cache',
            TMP . 'cache' . DS . 'models',
            TMP . 'cache' . DS . 'persistent',
            TMP . 'cache' . DS . 'views',
            TMP . 'sessions',
            TMP . 'tests',
            WWW_ROOT . 'files',
            WWW_ROOT . 'fonts',
            WWW_ROOT . 'vendor',
        ];
    }

    /**
     * Executes all available tasks
     * @return void
     * @uses copyConfig()
     * @uses copyFonts()
     * @uses createDirectories()
     * @uses createRobots()
     * @uses createVendorsLinks()
     * @uses fixComposerJson()
     * @uses installPackages()
     * @uses setPermissions()
     */
    public function all()
    {
        if ($this->param('force')) {
            $this->createDirectories(true);
            $this->setPermissions(true);
            $this->copyConfig();
            $this->createRobots();
            $this->fixComposerJson();
            $this->installPackages(true);
            $this->createVendorsLinks();
            $this->copyFonts();

            return;
        }

        $ask = $this->in(__d('me_tools', 'Create default directories?'), ['y', 'N'], 'N');
        if (in_array($ask, ['Y', 'y'])) {
            $this->createDirectories();
        }

        $ask = $this->in(__d('me_tools', 'Set directories permissions?'), ['Y', 'n'], 'Y');
        if (in_array($ask, ['Y', 'y'])) {
            $this->setPermissions();
        }

        $ask = $this->in(__d('me_tools', 'Copy configuration files?'), ['Y', 'n'], 'Y');
        if (in_array($ask, ['Y', 'y'])) {
            $this->copyConfig();
        }

        $ask = $this->in(__d('me_tools', 'Create {0}?', 'robots.txt'), ['Y', 'n'], 'Y');
        if (in_array($ask, ['Y', 'y'])) {
            $this->createRobots();
        }

        $ask = $this->in(__d('me_tools', 'Fix {0}?', 'composer.json'), ['Y', 'n'], 'Y');
        if (in_array($ask, ['Y', 'y'])) {
            $this->fixComposerJson();
        }

        $ask = $this->in(__d('me_tools', 'Install the suggested packages?'), ['y', 'N', 'all'], 'N');
        if (in_array($ask, ['Y', 'y', 'all'])) {
            $this->installPackages($ask === 'all');
        }

        $ask = $this->in(__d('me_tools', 'Create symbolic links for vendor assets?'), ['Y', 'n'], 'Y');
        if (in_array($ask, ['Y', 'y'])) {
            $this->createVendorsLinks();
        }

        $ask = $this->in(__d('me_tools', 'Create symbolic links for fonts?'), ['Y', 'n'], 'Y');
        if (in_array($ask, ['Y', 'y'])) {
            $this->copyFonts();
        }
    }

    /**
     * Copies the configuration files
     * @return void
     * @uses $config
     * @uses MeTools\Console\Shell::copyFile()
     * @uses MeTools\Core\Plugin::path()
     */
    public function copyConfig()
    {
        foreach ($this->config as $file) {
            list($plugin, $file) = pluginSplit($file);

            $source = Plugin::path($plugin, 'config' . DS . $file . '.php');
            $dest = CONFIG . basename($source);

            $this->copyFile($source, $dest);
        }
    }

    /**
     * Creates symbolic links for fonts
     * @return void
     * @uses $fonts
     * @uses MeTools\Console\Shell::createLink()
     */
    public function copyFonts()
    {
        foreach ($this->fonts as $origin) {
            $origin = ROOT . DS . 'vendor' . DS . $origin;
            $target = WWW_ROOT . 'fonts' . DS . basename($origin);

            $this->createLink($origin, $target);
        }
    }

    /**
     * Creates directories
     * @param bool $force Forces creation
     * @return void
     * @uses $paths
     */
    public function createDirectories($force = false)
    {
        $error = false;

        foreach ($this->paths as $path) {
            if (file_exists($path)) {
                $this->verbose(__d('me_tools', 'File or directory {0} already exists', rtr($path)));

                continue;
            }

            if (mkdir($path, 0777, true)) {
                $this->verbose(__d('me_tools', 'Created {0} directory', rtr($path)));
            } else {
                $error = true;

                $this->err(__d('me_tools', 'Failed to create file or directory {0}', rtr($path)));
            }
        }

        //In case of error, asks for sudo
        if ($error && which('sudo')) {
            if ($this->param('force') || $force) {
                exec(sprintf('sudo mkdir -p %s', implode(' ', $this->paths)));

                return;
            }

            $ask = $this->in(__d('me_tools', 'Some directories were not created. Try again using {0}?', 'sudo'), ['Y', 'n'], 'Y');
            if (in_array($ask, ['Y', 'y'])) {
                exec(sprintf('sudo mkdir -p %s', implode(' ', $this->paths)));
            }
        }
    }

    /**
     * Creates the `robots.txt` file
     * @return void
     * @uses MeTools\Console\Shell::createFile()
     */
    public function createRobots()
    {
        $this->createFile(WWW_ROOT . 'robots.txt', 'User-agent: *' . PHP_EOL .
            'Disallow: /admin/' . PHP_EOL . 'Disallow: /ckeditor/' . PHP_EOL .
            'Disallow: /css/' . PHP_EOL . 'Disallow: /js/' . PHP_EOL .
            'Disallow: /vendor/');
    }

    /**
     * Creates symbolic links for vendor assets
     * @return void
     * @uses $links
     * @uses createLink()
     */
    public function createVendorsLinks()
    {
        foreach ($this->links as $origin => $target) {
            $origin = ROOT . DS . 'vendor' . DS . $origin;
            $target = WWW_ROOT . 'vendor' . DS . $target;

            $this->createLink($origin, $target);
        }
    }

    /**
     * Fixes `composer.json`
     * @return void
     */
    public function fixComposerJson()
    {
        $file = ROOT . DS . 'composer.json';

        if (!is_writeable($file)) {
            $this->err(__d('me_tools', 'File or directory {0} not writeable', rtr($file)));

            return;
        }

        //Gets and decodes the file
        $contents = json_decode(file_get_contents($file), true);

        //Checks if the file has been fixed
        if (!empty($contents['config']['component-dir']) &&
            $contents['config']['component-dir'] === 'vendor/components'
        ) {
            $this->verbose(__d('me_tools', 'The file {0} doesn\'t need to be fixed', rtr($file)));

            return;
        }

        //Fixeds and encodes the content
        $contents = (new File($file))->prepare(json_encode(
            am($contents, ['config' => ['component-dir' => 'vendor/components']]),
            JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
        ));

        if ((new File($file))->write($contents)) {
            $this->verbose(__d('me_tools', 'The file {0} has been fixed', rtr($file)));
        } else {
            $this->err(__d('me_tools', 'The file {0} has not been fixed', rtr($file)));
        }
    }

    /**
     * Install the suggested packages
     * @param bool $force Forces installing
     * @return void
     * @uses $packages
     */
    public function installPackages($force = false)
    {
        //Checks for Composer
        $bin = which('composer');

        if (!$bin) {
            $this->err(__d('me_tools', '{0} is not available', 'composer'));

            return;
        }

        //Empty arrays. These will contain the packages to install and the
        //  installed packages
        $packagesToInstall = $installed = [];

        //Asks whick packages to install, if it was not asked to install all of
        //them or if you are not using the "force" parameter
        if (!$force && !$this->param('force')) {
            foreach ($this->packages as $package) {
                $ask = $this->in(__d('me_tools', 'Do you want to install {0}?', $package), ['Y', 'n'], 'Y');

                if (in_array($ask, ['Y', 'y'])) {
                    $packagesToInstall[] = $package;
                }
            }

            if (empty($packagesToInstall)) {
                $this->verbose(__d('me_tools', 'No package has been selected for installation'));

                return;
            }
        } else {
            $packagesToInstall = $this->packages;
        }

        //Gets the list of installed packages
        exec(sprintf('%s show --latest --name-only', $bin), $installed);

        $packagesToInstall = array_diff($packagesToInstall, $installed);

        if (empty($packagesToInstall)) {
            $this->verbose(__d('me_tools', 'All packages are already installed'));

            return;
        }

        //Executes the command
        exec(sprintf('%s require %s', $bin, implode(' ', $packagesToInstall)));
    }

    /**
     * Main command. Alias for `main()`
     * @return void
     * @uses main()
     */
    public function main()
    {
        $this->all();
    }

    /**
     * Sets permissions on directories
     * @param bool $force Forces settings
     * @return void
     * @uses $paths
     */
    public function setPermissions($force = false)
    {
        $error = false;

        foreach ($this->paths as $path) {
            if ((new Folder())->chmod($path, 0777, true)) {
                $this->verbose(__d('me_tools', 'Setted permissions on {0}', rtr($path)));
            } else {
                $this->err(__d('me_tools', 'Failed to set permissions on {0}', rtr($path)));

                $error = true;
            }
        }

        //In case of error, asks for sudo
        if ($error && which('sudo')) {
            $command = sprintf('sudo chmod -R 777 %s', implode(' ', $this->paths));

            if ($this->param('force') || $force) {
                exec($command);

                return;
            }

            $ask = $this->in(__d('me_tools', 'Some directories were not created. Try again using {0}?', 'sudo'), ['Y', 'n'], 'Y');
            if (in_array($ask, ['Y', 'y'])) {
                exec($command);
            }
        }
    }

    /**
     * Gets the option parser instance and configures it
     * @return ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->addSubcommand('all', ['help' => __d('me_tools', 'Executes all available tasks')]);
        $parser->addSubcommand('copyConfig', ['help' => __d('me_tools', 'Copies the configuration files')]);
        $parser->addSubcommand('copyFonts', ['help' => __d('me_tools', 'Creates symbolic links for fonts')]);
        $parser->addSubcommand('createDirectories', ['help' => __d('me_tools', 'Creates default directories')]);
        $parser->addSubcommand('createRobots', ['help' => __d('me_tools', 'Creates the {0} file', 'robots.txt')]);
        $parser->addSubcommand('createVendorsLinks', ['help' => __d('me_tools', 'Creates symbolic links for vendor assets')]);
        $parser->addSubcommand('fixComposerJson', ['help' => __d('me_tools', 'Fixes {0}', 'composer.json')]);
        $parser->addSubcommand('installPackages', ['help' => __d('me_tools', 'Installs the suggested packages')]);
        $parser->addSubcommand('setPermissions', ['help' => __d('me_tools', 'Sets directories permissions')]);

        $parser->addOption('force', [
            'boolean' => true,
            'default' => false,
            'help' => __d('me_tools', 'Executes tasks without prompting'),
            'short' => 'f',
        ]);

        $parser->description(__d('me_tools', 'Executes some tasks to make the system ready to work'));

        return $parser;
    }
}
