<?php

/**
 * @package Filter
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\filter;

use gplcart\core\Module,
    gplcart\core\Library;
use gplcart\core\models\Language as LanguageModel;

/**
 * Main class for Filter module
 */
class Filter extends Module
{

    /**
     * An array of HTML Purifier instances keyed by filter config hash
     * @var array
     */
    protected $htmlpurifiers = array();

    /**
     * Library class instance
     * @var \gplcart\core\Library $library
     */
    protected $library;

    /**
     * Language model instance
     * @var \gplcart\core\models\Language $language
     */
    protected $language;

    /**
     * @param Library $library
     * @param LanguageModel $language
     */
    public function __construct(Library $library, LanguageModel $language)
    {
        parent::__construct();

        $this->library = $library;
        $this->language = $language;
    }

    /**
     * Module info
     * @return array
     */
    public function info()
    {
        return array(
            'name' => 'Filter',
            'version' => '1.0.0-dev',
            'description' => 'Provides advanced HTML filters based on the HTML Purifier library',
            'author' => 'Iurii Makukh ',
            'core' => '1.x',
            'license' => 'GPL-3.0+',
            'configure' => 'admin/module/settings/filter',
            'settings' => array(
                'role_id' => array('minimal' => array(), 'advanced' => array(), 'maximal' => array()),
                'status' => array('minimal' => false, 'advanced' => false, 'maximal' => false)
            ),
        );
    }

    /**
     * Implements hook "library.list"
     * @param array $libraries
     */
    public function hookLibraryList(array &$libraries)
    {
        $libraries['htmlpurifier'] = array(
            'name' => 'HTML Purifier',
            'description' => 'Standards compliant HTML filter written in PHP',
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
                'controller' => array('gplcart\\modules\\filter\\controllers\\Settings', 'editSettings')
            )
        );
    }

    /**
     * Implements hook "user.role.permissions"
     * @param array $permissions
     */
    public function hookUserRolePermissions(array &$permissions)
    {
        $permissions['module_filter_edit'] = 'HTML Filter: edit';
    }

    /**
     * Implements hook "filter"
     * @param mixed $data
     */
    public function hookFilter($text, $filter, &$filtered)
    {
        $filtered = $this->filter($text, $filter);
    }

    /**
     * Filter a string
     * @param string $text
     * @param array $filter
     * @return string
     */
    public function filter($text, $filter)
    {
        if (empty($filter['config']) || empty($filter['status'])) {
            $filter['config'] = array(); // Empty config enables most safest default filter
        }

        return $this->getHtmlpurifierInstance($filter)->purify($text);
    }

    /**
     * Returns HTML Purifier class instance depending on the filter config
     * @param array $filter
     * @return \HTMLPurifier
     */
    public function getHtmlpurifierInstance(array $filter)
    {
        ksort($filter['config']);
        $key = md5(json_encode($filter['config']));

        if (isset($this->htmlpurifiers[$key])) {
            return $this->htmlpurifiers[$key];
        }

        $this->library->load('htmlpurifier');

        if (empty($filter['config'])) {
            $config = \HTMLPurifier_Config::createDefault();
        } else {
            $config = \HTMLPurifier_Config::create($filter['config']);
        }

        return $this->htmlpurifiers[$key] = new \HTMLPurifier($config);
    }

    /**
     * Implements hook "filter.list"
     * @param mixed $filters
     */
    public function hookFilterList(array &$filters)
    {
        $settings = $this->config->module('filter');

        $filters['minimal'] = array(
            'name' => $this->language->text('Minimal'),
            'description' => $this->language->text('Minimal configuration for untrusted users'),
            'status' => $settings['status']['minimal'],
            'role_id' => $settings['role_id']['minimal'],
            'config' => array(
                'AutoFormat.DisplayLinkURI' => true,
                'AutoFormat.RemoveEmpty' => true,
                'HTML.Allowed' => 'strong,em,p,b,s,i,a[href|title],img[src|alt],'
                . 'blockquote,code,pre,del,ul,ol,li'
            )
        );

        $filters['advanced'] = array(
            'name' => $this->language->text('Advanced'),
            'description' => $this->language->text('Advanced configuration for trusted users, e.g content managers'),
            'status' => $settings['status']['advanced'],
            'role_id' => $settings['role_id']['advanced'],
            'config' => array(
                'AutoFormat.Linkify' => true,
                'AutoFormat.RemoveEmpty.RemoveNbsp' => true,
                'AutoFormat.RemoveEmpty' => true,
                'HTML.Nofollow' => true,
                'HTML.Allowed' => 'div,table,tr,td,tbody,tfoot,thead,th,strong,'
                . 'em,p[style],b,s,i,h2,h3,h4,h5,hr,br,span[style],a[href|title],'
                . 'img[width|height|alt|src],blockquote,code,pre,del,kbd,'
                . 'cite,dt,dl,dd,sup,sub,ul,ol,li',
                'CSS.AllowedProperties' => 'font,font-size,font-weight,font-style,'
                . 'font-family,text-decoration,padding-left,color,'
                . 'background-color,text-align',
                'HTML.FlashAllowFullScreen' => true,
                'HTML.SafeObject' => true,
                'HTML.SafeEmbed' => true,
                'HTML.Trusted' => true,
                'Output.FlashCompat' => true
            )
        );

        $filters['maximal'] = array(
            'name' => $this->language->text('Maximal'),
            'description' => $this->language->text('Maximal configuration for experienced and trusted users, e.g superadmin'),
            'status' => $settings['status']['maximal'],
            'role_id' => $settings['role_id']['maximal'],
            'config' => array(
                'AutoFormat.Linkify' => true,
                'AutoFormat.RemoveEmpty.RemoveNbsp' => false,
                'AutoFormat.RemoveEmpty' => true,
                'HTML.Allowed' => 'div,table,tr,td,tbody,tfoot,thead,th,strong,'
                . 'em,p[style],b,s,i,h2,h3,h4,h5,hr,br,span[style],a[href|title],'
                . 'img[width|height|alt|src],blockquote,code,pre,del,kbd,'
                . 'cite,dt,dl,dd,sup,sub,ul,ol,li',
                'CSS.AllowedProperties' => 'font,font-size,font-weight,font-style,'
                . 'font-family,text-decoration,padding-left,color,'
                . 'background-color,text-align',
                'HTML.FlashAllowFullScreen' => true,
                'HTML.SafeObject' => true,
                'HTML.SafeEmbed' => true,
                'HTML.Trusted' => true,
                'Output.FlashCompat' => true,
                'Attr.AllowedFrameTargets' => array('_blank', '_self', '_parent', '_top')
            )
        );
    }

    /**
     * Implements hook "module.enable.after"
     */
    public function hookModuleEnableAfter()
    {
        $this->library->clearCache();
    }

    /**
     * Implements hook "module.disable.after"
     */
    public function hookModuleDisableAfter()
    {
        $this->library->clearCache();
    }

    /**
     * Implements hook "module.install.after"
     */
    public function hookModuleInstallAfter()
    {
        $this->library->clearCache();
    }

    /**
     * Implements hook "module.uninstall.after"
     */
    public function hookModuleUninstallAfter()
    {
        $this->library->clearCache();
    }

}