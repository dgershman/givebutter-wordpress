<?php
/*
Plugin Name: givebutter
Plugin URI: https://wordpress.org/plugins/givebutter/
Description: Shows data from GiveButter.
Author: radius314
Author URI: https://example.app
Version: 0.0.1
*/
/* Disallow direct access to the plugin file */
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Sorry, but you cannot access this page directly.');
}
if (!class_exists("GiveButter")) {
    class GiveButter
    {
        public function __construct()
        {
            add_shortcode('givebutter', array(
                &$this,
                "giveButter"
            ));
        }
    
        function giveButter($atts) {
            $args = array('headers' => [
                'Authorization' => sprintf('Bearer %s', $atts['api_key'])
            ]);
            $results = wp_remote_get(sprintf("https://api.givebutter.com/v1/campaigns/%s", $atts['campaign_id']), $args);
            $result = json_decode(wp_remote_retrieve_body($results), true);
            if (is_wp_error($results)) {
                echo '<div style="font-size: 20px;text-align:center;font-weight:normal;color:#F00;margin:0 auto;margin-top: 30px;"><p>Problem Connecting to GiveButter</p><p>Error: ' . $result->get_error_message() . '</p></div>';
                return 0;
            }
            
            $goal = $result['goal'];
            $raised = $result['raised'];
            $donors = $result['donors'];
            $pct_goal = round((float)($raised / $goal) * 100);
            
            echo sprintf("$%s (%s%%) raised thus far by %s donations.", $raised, $pct_goal, $donors);
        }
    }
}
if (class_exists("GiveButter")) {
    new GiveButter();
}
