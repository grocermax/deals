<?php
/*
Plugin Name: Track 404 Not found hits
Plugin URI: http://online-source.net/2011/04/03/404-not-found-report/
Description: Track the hits on your 404 - Not Found page
Version: 0.8
Author: MrXHellboy
Author URI: http://www.online-source.net
*/

/**
 * NotFound_404
 * Sets up the 404 page activity details
 */
class NotFound_404 {    
    # Vistor 404 time
    private $visitor_time = '';
    
    # Visitor request uri
    private $visitor_request_uri = '';
    
    # Visitor Refferer
    private $visitor_referer = '';
    
    # Constructer
    function __construct() {
        # The time
        $this->visitor_time = date('Y F-dS H:i:s', time());
        # Request URI
        $this->visitor_request_uri = ($_SERVER['HTTPS'] == 'on') ? 
                                    'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : 
                                    'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        # Referer                            
        $this->visitor_referer = $_SERVER['HTTP_REFERER'];
    }
    
    # Log the error
    public function Log404Error() {
        # If URI already exists -- Stop
        if (preg_match('@'.$this->visitor_request_uri.'@si', file_get_contents(dirname(__FILE__).'/404.txt')))
            return;
        
        # Prepare Error
        $Notice_404 = "Visitor Time: ". $this->visitor_time .
                        PHP_EOL ."Visitor request URI: ". $this->visitor_request_uri .
                        PHP_EOL ."Visitor Referer: ". $this->visitor_referer .
                        PHP_EOL . str_repeat('-', 25) . PHP_EOL;
        # Log activity                
        error_log($Notice_404, 3, dirname(__FILE__).'/404.txt');
            return;
    }
    
    # Destructor
    public function __destruct()
    {
        foreach (get_object_vars($this) as $property)
            $property = null;
    }
}

/**
 * NotFound_404_Admin extends NotFound_404
 * Sets up the admin options
 */
class NotFound_404_Admin extends NotFound_404 {
    
    # Will retrieve the content as an array
    protected function GetContent(){
        return ($file_arr = @file(dirname(__FILE__).'/404.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) ? implode('<br />', $file_arr) : 'There are no records';
    }
    
    # Output the 404 page
    public function SetAdminPage(){
        if (!current_user_can(10))
            wp_die('Come one ? Seriously ?');
            
        self::DeleteFile();
            $admin = '<div id="wrap">';
                $admin .= '<h2>404 Report</h2>';
                    $admin .= "<form method=\"post\" action=\"\"><button type=\"submit\" name=\"delete_file\">Clear File</button></form><hr />";
                $admin .= self::GetContent();                               
            $admin .= '</div>';
        echo $admin;
    }
    
    # Deletes the file
    protected function DeleteFile(){
        if (isset($_POST['delete_file']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
            file_put_contents(dirname(__FILE__).'/404.txt', '');
        }
            
    }
    
    # Add page @ admin menu
    public function AddControlPage(){
        add_options_page('404 Report', '404 Report', 10, '404_error', array(__CLASS__, 'SetAdminPage'));
            return;
    }
}

# Hook onto the admin_menu hook
add_action('admin_menu', array('NotFound_404_Admin', 'AddControlPage'));
?>