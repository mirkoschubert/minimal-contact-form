=== Minimal Contact Form ===
Contributors: mirkoschubert
Tags: contact, contact form, email, feedback, form, gdpr, dsgvo, simple, minimal
Donate link: https://www.paypal.me/mirkoschubert
Requires at least: 4.9.6
Tested up to: 5.7
Requires PHP: 7.2
Stable tag: 0.8.2
License: GPL3
License URI: https://tldrlegal.com/license/gnu-general-public-license-v3-(gpl-3)

Minimal Contact Form is a simple, clean and secure contact form that meets the requirements of the GDPR.

== Description ==

Almost everyone needs a contact form for their website. But you quickly realize that it is not that easy to find a suitable plugin.

Either you end up with huge form builders that are simply too bulky for a single contact form, or you find a small plugin that doesn't meet your requirements.

= Features =

**Minimal Contact Form** is designed to address precisely this problem:

* It is a simple contact form without a lot of extras.
* All form fields are validated and sanitized.
* You can choose to whom the emails are sent.
* The form data is sent via AJAX.
* Sending with WP Mail (SMTP) or PHP Mail is selectable. 
* No annoying captchas are used for spam detection, but the honeypot method.
* Predefined GDPR-compliant messages with or without checkbox will be displayed.
* Those messages are linked directly to the privacy policy.
* In case of consent, the recipient will be informed directly in the e-mail.
* The plugin is completely translatable (English & German already available).

All this with a total of **four** settings!

= Requirements =

* Due to the new privacy options, this plugin requires WordPress 4.9.6 or up.
* You must setup a privacy policy page in WordPress.

= Translations =

If you want to translate this plugin into another language, you are welcome! English and German (informal) are already there. I used the plugin [Loco Translate](https://wordpress.org/plugins/loco-translate/), but I suppose that other ways should be compatible as well.

= See room for improvement? =

Great! There are several ways you can get involved to help make this plugin better:

1. **Report Bugs:** If you find a bug, error or other problem, please report it! You can do this by [creating a new topic](http://wordpress.org/support/plugin/minimal-contact-form) in the plugin forum or [creating an issue](https://github.com/mirkoschubert/minimal-contact-form/issues) on Github.
2. **Suggest New Features:** Have an awesome idea? Please share it! Simply [create a new topic](https://wordpress.org/support/plugin/minimal-contact-form) in the plugin forum to express your thoughts on why the feature should be included and get a discussion going around your idea.
3. **Issue Pull Requests:** If you're a developer, the easiest way to get involved is to help out on [issues already reported](https://github.com/mirkoschubert/minimal-contact-form/issues) in GitHub.


[**Checkout our GitHub Repository**](https://github.com/mirkoschubert/minimal-contact-form)

== Installation ==

1. Upload the entire `minimal-contact-form` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Place the shortcode `[minimal_contact_form]` in your contact page.

You will find 'Contact Form' menu in your WordPress options panel.

For basic usage, please read the 'Overview' section directly in the plugin settings.

== Upgrade Notice ==

You will be notified in your WordPress installation under `Dashboard`-`Updates` when an update is available. 

== Frequently Asked Questions ==

If you have any questions, please use the [support forum](https://wordpress.org/support/plugin/minimal-contact-form). If there are frequently asked question I will sum them up here.

== Screenshots ==

1. Contact form on frontend with GDPR Opt-In option on
2. Minimal Contact Form Settings

== Changelog ==

= 0.8.2 =

* GitHub Actions for Deployment
* Tested up to WordPress 5.7

= 0.8.1 =

* No Changes, but tested up to WordPress 5.3

= 0.8.0 =

* Design changes for validation
* Optional field for phone numbers
* Bugfix: Version number update

= 0.7.2 =

* New language: German (formal)

= 0.6.4 =

* Readme and License for Github
* WordPress SVN Assets fix (hopefully)
* Small changes in the deploy.sh (which won't be published anymore)

= 0.6.3 =

* This is the first release on WordPress repository. Sorry, no other changes yet.

= 0.6.2 =

* This is the first official release!