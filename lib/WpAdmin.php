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
 * @copyright   Copyright (c) 2011 88mph. (http://88mph.co)
 * @license     GNU
 * @author      88mph http://88mph.co
 */
 
/**
 * The WpAdmin class provides methods for parsing user input and instantiating
 * the required class.
 *
 * @since   0.0.1
 * @author  Kieran Masterton http://twitter.com/kieranmasterton
 */
class WpAdmin
{
    /**
     * Class name to instantiate.
     *
     * @static
     * @access private
     * @param  string
     */
    private static $_className;
    
    /**
     * Method name to instantiate.
     *
     * @static
     * @access private
     * @param  string
     */
    private static $_methodName;
    
    /**
     * Params to pass to class method.
     *
     * @static
     * @access private
     * @param  array
     */
    private static $_params = array();
    
    /**
     * Function invoked from prompt.
     *
     * @static
     * @access  public
     * @param   array $args
     * @return  void
     */
    public static function exec($args)
    {
        // Parse user input create set class properties.
        self::_parseOptions($args);

        // Check class exists and instantiate object & call method.
        $class      = self::$_className;
        $method     = self::$_methodName;
        
        // Decide how to call the class / method & in special cases what to do
        // with the returned data.
        switch($method){
            
            // Add option.
            case 'add':
                // Using eval() as opposed to $class::$method() for the benefit
                // of users with PHP version < 5.3.0. 
                eval("\$object = " . $class . "::add(self::\$_params);");
                break;
            
            // List option.
            case 'list':
                
                // Fetch an array of objects of type $class.
                eval("\$objects = " . $class . "::listAll(self::\$_params);");
                
                // Create an instance of Console_Table for the output formatting.
                $tbl = new Console_Table(CONSOLE_TABLE_ALIGN_LEFT, 
                                            CONSOLE_TABLE_BORDER_ASCII, 
                                            1,
                                            'UTF-8');
                
                // Create the headers for the table.
                $tbl->setHeaders($class::getColumnHeaders());
                
                // Loop through each of the returned objects.
                foreach($objects as $object){
                    
                    // Create an array to hold the row data for 
                    // Console_Table::addRow().
                    $rowData = array();
                    
                    // Loop through each of the key => value pairs returned as
                    // we need to truncate the length of long strings so that
                    // we dont get "Allowed memory size exhausted" errors.
                    foreach($object->getData() as $key => $value){
                        if (strlen($value) > 50){
                            $value = substr($value, 0, 50) . '[...]';
                        }
                        
                        // Add the data to the row data array.
                        $rowData[$key] = $value;
                    }
                    
                    // Add the row to Console_Table.
                    $tbl->addRow($rowData);
                }
                
                // Output the table.
                echo $tbl->getTable();
                
            break;
            
            // Default option.
            default:
                // Does the class and method requested exist?
                if(method_exists($class, $method)){
                    eval("\$object = " . $class . "::load(self::\$_params['primary']);");
                    $object->$method(self::$_params);
                }else{
                    // Else class / method not found, display help.
                    self::displayHelp();
                }
                break;
        }
    }
    
    /**
     * Parse the arguments send from the command line.
     *
     * @static
     * @access  private
     * @param   array $args
     * @return  void
     */
    private static function _parseOptions($args)
    {
        // Set class name and method name based on first & second user input.
        self::$_className = 'WpAdmin_' . ucfirst($args[1]);
        self::$_methodName = strtolower($args[2]);
        
        // Unset no longer required user input.
        unset($args[0], $args[1], $args[2]);
        
        // Loop through user input create array of key value pairs.
        foreach($args as $k => $value){
            
            // Does the value contain an equals sign?
            if (preg_match('/=/',$value)){
                
                // yes, the value does contain an equals sign. Therefore we need
                // to split the value into two parts.
                $pair = preg_split('/=/',$value);
                
                $pair[0] = substr($pair[0], 2);
                self::$_params[$pair[0]] = $pair[1];
            }else{
                // The value doesnt contrain an equals. Just add the key to
                // the params array with a value of true.
                $value = substr($value, 2);
                self::$_params[$value] = TRUE;
                
            }
        }
        
        // Special circumstances for primary key for load lookup.
        switch (self::$_className) {
            case 'WpAdmin_User':
                self::$_params['primary'] = self::$_params['username'];
                break;
            default:
                $firstKey = array_keys(self::$_params);
                self::$_params['primary'] = self::$_params[$firstKey[0]];
                break;
        }
        
        // Debug.
        //var_dump(self::$_params); exit;
    }
    
    /**
     * Determines whether the user has said "yes" or "no" to a question prompt on
     * the command line.
     *
     * @static
     * @access  public
     * @param   array $args
     * @return  void
     */
    public static function _parseYesNo($response)
    {
        if('yes' == strtolower($response) || 'y' == strtolower($response)){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
    /**
     * Displays end-user help.
     *
     * @static
     * @access  public
     * @return  string
     */
    public static function displayHelp()
    {

$str = <<<EOF
Usage: wpadmin [options] [params]

User functions:
    
    user add --username={username} --email={email} --password={password}
    user delete --username={username}
    user update --username={username} --role={subscriber, editor, author, administrator}
    user update --username={username} --password={value}
                                      --email={value}
                                      --display_name={value}
                                      --nickname={value}
                                      --first_name={value}
                                      --last_name={value}
                                      --description={value}
    
Option functions:
    
    wpadmin option add --{key}={value}
    wpadmin option update --{key}={value}
    wpadmin option delete --{key}
    
    Key/value pairs can be found in the wp_options table. For example this command 
    will disable user comments:
    
    wpadmin option update --default_comment_status=closed

Plugin functions:

    wp plugin list

EOF;
            
        echo $str;
    }
    
}
