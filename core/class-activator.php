<?php

class MCF_Activator {

	/**
	 * Activation
	 * @since 1.0.0
	 */
	public static function activate() {
    
    // Write default options to database
    add_option(
      'mcf_options',
      array(
        'user' => 1,
        'gdpr' => 'no',
        'spam' => 'yes',
        'phpmail' => 'no',
        'phone' => 'no',
        'hidesubject' => 'no',
        'oneline' => 'no',
        'labels' => 'no',
        'css' => '#minimal-contact-form {}'
      ),
      '',
      'yes'
    );
  }
}
