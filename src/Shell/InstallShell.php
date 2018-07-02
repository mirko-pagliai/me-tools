<?php
/**
 * This file is part of me-tools.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/me-tools
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
namespace MeTools\Shell;

use Cake\Console\ConsoleIo;
use Cake\Utility\Hash;
use Exception;
use MeTools\Console\Shell;

/**
 * Executes some tasks to make the system ready to work
 */
class InstallShell extends Shell
{
    /**
     * Assets for which create symbolic links.
     * The key must be relative to `vendor/` and the value must be relative
     *  to `webroot/vendor/`
     * @var array
     */
    protected $links = [
        'eonasdan' . DS . 'bootstrap-datetimepicker' . DS . 'build' => 'bootstrap-datetimepicker',
        'components' . DS . 'jquery' => 'jquery',
        'components' . DS . 'moment' . DS . 'min' => 'moment',
        'newerton' . DS . 'fancy-box' . DS . 'source' => 'fancybox',
        'npm-asset' . DS . 'fortawesome--fontawesome-free' => 'font-awesome',
        'twbs' . DS . 'bootstrap' . DS . 'dist' => 'bootstrap',
    ];

    /**
     * Paths to be created and made writable
     * @var array
     */
    protected $paths = [
        LOGS,
        TMP,
        TMP . 'cache',
        TMP . 'cache' . DS . 'models',
        TMP . 'cache' . DS . 'persistent',
        TMP . 'cache' . DS . 'views',
        TMP . 'sessions',
        TMP . 'tests',
        WWW_ROOT . 'files',
        WWW_ROOT . 'vendor',
    ];

    /**
     * Questions used by `all()` method
     * @var array
     */
    protected $questions = [];

    /**
     * Constructs this `Shell` instance
     * @param ConsoleIo $io A `ConsoleIo` instance
     * @uses $questions
     */
    public function __construct(ConsoleIo $io = null)
    {
        parent::__construct($io);

        $this->questions = [
            [
                'question' => __d('me_tools', 'Create default directories?'),
                'default' => 'N',
                'method' => 'createDirectories',
            ],
            [
                'question' => __d('me_tools', 'Set directories permissions?'),
                'default' => 'Y',
                'method' => 'setPermissions',
            ],
            [
                'question' => __d('me_tools', 'Create {0}?', 'robots.txt'),
                'default' => 'Y',
                'method' => 'createRobots',
            ],
            [
                'question' => __d('me_tools', 'Fix {0}?', 'composer.json'),
                'default' => 'Y',
                'method' => 'fixComposerJson',
            ],
            [
                'question' => __d('me_tools', 'Create symbolic links for plugins assets?'),
                'default' => 'Y',
                'method' => 'createPluginsLinks',
            ],
            [
                'question' => __d('me_tools', 'Create symbolic links for vendor assets?'),
                'default' => 'Y',
                'method' => 'createVendorsLinks',
            ],
        ];
    }

    /**
     * Executes all available tasks
     * @return void
     * @uses $questions
     */
    public function all()
    {
        $questions = $this->param('force') ? Hash::extract($this->questions, '{n}[default=Y]') : $this->questions;

        foreach ($questions as $question) {
            list($question, $default, $method) = array_values($question);

            //The method must be executed if the `force` mode is set or if the
            //  user answers yes to the question
            $toBeExecuted = true;
            if (!$this->param('force')) {
                $ask = $this->in($question, $default === 'Y' ? ['Y', 'n'] : ['y', 'N'], $default);
                $toBeExecuted = in_array($ask, ['Y', 'y']);
            }

            if ($toBeExecuted) {
                call_user_func([$this, $method]);
            }
        }
    }

    /**
     * Creates directories
     * @return void
     * @uses $paths
     */
    public function createDirectories()
    {
        array_map([$this, 'createDir'], $this->paths);
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
     * @uses MeTools\Console\Shell::createLink()
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
     * @return bool Success
     */
    public function fixComposerJson()
    {
        $path = $this->param('path') ?: ROOT . DS . 'composer.json';

        try {
            is_writable_or_fail($path);
        } catch (Exception $e) {
            $this->err($e->getMessage());

            return false;
        }

        //Gets and decodes the file
        $contents = json_decode(file_get_contents($path), true);

        if (empty($contents)) {
            $this->err(__d('me_tools', 'The file {0} does not seem a valid {1} file', rtr($path), 'composer.json'));

            return false;
        }

        //Checks if the file has been fixed
        if (!empty($contents['config']['component-dir']) &&
            $contents['config']['component-dir'] === 'vendor/components'
        ) {
            $this->verbose(__d('me_tools', 'The file {0} doesn\'t need to be fixed', rtr($path)));

            return true;
        }

        $contents += ['config' => ['component-dir' => 'vendor/components']];

        file_put_contents($path, json_encode($contents, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        $this->verbose(__d('me_tools', 'The file {0} has been fixed', rtr($path)));
    }

    /**
     * Main command. Alias for `main()`
     * @return void
     * @uses all()
     */
    public function main()
    {
        $this->all();
    }

    /**
     * Sets permissions on directories
     * @return void
     * @uses $paths
     * @uses MeTools\Console\Shell::folderChmod()
     */
    public function setPermissions()
    {
        array_map([$this, 'folderChmod'], $this->paths);
    }

    /**
     * Gets the option parser instance and configures it
     * @return ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->addSubcommand('all', ['help' => __d('me_tools', 'Executes all available tasks')]);
        $parser->addSubcommand('createDirectories', ['help' => __d('me_tools', 'Creates default directories')]);
        $parser->addSubcommand('createPluginsLinks', ['help' => __d('me_tools', 'Creates symbolic links for plugins assets')]);
        $parser->addSubcommand('createRobots', ['help' => __d('me_tools', 'Creates the {0} file', 'robots.txt')]);
        $parser->addSubcommand('createVendorsLinks', ['help' => __d('me_tools', 'Creates symbolic links for vendor assets')]);
        $parser->addSubcommand('fixComposerJson', [
            'help' => __d('me_tools', 'Fixes {0}', 'composer.json'),
            'parser' => [
                'options' => [
                    'path' => [
                        'help' => __d('me_tools', 'Path of the `{0}` file', 'composer.json'),
                        'short' => 'p',
                    ],
                ],
            ],
        ]);
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
