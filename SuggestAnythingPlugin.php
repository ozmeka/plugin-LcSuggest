<?php
/**
 * Library of Congress Suggest
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The Library of Congress Suggest plugin.  Modified to add SCOT and Geonames support.
 * 
 * @package Omeka\Plugins\SuggestAnything
 */
class SuggestAnythingPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'install', 
        'uninstall', 
        'initialize', 
        'define_acl', 
    );
    
    protected $_filters = array(
        'admin_navigation_main', 
    );
    
    /**
     * Install the plugin.
     */
    public function hookInstall()
    {
        $sql = "
        CREATE TABLE `{$this->_db->SuggestAnything}` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `element_id` int(10) unsigned NOT NULL,
            `suggest_endpoint` tinytext COLLATE utf8_unicode_ci NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `element_id` (`element_id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $this->_db->query($sql);
    }
    
    /**
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {
        $sql = "DROP TABLE IF EXISTS `{$this->_db->SuggestAnything}`";
        $this->_db->query($sql);
    }
    
    /**
     * Initialize the plugin.
     */
    public function hookInitialize()
    {
        // Register the SelectFilter controller plugin.
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new SuggestAnything_Controller_Plugin_Autosuggest);
        
        // Add translation.
        add_translation_source(dirname(__FILE__) . '/languages');
    }
    
    /**
     * Define the plugin's access control list.
     */
    public function hookDefineAcl($args)
    {
        $args['acl']->addResource('SuggestAnything_Index');
    }
    
    /**
     * Add the LC Suggest page to the admin navigation.
     */
    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('Suggest Anything'), 
            'uri' => url('suggest-anything'), 
            'resource' => 'SuggestAnything_Index', 
            'privilege' => 'index', 
        );
        return $nav;
    }
}
