<?php

class MCF_Public {

	private $mcf;
	private $version;


	/**
	 * Initialize the class and set its properties.
	 * @since 1.0.0
	 */
	public function __construct( $mcf, $version ) {
		$this->mcf = $mcf;
		$this->version = $version;
	}


	/**
	 * Register the stylesheets for the public-facing side of the site.
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->mcf, plugin_dir_url( __FILE__ ) . 'assets/css/mcf-public.css', array(), $this->version, 'all' );
	}


	/**
	 * Register the JavaScript for the public-facing side of the site.
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->mcf, plugin_dir_url( __FILE__ ) . 'assets/js/mcf-public.js', array( 'jquery' ), $this->version, false );

		$script_data = array(
    	'ajax_url' => site_url('/wp-json/minimal-contact-form/v1/submit')
		);
		wp_localize_script($this->mcf, 'scriptData', $script_data);
	}

}
