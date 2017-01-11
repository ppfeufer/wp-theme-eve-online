<?php
/**
 * Whitelabeling WordPress
 */

namespace WordPress\Themes\EveOnline\Plugins;

use WordPress\Themes\EveOnline;

\defined('ABSPATH') or die();

class Whitelabel {
	private $textdomain = null;

	private $developerName = null;
	private $developerEmailAddress = null;
	private $developerWebsite = null;

	private $themeName = null;
	private $themeGithubUri = null;
	private $themeGithubIssueUri = null;

	private $themeBackgroundUrl = null;
	private $customLoginLogo = null;

	private $themeSettings = null;

	private $eveApi = null;

	/**
	 * Fire the actions to whitelabel WordPress
	 *
	 * Maybe edit the .htaccess file aswell
	 * 		RewriteRule ^login$ http://www.website.de/wp-login.php [NC,L]
	 */
	function __construct() {
		/**
		 * Translation Stuff
		 */
		$this->textdomain = 'eve-online';

		/**
		 * Setting Theme Informations
		 */
		$this->themeName = 'EVE Online';
		$this->themeGithubUri = 'https://github.com/ppfeufer/eve-online-wordpress-theme';
		$this->themeGithubIssueUri = 'https://github.com/ppfeufer/eve-online-wordpress-theme/issues';

		/**
		 * Setting Developer Information
		 */
		$this->developerName = 'YF [TN-NT] Rounon Dax';
		$this->developerEmailAddress = 'rounon.dax@yulai-federation.net';
		$this->developerWebsite = 'http://yulaifederation.net';

		$this->themeBackgroundUrl = $this->getBackgroundImage();
		$this->customLoginLogo = $this->getLoginLogo();

		$this->themeSettings = \get_option('eve_theme_options', EveOnline\eve_get_options_default());

		$this->eveApi = new EveOnline\Helper\EveApiHelper;

		$this->initActions();
		$this->initFilters();
	} // END function __construct()

	public function initActions() {
		/**
		 * Actions
		 */
		\add_action('login_head', array($this, 'customLoginLogoStyle'));
		\add_action('wp_dashboard_setup', array($this, 'addDashboardWidget'));
	} // END public function initActions()

	public function initFilters() {
		/**
		 * Filters
		 */
		\add_filter('admin_footer_text', array($this, 'modifyAdminFooter'));
		\add_filter('login_headerurl', array($this, 'loginLogoUrl'));
		\add_filter('login_headertitle', array($this, 'loginLogoTitle'));
	} // END public function initFilters()

	private function getBackgroundImage() {
		return EveOnline\eve_get_theme_background_image();
	} // END private function getBackgroundImage()

	/**
	 * Custom URL Title
	 *
	 * @return Ambigous <string, mixed>
	 */
	public function loginLogoTitle() {
//		return __('Yulai Federation - NRDS Provibloc Alliance', $this->textdomain);
		return \get_bloginfo('name') . ' - ' . \get_bloginfo('description');
	} // END public function loginLogoTitle()

	/**
	 * Custom URL linked by the Logo on Login page
	 *
	 * @return Ambigous <string, mixed, boolean>
	 */
	public function loginLogoUrl() {
		return get_bloginfo('wpurl');
	} // END public function loginLogoUrl()

	/**
	 * Developer Info in Admin Footer
	 */
	public function modifyAdminFooter() {
		echo sprintf('<span id="footer-thankyou">%1$s</span> %2$s',
			__('Customized by:', $this->textdomain),
			' <a href="' . $this->developerWebsite . '" target="_blank">' . $this->developerName . '</a>'
		);
	} // END public function modifyAdminFooter()

	/**
	 * Dashboard Widget with Developer Contact Info
	 */
	public function themeInfo() {
		echo '<ul>
		<li>
			<strong>' . __('Theme:', $this->textdomain) . '</strong> ' . $this->themeName .
			sprintf(__(' (%1$s | %2$s)', $this->textdomain),
				'<a href="' . $this->themeGithubUri . '">Github</a>',
				'<a href="' . $this->themeGithubIssueUri . '">Issue Tracker</a>'
			) . '
		</li>
		<li><strong>' . __('Customized by:', $this->textdomain) . '</strong> ' . $this->developerName . '</li>
		<li><strong>' . __('Website:', $this->textdomain) . '</strong> <a href="' . $this->developerWebsite . '">' . $this->developerWebsite . '</a></li>
		<li><strong>' . __('Contact:',  $this->textdomain) . '</strong> <a href="mailto:' . $this->developerEmailAddress . '">' . $this->developerEmailAddress . '</a></li>
		</ul>';
	} // END public function themeInfo()

	public function addDashboardWidget() {
		\wp_add_dashboard_widget('wp_dashboard_widget', __('Developer Contact', $this->textdomain), array($this, 'themeInfo'));
	} // END public function addDashboardWidget()

	/**
	 * Custom Logo on Login Page
	 */
	public function customLoginLogoStyle() {
		$type = (!empty($this->themeSettings['type'])) ? $this->themeSettings['type'] : null;
		$loginLogo = $this->getLoginLogo();
		$hasCustomLoginLogo = (!empty($this->themeSettings['custom_login_logo'])) ? true : false;

		$size = 320;

		if($loginLogo === null) {
			$size = 1;
		} // END if($loginLogo === null)

		if($type !== null && $hasCustomLoginLogo === false) {
				$size = ($type === 'alliance') ? 128 : 256;
		} // END if($type !== null && $hasCustomLoginLogo === false)

		echo '<style type="text/css">
		body {
			background-image: url("' . $this->themeBackgroundUrl . '");
			background-position: center center;
			background-repeat: no-repeat;
			background-size: cover;
			background-attachment: fixed;
		}
		h1 a {
			background-image:url(' . $loginLogo . ') !important;
			background-size: cover !important;
			height: ' . $size . 'px !important;
			width: ' . $size . 'px !important;
			height: ' . 1 / 16 * $size . 'rem !important;
			width: ' . 1 / 16 * $size . 'rem !important;
			margin-bottom: 0 !important;
			padding-bottom: 0 !important;
		}
		.login form {
			margin-top: 10px !important;
			margin-top: 0.625rem !important;
			background-color: rgba(255,255,255,0.7);
		}
		.login input[type="text"], .login input[type="password"] {
			background-color: rgba(251,251,251,0.5);
		}
		#login {
			padding: 0;
			display: table;
			margin: auto;
			position: absolute;
			top: 0;
			left: 0;
			bottom: 0;
			right: 0;
		}
		</style>';
	} // END public function customLoginLogoStyle()

	private function getLoginLogo() {
		$logo = null;

		/**
		 * Check if we have a custom login logo or now and use
		 * what ever we might have as logo here.
		 */
		if(!empty($this->themeSettings['custom_login_logo'])) {
		} else {
			$type = (!empty($this->themeSettings['type'])) ? $this->themeSettings['type'] : null;
			$name = (!empty($this->themeSettings['name'])) ? $this->themeSettings['name'] : null;

			if($type !== null && $name !== null) {
				$size = ($type === 'alliance') ? 128 : 256;
				// getting the logo
				$logo = $this->eveApi->getEntityLogoByName($name, true, $size);
			} // END if($type !== null && $name !== null)
		} // END if(!empty($this->themeSettings['custom_login_logo']))

		return $logo;
	} // END private function getLoginLogo()
} // END class Whitelabel

new Whitelabel;