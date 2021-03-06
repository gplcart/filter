<?php

/**
 * @package Filter
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\filter;

use Exception;
use gplcart\core\Config;
use gplcart\core\Library;
use HTMLPurifier;
use HTMLPurifier_Config;
use LogicException;

/**
 * Main class for Filter module
 */
class Main
{

    /**
     * An array of HTML Purifier instances keyed by filter configuration hash
     * @var array
     */
    protected $htmlpurifiers = array();

    /**
     * Config class instance
     * @var \gplcart\core\Config $config
     */
    protected $config;

    /**
     * Library class instance
     * @var \gplcart\core\Library $library
     */
    protected $library;

    /**
     * @param Config $config
     * @param Library $library
     */
    public function __construct(Config $config, Library $library)
    {
        $this->config = $config;
        $this->library = $library;
    }

    /**
     * Implements hook "library.list"
     * @param array $libraries
     */
    public function hookLibraryList(array &$libraries)
    {
        $libraries['htmlpurifier'] = array(
            'name' => 'HTML Purifier', // @text
            'description' => 'Standards compliant HTML filter written in PHP', // @text
            'type' => 'php',
            'module' => 'filter',
            'url' => 'https://github.com/ezyang/htmlpurifier',
            'download' => 'https://github.com/ezyang/htmlpurifier/archive/v4.9.2.zip',
            'version' => '4.9.2',
            'vendor' => 'ezyang/htmlpurifier',
            'files' => array(
                'library/HTMLPurifier.auto.php'
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
        $permissions['module_filter_edit'] = 'HTML Filter: edit'; // @text
        $permissions['module_filter_delete'] = 'HTML Filter: delete'; // @text
    }

    /**
     * Implements hook "filter"
     * @param string $text
     * @param array $filter
     * @param null|string $filtered
     */
    public function hookFilter($text, $filter, &$filtered)
    {
        if (!isset($filtered) && isset($filter['module']) && $filter['module'] === 'filter' && !empty($filter['status'])) {
            try {
                $filtered = $this->filter($text, $filter);
            } catch (Exception $ex) {
                $filtered = '** filter error **';
            }
        }
    }

    /**
     * Implements hook "filter.handlers"
     * @param mixed $filters
     */
    public function hookFilterHandlers(array &$filters)
    {
        $filters = array_merge($filters, $this->getHandlers());
    }

    /**
     * Implements hook "module.uninstall.after"
     */
    public function hookModuleUninstallAfter()
    {
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
        return $this->getPurifier($filter)->purify($text);
    }

    /**
     * Returns HTML Purifier class instance depending on the filter configuration
     * @param array $filter
     * @return HTMLPurifier
     * @throws LogicException
     */
    public function getPurifier(array $filter)
    {
        ksort($filter['data']);
        $key = md5(json_encode($filter['data']));

        if (isset($this->htmlpurifiers[$key])) {
            return $this->htmlpurifiers[$key];
        }

        $this->library->load('htmlpurifier');

        if (!class_exists('HTMLPurifier')) {
            throw new LogicException('Class HTMLPurifier not found');
        }

        if (empty($filter['data'])) {
            $config = HTMLPurifier_Config::createDefault();
        } else {
            $config = HTMLPurifier_Config::create($filter['data']);
        }

        return $this->htmlpurifiers[$key] = new HTMLPurifier($config);
    }

    /**
     * Returns an array of filter handlers
     * @return array
     */
    public function getHandlers()
    {
        $filters = gplcart_config_get(__DIR__ . '/config/filters.php');
        $saved = $this->config->get('module_filter_filters', array());

        return array_replace_recursive($filters, $saved);
    }

}
