<?php
/**
 * Cleanup on uninstall.
 *
 * The page a merchant may have created from the settings screen is their
 * content, so it is left alone: a plugin does not delete pages somebody wrote.
 *
 * @package APG_Legal_Guarantee_Notice
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'apg_guarantee_settings' );
delete_transient( 'apg_guarantee_plugin' );
