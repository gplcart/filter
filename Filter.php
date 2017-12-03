<?php

/**
 * @package Filter
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\filter;

use gplcart\core\Module,
    gplcart\core\Config;

/**
 * Main class for Filter module
 */
class Filter extends Module
{

    /**
     * An array of HTML Purifier instances keyed by filter configuration hash
     * @var array
     */
    protected $htmlpurifiers = array();

    /**
     * Constructor
     */
    public function __construct(Config $config)
    {
        parent::__construct($config);
    }

    /**
     * Implements hook "library.list"
     * @param array $libraries
     */
    public function hookLibraryList(array &$libraries)
    {
        $libraries['htmlpurifier'] = array(
            'name' => /* @text */'HTML Purifier',
            'description' => /* @text */'Standards compliant HTML filter written in PHP',
            'type' => 'php',
            'module' => 'filter',
            'url' => 'https://github.com/ezyang/htmlpurifier',
            'download' => 'https://github.com/ezyang/htmlpurifier/archive/v4.9.2.zip',
            'version_source' => array(
                'lines' => 100,
                'file' => 'vendor/ezyang/htmlpurifier/library/HTMLPurifier.php',
                'pattern' => '/.*VERSION.*(\\d+\\.+\\d+\\.+\\d+)/'
            ),
            'files' => array(
                'vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php'
            ),
        );
    }

    /**
     * Implements hook "route.list"
     * @param array $routes
     */
    public function hookRouteList(array &$routes)
    {
        $routes['admin/module/settings/filter'] = array(
            'access' => 'module_edit',
            'handlers' => array(
                'controller' => array('gplcart\\modules\\filter\\controllers\\Filter', 'listFilter')
            )
        );

        $routes['admin/module/settings/filter/edit/(\w+)'] = array(
            'access' => 'module_filter_edit',
            'handlers' => array(
                'controller' => array('gplcart\\modules\\filter\\controllers\\Filter', 'editFilter')
            )
        );
    }

    /**
     * Implements hook "user.role.permissions"
     * @param array $permissions
     */
    public function hookUserRolePermissions(array &$permissions)
    {
        $permissions['module_filter_edit'] = /* @text */'HTML Filter: edit';
        $permissions['module_filter_delete'] = /* @text */'HTML Filter: delete';
    }

    /**
     * Implements hook "filter"
     * @param string $text
     * @param array $filter
     * @param null|string $filtered
     */
    public function hookFilter($text, $filter, &$filtered)
    {
        if (isset($filter['module']) && $filter['module'] === 'filter' && !empty($filter['status'])) {
            $filtered = $this->filter($text, $filter);
        }
    }

    /**
     * Implements hook "filter.handlers"
     * @param mixed $filters
     */
    public function hookFilterHandlers(array &$filters)
    {
        $filters = array_merge($filters, $this->getFilterHandlers());
    }

    /**
     * Implements hook "module.enable.after"
     */
    public function hookModuleEnableAfter()
    {
        $this->getLibrary()->clearCache();
    }

    /**
     * Implements hook "module.disable.after"
     */
    public function hookModuleDisableAfter()
    {
        $this->getLibrary()->clearCache();
    }

    /**
     * Implements hook "module.install.after"
     */
    public function hookModuleInstallAfter()
    {
        $this->getLibrary()->clearCache();
    }

    /**
     * Implements hook "module.uninstall.after"
     */
    public function hookModuleUninstallAfter()
    {
        $this->getLibrary()->clearCache();

        foreach (array_keys($this->config->select()) as $key) {
            if (strpos($key, 'module_filter_') === 0) {
                $this->config->reset($key);
            }
        }
    }

    /**
     * Filter a string
     * @param string $text
     * @param array $filter
     * @return string
     */
    public function filter($text, $filter)
    {
        return $this->getHtmlpurifierInstance($filter)->purify($text);
    }

    /**
     * Returns HTML Purifier class instance depending on the filter configuration
     * @param array $filter
     * @return \HTMLPurifier
     */
    public function getHtmlpurifierInstance(array $filter)
    {
        ksort($filter['data']);
        $key = md5(json_encode($filter['data']));

        if (isset($this->htmlpurifiers[$key])) {
            return $this->htmlpurifiers[$key];
        }

        $this->getLibrary()->load('htmlpurifier');

        if (empty($filter['data'])) {
            $config = \HTMLPurifier_Config::createDefault();
        } else {
            $config = \HTMLPurifier_Config::create($filter['data']);
        }

        return $this->htmlpurifiers[$key] = new \HTMLPurifier($config);
    }

    /**
     * Returns an array of filter handlers
     * @return array
     */
    protected function getFilterHandlers()
    {
        $filters = gplcart_config_get(__DIR__ . '/config/filters.php');
        $saved = $this->config->get('module_filter_filters', array());
        return array_replace_recursive($filters, $saved);
    }

}
