<?php

/**
 * Plugin Name: WC Block User
 * Description: Prevent certain users from completing a checkout in WooCommerce.
 * Version: v2023.03.02
 * Author: Jesse Shawl
 * Author URI: https://jesse.sh/
 * License: GPLv2 or later
 */

add_action('woocommerce_after_checkout_validation', 'wc_block_user_deny_denied_users');
function wc_block_user_deny_denied_users($data)
{
	$email_string = get_option('wc_block_user_list');
	$emails = array_map('trim', explode(",", $email_string));
	if (in_array($data["billing_email"], $emails)) {
		wc_add_notice(sprintf(__("Something went wrong. Please try again later.")), 'error');
	}
}

add_filter('woocommerce_get_sections_account', 'wc_block_user_add_section');
function wc_block_user_add_section($sections)
{
	$sections['wc_block_user'] = __('WC Block User', 'text-domain');
	return $sections;
}

add_filter('woocommerce_get_settings_account', 'wc_block_user_all_settings', 10, 2);
function wc_block_user_all_settings($settings, $current_section)
{
	if ($current_section == 'wc_block_user') {
		$wc_block_user_options = array();
		$wc_block_user_options[] = array('name' => __('WC Block User Settings', 'text-domain'), 'type' => 'title', 'desc' => __('The following options are used to configure WC Block User', 'text-domain'), 'id' => 'wc_block_user');

		$wc_block_user_options[] = array(
			'name'     => __('Email Block List', 'text-domain'),
			'desc_tip' => __('These email addresses will be prevented from checking out.', 'text-domain'),
			'id'       => 'wc_block_user_list',
			'type'     => 'text',
			'desc'     => __('Add a list of comma-separated email addresses that should not be able to check out.', 'text-domain'),
		);

		$wc_block_user_options[] = array('type' => 'sectionend', 'id' => 'wc-block-user');
		return $wc_block_user_options;
	} else {
		return $settings;
	}
}
