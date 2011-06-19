<?php

/**
 * wpadmin
 *
 * Coded to Zend's coding standards:
 * http://framework.zend.com/manual/en/coding-standard.html
 *
 * File format: UNIX
 * File encoding: UTF8
 * File indentation: Spaces (4). No tabs
 *
 * @category   Plugin
 * @copyright  Copyright (c) 2011 88mph. (http://88mph.co)
 * @license    GNU
 */
 
/**
 * The WpAdmin_Plugin class provides methods for dealing with the administering
 * of the WordPress plugins.
 * 
 * 
 * @since 0.0.2
 * @author  Ben Campbell http://scumways.com
 */
class WpAdmin_Plugin extends WpAdmin
{
    /**
     * Array of plugin data returned from the database.
     *
     * @access private
     * @see WpAdmin_Plugin::getData()
     * @param  array
     */
    private $_data = array();
    
    /**
     * Class constructor. Private for factory-style methods.
     *
     * @access private
     * @param $path relative path of plugin file
     * @param $p raw plugin data from get_plugin_data()
     * @return WpAdmin_Plugin
     */
    private function __construct($path,$p)
    {
        $this->_data= array( $path,
            $p['Name'],
            $p['PluginURI'],
            $p['Version'],
            $p['Description'],
            $p['Author'],
            $p['AuthorURI'],
            $p['TextDomain'],
            $p['DomainPath'],
            $p['Network'],
            $p['Title'] );
    }

    /**
     * Returns available plugins as an array of WpAdmin_Plugin objects.
     * (NOTE: uses the possibly-private get_plugins() call...)
     *
     * @static
     * @access  public
     * @return  array   Array of WpAdmin_Plugin objects.
     */
    static public function listAll($params)
    {
        $installed = array();
        $plugins = get_plugins();
        foreach ($plugins as $path=>$data){
            $installed[] = new WpAdmin_Plugin( $path, $data );
        }
        return $installed;
    }

    /**
     * Returns an array of column headings, matching order returned by getData()
     * This is used for the tabular output.
     *
     * @access  public
     * @return  array
     */
    public function getColumnHeaders()
    {
        return array(
            'Path',
            'Name',
            'PluginURI',
            'Version',
            'Description',
            'Author',
            'AuthorURI',
            'TextDomain',
            'DomainPath',
            'Network',
            'Title' );
    }
 
    /**
     * Getter for {@link $_username}.
     *
     * @access public 
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }
    
}
