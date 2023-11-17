<?php
namespace SiteGround_Optimizer\Activator;

use SiteGround_Optimizer\Helper\Helper;
use SiteGround_Optimizer\Memcache\Memcache;
use SiteGround_Optimizer\Options\Options;
use SiteGround_Optimizer\Analysis\Analysis;
use SiteGround_Optimizer\Install_Service\Install_Service;

class Activator {
	/**
	 * Run on plugin activation.
	 *
	 * @since 5.0.9
	 */
	public function activate() {
		$this->maybe_create_memcache_dropin();
		$this->maybe_set_data_consent_default();

		$install_service = new Install_Service();
		$install_service->install();

		$analysis = new Analysis();
		$analysis->check_for_premigration_test();
	}

	/**
	 * Check if memcache options was enabled and create the memcache dropin.
	 *
	 * @since  5.0.9
	 */
	public function maybe_create_memcache_dropin() {
		if ( Options::is_enabled( 'siteground_optimizer_enable_memcached' ) ) {
			$memcached = new Memcache();
			$memcached->remove_memcached_dropin();
			$memcached->create_memcached_dropin();
		}
	}

	/**
	 * Set the default data consent.
	 *
	 * @since 7.4.3
	 */
	public function maybe_set_data_consent_default() {
		// Check if we have a new installation and bail if we do not meet the new user criteria or the user has modified its consent settings.
		if ( ! empty( get_option( 'siteground_data_consent_timestamp', '' ) ) ) {
			return;
		}

		if ( '0.0.0' === get_option( 'siteground_optimizer_version', '0.0.0' ) ) {
			// Update the consent option.
			update_option( 'siteground_data_consent', 1 );
		}
	}
}
