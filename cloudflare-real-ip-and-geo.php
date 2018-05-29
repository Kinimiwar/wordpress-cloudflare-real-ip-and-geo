<?php

/**************************************************************************
Plugin Name: Cloudflare Real IP and Geo
Plugin URI: https://wordpress.org/plugins/cloudflare-real-ip-and-geo/
Description: Saves and displays visitor's real IP and location, instead of Cloudflare's.
Author: RaMMicHaeL
Version: 1.0
Author URI: http://rammichael.com/
**************************************************************************/

function add_custom_comment_fields($comment_id)
{
	if (isset($_SERVER['HTTP_CF_CONNECTING_IP']))
	{
		add_comment_meta($comment_id, 'cf_connecting_ip', $_SERVER['HTTP_CF_CONNECTING_IP']);
	}
	if (isset($_SERVER['HTTP_CF_IPCOUNTRY']))
	{
		add_comment_meta($comment_id, 'cf_ipcountry', $_SERVER['HTTP_CF_IPCOUNTRY']);
	}
}
add_action('comment_post', 'add_custom_comment_fields');

function my_filter($objects)
{   
	global $current_screen;
	if (is_admin() && $current_screen->id == 'edit-comments') // I only needed it on that screen within admin
	{
		if (count($objects) > 0)
		{
			foreach ($objects as $key => $object)
			{
				$cf_connecting_ip = get_comment_meta($object->comment_ID, 'cf_connecting_ip', true);
				if ($cf_connecting_ip)
				{
					$cf_ipcountry = get_comment_meta($object->comment_ID, 'cf_ipcountry', true);
					if (!$cf_ipcountry)
					{
						$cf_ipcountry = '???';
					}
					$object->comment_author_IP = "[$cf_ipcountry] $cf_connecting_ip (cf:{$object->comment_author_IP})";
				}
			}
		}
	}
	return $objects;
}
add_filter('the_comments', 'my_filter');
