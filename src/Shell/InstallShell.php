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

use Cake\Console\ConsoleIo;
use MeTools\Console\Shell;

/**
 * Executes some tasks to make the system ready to work
 */
class InstallShell extends Shell
{
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
     * Paths to be created and made writable
     * @var array
     */
    protected $paths = [];

    /**
     * Construct
     * @param \Cake\Console\ConsoleIo|null $io An io instance
     * @uses $fonts
     * @uses $links
     * @uses $paths
     */
    public function __construct(ConsoleIo $io = null)
    {
        parent::__construct($io);

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
     * @uses copyFonts()
     * @uses createDirectories()
     * @uses createPluginsLinks()
     * @uses createRobots()
     * @uses createVendorsLinks()
     * @uses fixComposerJson()
     * @uses setPermissions()
     */
    public function all()
    {
        if ($this->param('force')) {
            $this->createDirectories();
            $this->setPermissions();
            $this->createRobots();
            $this->fixComposerJson();
            $this->createPluginsLinks();
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

        $ask = $this->in(__d('me_tools', 'Create {0}?', 'robots.txt'), ['Y', 'n'], 'Y');
        if (in_array($ask, ['Y', 'y'])) {
            $this->createRobots();
        }

        $ask = $this->in(__d('me_tools', 'Fix {0}?', 'composer.json'), ['Y', 'n'], 'Y');
        if (in_array($ask, ['Y', 'y'])) {
            $this->fixComposerJson();
        }

        $ask = $this->in(__d('me_tools', 'Create symbolic links for plugins assets?'), ['Y', 'n'], 'Y');
        if (in_array($ask, ['Y', 'y'])) {
            $this->createPluginsLinks();
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
     * Creates symbolic links for fonts
     * @return void
     * @uses $fonts
     * @uses MeTools\Console\Shell::createLink()
     */
    public function copyFonts()
    {
        foreach ($this->fonts as $origin) {
            $this->createLink(
                ROOT . DS . 'vendor' . DS . $origin,
                WWW_ROOT . 'fonts' . DS . basename($origin)
            );
        }
    }

    /**
     * Creates directories
     * @return void
     * @uses $paths
     * @uses createDir()
     */
    public function createDirectories()
    {
        foreach ($this->paths as $path) {
            $this->createDir($path);
        }
    }

    /**
     * Creates symbolic links for plugin assets
     * @return void
     * @see https://book.cakephp.org/3.0/en/deployment.html#symlink-assets
     */
    public function createPluginsLinks()
    {
        $this->Tasks->load('Assets')->symlink();
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
            $this->createLink(
                ROOT . DS . 'vendor' . DS . $origin,
                WWW_ROOT . 'vendor' . DS . $target
            );
        }
    }

    /**
     * Fixes the `composer.json` file, adding the `component-dir` value
     * @param string $path Path for `composer.json` file
     * @return void
     */
    public function fixComposerJson($path = null)
    {
        if (empty($path)) {
            $path = ROOT . DS . 'composer.json';
        }

        if (!is_writeable($path)) {
            $this->err(__d('me_tools', 'File or directory {0} not writeable', rtr($path)));

            return;
        }

        //Gets and decodes the file
        $contents = json_decode(file_get_contents($path), true);

        if (empty($contents)) {
            $this->err(__d('me_tools', 'The file {0} does not seem a valid {1} file', rtr($path), 'composer.json'));

            return;
        }

        //Checks if the file has been fixed
        if (!empty($contents['config']['component-dir']) &&
            $contents['config']['component-dir'] === 'vendor/components'
        ) {
            $this->verbose(__d('me_tools', 'The file {0} doesn\'t need to be fixed', rtr($path)));

            return;
        }

        $contents += ['config' => ['component-dir' => 'vendor/components']];

        file_put_contents($path, json_encode($contents, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        $this->verbose(__d('me_tools', 'The file {0} has been fixed', rtr($path)));
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
     * @return void
     * @uses $paths
     * @uses folderChmod()
     */
    public function setPermissions()
    {
        foreach ($this->paths as $path) {
            $this->folderChmod($path, 0777);
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
        $parser->addSubcommand('copyFonts', ['help' => __d('me_tools', 'Creates symbolic links for fonts')]);
        $parser->addSubcommand('createDirectories', ['help' => __d('me_tools', 'Creates default directories')]);
        $parser->addSubcommand('createPluginsLinks', ['help' => __d('me_tools', 'Creates symbolic links for plugins assets')]);
        $parser->addSubcommand('createRobots', ['help' => __d('me_tools', 'Creates the {0} file', 'robots.txt')]);
        $parser->addSubcommand('createVendorsLinks', ['help' => __d('me_tools', 'Creates symbolic links for vendor assets')]);
        $parser->addSubcommand('fixComposerJson', ['help' => __d('me_tools', 'Fixes {0}', 'composer.json')]);
        $parser->addSubcommand('setPermissions', ['help' => __d('me_tools', 'Sets directories permissions')]);

        $parser->addOption('force', [
            'boolean' => true,
            'default' => false,
            'help' => __d('me_tools', 'Executes tasks without prompting'),
            'short' => 'f',
        ]);

        $parser->setDescription(__d('me_tools', 'Executes some tasks to make the system ready to work'));

        return $parser;
    }
}
