<?php

namespace MinimalContactForm\Public;

/**
 * Form Renderer
 *
 * Handles form HTML rendering based on field_groups configuration.
 * Replaces the legacy MCF_Form class.
 *
 * @since 1.0.0
 */
class FormRenderer
{
    /**
     * Plugin version
     *
     * @var string
     */
    private $version;

    /**
     * Plugin options
     *
     * @var array
     */
    private $options;

    /**
     * Constructor
     *
     * @param string $version Plugin version
     */
    public function __construct($version)
    {
        $this->version = $version;
        $this->options = get_option('mcf_options');
    }

    /**
     * Register hooks
     *
     * @since 1.0.0
     */
    public function register()
    {
        add_shortcode('minimal_contact_form', [$this, 'render_shortcode']);
    }

    /**
     * Render shortcode
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Form HTML
     */
    public function render_shortcode($atts = [])
    {
        ob_start();
        $this->render_form();
        return ob_get_clean();
    }

    /**
     * Render the complete form
     *
     * @since 1.0.0
     */
    private function render_form()
    {
        $styling = $this->options['styling'] ?? [];
        $variant = $styling['variant'] ?? 'light';
        $field_groups = $this->options['fields']['field_groups'] ?? [];

        ?>
        <!-- Minimal Contact Form v<?php echo esc_attr($this->version); ?> -->
        <div id="minimal-contact-form" class="mcf-form" data-theme-variant="<?php echo esc_attr($variant); ?>">
            <div class="mcf-notice" style="display: none;"></div>

            <form class="mcf-contact-form" method="post" novalidate>
                <div class="mcf-grid">
                    <?php
                    // Company field
                    if (!empty($field_groups['company']['enabled'])) {
                        $this->render_field('company', 'text', false, 'organization', true);
                    }

                    // Name fields
                    if (!empty($field_groups['name']['enabled'])) {
                        if ($field_groups['name']['mode'] === 'single') {
                            $this->render_field('name', 'text', true, 'name', true);
                        } else {
                            // Split mode (first-name + last-name)
                            $this->render_field('first-name', 'text', true, 'given-name');
                            $this->render_field('last-name', 'text', true, 'family-name');
                        }
                    }

                    // Contact fields
                    if (!empty($field_groups['contact']['enabled'])) {
                        if ($field_groups['contact']['mode'] === 'email-phone') {
                            $this->render_field('email', 'email', true, 'email');
                            $this->render_field('phone', 'tel', false, 'tel');
                        } else {
                            $this->render_field('email', 'email', true, 'email', true);
                        }
                    }

                    // Subject field
                    if (!empty($field_groups['subject']['enabled'])) {
                        $this->render_field('subject', 'text', true, 'off', true);
                    }

                    // Message field (always enabled)
                    $this->render_textarea('message', true);

                    // GDPR/Privacy
                    $this->render_privacy();
                    ?>
                </div>

                <?php
                // Security fields (honeypot, CSRF, timestamp)
                $this->render_security();

                // Submit button
                $this->render_submit($field_groups['submit']['alignment'] ?? 'left');
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Render an input field
     *
     * @since 1.0.0
     * @param string $field_id Field ID
     * @param string $type Input type
     * @param bool $required Whether field is required
     * @param string $autocomplete Autocomplete attribute value
     * @param bool $full_width Whether field should span full width in grid
     */
    private function render_field($field_id, $type = 'text', $required = false, $autocomplete = 'off', $full_width = false)
    {
        $labels = $this->options['fields']['labels'] ?? [];
        $placeholders = $this->options['fields']['placeholders'] ?? [];
        $hide_labels = $this->options['fields']['hide_labels'] ?? false;

        $label = $labels[$field_id] ?? ucwords(str_replace('-', ' ', $field_id));

        // Compute placeholder based on hide_labels setting
        if ($hide_labels) {
            // Labels are hidden → Placeholder = Label + asterisk (if required)
            $placeholder = $required ? $label . ' *' : $label;
        } else {
            // Labels are visible → Only use explicitly entered placeholder
            $placeholder = $placeholders[$field_id] ?? '';
        }

        $label_class = $hide_labels ? 'mcf-label mcf-sr-only' : 'mcf-label';

        ?>
        <div class="mcf-field mcf-field-<?php echo esc_attr($field_id); ?>" <?php echo $full_width ? 'data-full-width="true"' : ''; ?>>
            <label for="mcf-<?php echo esc_attr($field_id); ?>" class="<?php echo esc_attr($label_class); ?>">
                <?php echo esc_html($label); ?>
                <?php if ($required): ?>
                    <span class="required" aria-label="<?php esc_attr_e('required', 'mcf'); ?>">*</span>
                <?php endif; ?>
            </label>
            <input
                type="<?php echo esc_attr($type); ?>"
                id="mcf-<?php echo esc_attr($field_id); ?>"
                name="<?php echo esc_attr($field_id); ?>"
                class="mcf-input"
                placeholder="<?php echo esc_attr($placeholder); ?>"
                autocomplete="<?php echo esc_attr($autocomplete); ?>"
                <?php echo $required ? 'required aria-required="true"' : ''; ?>
            />
        </div>
        <?php
    }

    /**
     * Render textarea field
     *
     * @since 1.0.0
     * @param string $field_id Field ID
     * @param bool $required Whether field is required
     */
    private function render_textarea($field_id, $required = false)
    {
        $labels = $this->options['fields']['labels'] ?? [];
        $placeholders = $this->options['fields']['placeholders'] ?? [];
        $hide_labels = $this->options['fields']['hide_labels'] ?? false;

        $label = $labels[$field_id] ?? ucwords(str_replace('-', ' ', $field_id));

        // Compute placeholder based on hide_labels setting
        if ($hide_labels) {
            // Labels are hidden → Placeholder = Label + asterisk (if required)
            $placeholder = $required ? $label . ' *' : $label;
        } else {
            // Labels are visible → Only use explicitly entered placeholder
            $placeholder = $placeholders[$field_id] ?? '';
        }

        $label_class = $hide_labels ? 'mcf-label mcf-sr-only' : 'mcf-label';

        ?>
        <div class="mcf-field mcf-field-<?php echo esc_attr($field_id); ?>" data-full-width="true">
            <label for="mcf-<?php echo esc_attr($field_id); ?>" class="<?php echo esc_attr($label_class); ?>">
                <?php echo esc_html($label); ?>
                <?php if ($required): ?>
                    <span class="required" aria-label="<?php esc_attr_e('required', 'mcf'); ?>">*</span>
                <?php endif; ?>
            </label>
            <textarea
                id="mcf-<?php echo esc_attr($field_id); ?>"
                name="<?php echo esc_attr($field_id); ?>"
                class="mcf-textarea"
                rows="4"
                placeholder="<?php echo esc_attr($placeholder); ?>"
                <?php echo $required ? 'required aria-required="true"' : ''; ?>
            ></textarea>
        </div>
        <?php
    }

    /**
     * Render privacy/GDPR field
     *
     * @since 1.0.0
     */
    private function render_privacy()
    {
        $field_groups = $this->options['fields']['field_groups'] ?? [];
        $privacy_texts = $this->options['privacy_texts'] ?? [];
        $labels = $this->options['fields']['labels'] ?? [];

        $gdpr_mode = $field_groups['gdpr']['mode'] ?? 'inform';

        if ($gdpr_mode === 'optin') {
            // Opt-in checkbox (required)
            $label = $labels['gdpr-optin'] ?? $privacy_texts['optin_text'] ?? '';
            ?>
            <div class="mcf-field mcf-field-privacy">
                <label class="mcf-checkbox-label">
                    <input
                        type="checkbox"
                        id="mcf-privacy"
                        name="privacy"
                        class="mcf-checkbox"
                        required
                        aria-required="true"
                    />
                    <span class="mcf-checkbox-text"><?php echo esc_html($label); ?> <span class="required">*</span></span>
                </label>
            </div>
            <?php
        } else {
            // Inform mode (no checkbox, just text)
            $label = $labels['gdpr-inform'] ?? $privacy_texts['inform_text'] ?? '';
            ?>
            <div class="mcf-field mcf-field-privacy">
                <p class="mcf-privacy-text"><?php echo esc_html($label); ?></p>
            </div>
            <?php
        }
    }

    /**
     * Render security fields (honeypot, nonce)
     *
     * @since 1.0.0
     */
    private function render_security()
    {
        // Generate WordPress nonce
        $nonce = wp_create_nonce('mcf_submit');

        ?>
        <!-- Security Fields -->
        <div class="mcf-security" style="display: none;" aria-hidden="true">
            <!-- WordPress Nonce -->
            <input type="hidden" name="_wpnonce" value="<?php echo esc_attr($nonce); ?>" />
        </div>

        <!-- Honeypot Fields (multiple for better bot detection) -->
        <div style="position: absolute; left: -5000px;" aria-hidden="true" tabindex="-1">
            <input type="text" name="website" value="" tabindex="-1" autocomplete="off" />
            <input type="text" name="url" value="" tabindex="-1" autocomplete="off" />
            <input type="email" name="business_email" value="" tabindex="-1" autocomplete="off" />
        </div>
        <?php
    }

    /**
     * Render submit button
     *
     * @since 1.0.0
     * @param string $alignment Button alignment (left or right)
     */
    private function render_submit($alignment = 'left')
    {
        $labels = $this->options['fields']['labels'] ?? [];
        $label = $labels['submit'] ?? __('Submit', 'mcf');

        ?>
        <div class="mcf-field mcf-field-submit mcf-submit-<?php echo esc_attr($alignment); ?>">
            <button type="submit" class="mcf-submit-button">
                <?php echo esc_html($label); ?>
            </button>
        </div>
        <?php
    }
}
