<?php

if ( !function_exists( 'get_home_path' ) )
	require_once( dirname(__FILE__) . '/../../../wp-admin/includes/file.php' );

function zk_get_file_path($url){
	return  get_home_path().substr(str_replace(get_home_url(),"",$url),1);;
}