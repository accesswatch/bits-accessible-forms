<?php
declare(strict_types=1);

namespace BITS\AccessibleForms\Renderer;

/**
 * Form renderer - handles shortcode and block rendering
 */
class FormRenderer
{
    /**
     * Initialize renderer hooks
     */
    public function init(): void
    {
        add_shortcode('bits_form', [$this, 'render_shortcode']);
        add_action('init', [$this, 'register_block']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    /**
     * Render form via shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function render_shortcode($atts): string
    {
        $atts = shortcode_atts([
            'id' => 0,
        ], $atts, 'bits_form');

        $form_id = (int) $atts['id'];
        
        if ($form_id === 0) {
            return '<div class="bits-form-error">Error: Form ID is required.</div>';
        }

        return $this->render_form($form_id);
    }

    /**
     * Register Gutenberg block
     */
    public function register_block(): void
    {
        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('bits/form', [
            'attributes' => [
                'formId' => [
                    'type' => 'number',
                    'default' => 0,
                ],
            ],
            'render_callback' => [$this, 'render_block'],
        ]);
    }

    /**
     * Render form via block
     *
     * @param array $attributes Block attributes
     * @return string
     */
    public function render_block($attributes): string
    {
        $form_id = isset($attributes['formId']) ? (int) $attributes['formId'] : 0;
        
        if ($form_id === 0) {
            return '<div class="bits-form-error">Error: Please select a form.</div>';
        }

        return $this->render_form($form_id);
    }

    /**
     * Render form HTML
     *
     * @param int $form_id
     * @return string
     */
    private function render_form(int $form_id): string
    {
        global $wpdb;
        $table = $wpdb->prefix . 'bits_forms';

        $form = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id, title, status, schema_json FROM {$table} WHERE id = %d",
                $form_id
            ),
            ARRAY_A
        );

        if (!$form) {
            return '<div class="bits-form-error">Error: Form not found.</div>';
        }

        if ($form['status'] !== 'active') {
            return '<div class="bits-form-error">Error: This form is not currently available.</div>';
        }

        $schema = $form['schema_json'] ? json_decode($form['schema_json'], true) : null;
        
        if (!$schema) {
            return '<div class="bits-form-error">Error: Form has no content.</div>';
        }

        // Minimal shell rendering
        ob_start();
        ?>
        <div class="bits-form-wrapper" data-form-id="<?php echo esc_attr($form_id); ?>">
            <form class="bits-form" id="bits-form-<?php echo esc_attr($form_id); ?>" method="post" novalidate>
                <?php wp_nonce_field('bits_form_submit_' . $form_id, 'bits_form_nonce'); ?>
                <input type="hidden" name="bits_form_id" value="<?php echo esc_attr($form_id); ?>">
                
                <div class="bits-form-header">
                    <h2 class="bits-form-title"><?php echo esc_html($schema['title'] ?? $form['title']); ?></h2>
                    <?php if (!empty($schema['description'])): ?>
                        <div class="bits-form-description"><?php echo wp_kses_post($schema['description']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="bits-form-body">
                    <?php echo $this->render_pages($schema['pages'] ?? []); ?>
                </div>

                <div class="bits-form-footer">
                    <button type="submit" class="bits-form-submit">Submit</button>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render form pages
     *
     * @param array $pages
     * @return string
     */
    private function render_pages(array $pages): string
    {
        $output = '';
        
        foreach ($pages as $page) {
            $output .= '<div class="bits-form-page" data-page-id="' . esc_attr($page['id'] ?? '') . '">';
            
            if (!empty($page['title'])) {
                $output .= '<h3 class="bits-page-title">' . esc_html($page['title']) . '</h3>';
            }
            
            if (isset($page['sections']) && is_array($page['sections'])) {
                foreach ($page['sections'] as $section) {
                    $output .= $this->render_section($section);
                }
            }
            
            $output .= '</div>';
        }
        
        return $output;
    }

    /**
     * Render form section
     *
     * @param array $section
     * @return string
     */
    private function render_section(array $section): string
    {
        $heading_level = $section['headingLevel'] ?? 3;
        $output = '<div class="bits-form-section" data-section-id="' . esc_attr($section['id'] ?? '') . '">';
        
        if (!empty($section['label'])) {
            $output .= '<h' . $heading_level . ' class="bits-section-label">' . esc_html($section['label']) . '</h' . $heading_level . '>';
        }
        
        if (!empty($section['description'])) {
            $output .= '<div class="bits-section-description">' . wp_kses_post($section['description']) . '</div>';
        }
        
        if (isset($section['fields']) && is_array($section['fields'])) {
            foreach ($section['fields'] as $field) {
                $output .= $this->render_field($field);
            }
        }
        
        $output .= '</div>';
        
        return $output;
    }

    /**
     * Render form field (minimal implementation)
     *
     * @param array $field
     * @return string
     */
    private function render_field(array $field): string
    {
        $field_id = $field['id'] ?? 0;
        $field_type = $field['type'] ?? 'text';
        $label = $field['label'] ?? '';
        $is_required = $field['isRequired'] ?? false;
        
        $output = '<div class="bits-field bits-field-' . esc_attr($field_type) . '" data-field-id="' . esc_attr($field_id) . '">';
        
        // Label
        if ($label) {
            $output .= '<label for="bits-field-' . esc_attr($field_id) . '" class="bits-field-label">';
            $output .= esc_html($label);
            if ($is_required) {
                $output .= ' <span class="bits-required" aria-label="required">*</span>';
            }
            $output .= '</label>';
        }
        
        // Help text
        if (!empty($field['helpText'])) {
            $output .= '<div class="bits-field-help">' . esc_html($field['helpText']) . '</div>';
        }
        
        // Field input (minimal)
        $output .= $this->render_field_input($field);
        
        $output .= '</div>';
        
        return $output;
    }

    /**
     * Render field input element
     *
     * @param array $field
     * @return string
     */
    private function render_field_input(array $field): string
    {
        $field_id = $field['id'] ?? 0;
        $field_type = $field['type'] ?? 'text';
        $placeholder = $field['placeholder'] ?? '';
        $is_required = $field['isRequired'] ?? false;
        
        $attrs = [
            'id' => 'bits-field-' . $field_id,
            'name' => 'bits_field[' . $field_id . ']',
            'class' => 'bits-input',
        ];
        
        if ($is_required) {
            $attrs['required'] = 'required';
        }
        
        if ($placeholder) {
            $attrs['placeholder'] = $placeholder;
        }
        
        $attrs_str = '';
        foreach ($attrs as $key => $value) {
            if ($value === 'required') {
                $attrs_str .= ' ' . esc_attr($key);
            } else {
                $attrs_str .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
            }
        }
        
        // Map field types to HTML input types
        $input_type_map = [
            'text' => 'text',
            'email' => 'email',
            'website' => 'url',
            'password' => 'password',
            'date' => 'date',
            'time' => 'time',
            'hidden' => 'hidden',
        ];
        
        if (isset($input_type_map[$field_type])) {
            return '<input type="' . esc_attr($input_type_map[$field_type]) . '"' . $attrs_str . '>';
        } elseif ($field_type === 'textarea') {
            return '<textarea' . $attrs_str . '></textarea>';
        } elseif ($field_type === 'select') {
            $output = '<select' . $attrs_str . '>';
            $output .= '<option value="">Select...</option>';
            if (isset($field['choices']) && is_array($field['choices'])) {
                foreach ($field['choices'] as $choice) {
                    $output .= '<option value="' . esc_attr($choice['value'] ?? '') . '">' . esc_html($choice['text'] ?? '') . '</option>';
                }
            }
            $output .= '</select>';
            return $output;
        }
        
        // Default to text input
        return '<input type="text"' . $attrs_str . '>';
    }

    /**
     * Enqueue renderer assets
     */
    public function enqueue_assets(): void
    {
        // Only enqueue if there's a form on the page
        if (!$this->has_form_shortcode() && !has_block('bits/form')) {
            return;
        }
        
        $plugin_url = plugins_url('', dirname(__DIR__));
        
        wp_enqueue_style(
            'bits-form-renderer',
            $plugin_url . '/assets/css/renderer.css',
            [],
            '1.0.0'
        );
        
        wp_enqueue_script(
            'bits-form-renderer',
            $plugin_url . '/assets/js/renderer.js',
            [],
            '1.0.0',
            true
        );
    }

    /**
     * Check if current post has bits_form shortcode
     *
     * @return bool
     */
    private function has_form_shortcode(): bool
    {
        global $post;
        
        if (!$post || !isset($post->post_content)) {
            return false;
        }
        
        return has_shortcode($post->post_content, 'bits_form');
    }
}
