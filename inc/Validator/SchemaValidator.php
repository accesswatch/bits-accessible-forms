<?php
declare(strict_types=1);

namespace BITS\AccessibleForms\Validator;

/**
 * Basic JSON Schema validator for form structure
 */
class SchemaValidator
{
    private array $schema;
    private array $errors = [];

    public function __construct()
    {
        $schema_path = dirname(dirname(__DIR__)) . '/schema/form.schema.json';
        $schema_json = file_get_contents($schema_path);
        $this->schema = json_decode($schema_json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Failed to load form schema: ' . json_last_error_msg());
        }
    }

    /**
     * Validate form data against schema
     *
     * @param array $data Form data to validate
     * @return bool True if valid
     */
    public function validate(array $data): bool
    {
        $this->errors = [];
        
        // Check required fields
        if (!isset($data['title']) || empty($data['title'])) {
            $this->errors[] = 'title is required';
        }
        
        if (!isset($data['pages']) || !is_array($data['pages']) || count($data['pages']) === 0) {
            $this->errors[] = 'At least one page is required';
        }
        
        // Validate title length
        if (isset($data['title']) && strlen($data['title']) > 255) {
            $this->errors[] = 'title must be 255 characters or less';
        }
        
        // Validate pages structure
        if (isset($data['pages']) && is_array($data['pages'])) {
            foreach ($data['pages'] as $idx => $page) {
                $this->validatePage($page, $idx);
            }
        }
        
        return count($this->errors) === 0;
    }

    /**
     * Get validation errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    private function validatePage(array $page, int $index): void
    {
        if (!isset($page['id'])) {
            $this->errors[] = "Page at index {$index} is missing 'id'";
        }
        
        if (!isset($page['sections']) || !is_array($page['sections'])) {
            $this->errors[] = "Page at index {$index} is missing 'sections' array";
            return;
        }
        
        foreach ($page['sections'] as $sidx => $section) {
            $this->validateSection($section, $index, $sidx);
        }
    }

    private function validateSection(array $section, int $pageIndex, int $sectionIndex): void
    {
        if (!isset($section['id'])) {
            $this->errors[] = "Section at page {$pageIndex}, index {$sectionIndex} is missing 'id'";
        }
        
        if (!isset($section['fields']) || !is_array($section['fields'])) {
            $this->errors[] = "Section at page {$pageIndex}, index {$sectionIndex} is missing 'fields' array";
            return;
        }
        
        foreach ($section['fields'] as $fidx => $field) {
            $this->validateField($field, $pageIndex, $sectionIndex, $fidx);
        }
    }

    private function validateField(array $field, int $pageIndex, int $sectionIndex, int $fieldIndex): void
    {
        if (!isset($field['id'])) {
            $this->errors[] = "Field at page {$pageIndex}, section {$sectionIndex}, index {$fieldIndex} is missing 'id'";
        }
        
        if (!isset($field['type'])) {
            $this->errors[] = "Field at page {$pageIndex}, section {$sectionIndex}, index {$fieldIndex} is missing 'type'";
        }
        
        if (!isset($field['label'])) {
            $this->errors[] = "Field at page {$pageIndex}, section {$sectionIndex}, index {$fieldIndex} is missing 'label'";
        }
        
        // Validate field type is in allowed list
        $allowedTypes = [
            'email', 'text', 'textarea', 'select', 'checkbox', 'radio',
            'name', 'address', 'date', 'time', 'website', 'password',
            'fileupload', 'list', 'multiselect', 'html', 'hidden',
            'section', 'page', 'consent', 'image_choice', 'product',
            'quantity', 'options', 'shipping', 'total', 'price_user'
        ];
        
        if (isset($field['type']) && !in_array($field['type'], $allowedTypes, true)) {
            $this->errors[] = "Field {$field['id']} has invalid type '{$field['type']}'";
        }
    }
}
