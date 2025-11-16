<?php
declare(strict_types=1);

namespace BITS\AccessibleForms\Tests\Unit;

use BITS\AccessibleForms\Validator\SchemaValidator;
use PHPUnit\Framework\TestCase;

class SchemaValidatorTest extends TestCase
{
    private SchemaValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new SchemaValidator();
    }

    public function test_valid_minimal_schema(): void
    {
        $data = [
            'title' => 'Test Form',
            'pages' => [
                [
                    'id' => 'p1',
                    'sections' => [
                        [
                            'id' => 's1',
                            'fields' => [
                                [
                                    'id' => 1,
                                    'type' => 'text',
                                    'label' => 'Name',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertTrue($this->validator->validate($data));
        $this->assertEmpty($this->validator->getErrors());
    }

    public function test_missing_title(): void
    {
        $data = [
            'pages' => [
                [
                    'id' => 'p1',
                    'sections' => [],
                ],
            ],
        ];

        $this->assertFalse($this->validator->validate($data));
        $this->assertContains('title is required', $this->validator->getErrors());
    }

    public function test_empty_title(): void
    {
        $data = [
            'title' => '',
            'pages' => [
                [
                    'id' => 'p1',
                    'sections' => [],
                ],
            ],
        ];

        $this->assertFalse($this->validator->validate($data));
        $this->assertContains('title is required', $this->validator->getErrors());
    }

    public function test_title_too_long(): void
    {
        $data = [
            'title' => str_repeat('a', 256),
            'pages' => [
                [
                    'id' => 'p1',
                    'sections' => [],
                ],
            ],
        ];

        $this->assertFalse($this->validator->validate($data));
        $errors = $this->validator->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('255 characters', implode(' ', $errors));
    }

    public function test_missing_pages(): void
    {
        $data = [
            'title' => 'Test Form',
        ];

        $this->assertFalse($this->validator->validate($data));
        $this->assertContains('At least one page is required', $this->validator->getErrors());
    }

    public function test_empty_pages_array(): void
    {
        $data = [
            'title' => 'Test Form',
            'pages' => [],
        ];

        $this->assertFalse($this->validator->validate($data));
        $this->assertContains('At least one page is required', $this->validator->getErrors());
    }

    public function test_page_missing_id(): void
    {
        $data = [
            'title' => 'Test Form',
            'pages' => [
                [
                    'sections' => [],
                ],
            ],
        ];

        $this->assertFalse($this->validator->validate($data));
        $errors = $this->validator->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('missing \'id\'', implode(' ', $errors));
    }

    public function test_section_missing_fields(): void
    {
        $data = [
            'title' => 'Test Form',
            'pages' => [
                [
                    'id' => 'p1',
                    'sections' => [
                        [
                            'id' => 's1',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertFalse($this->validator->validate($data));
        $errors = $this->validator->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('missing \'fields\'', implode(' ', $errors));
    }

    public function test_field_missing_required_properties(): void
    {
        $data = [
            'title' => 'Test Form',
            'pages' => [
                [
                    'id' => 'p1',
                    'sections' => [
                        [
                            'id' => 's1',
                            'fields' => [
                                [
                                    // Missing id, type, label
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertFalse($this->validator->validate($data));
        $errors = $this->validator->getErrors();
        $this->assertGreaterThanOrEqual(3, count($errors)); // Should have errors for id, type, and label
    }

    public function test_invalid_field_type(): void
    {
        $data = [
            'title' => 'Test Form',
            'pages' => [
                [
                    'id' => 'p1',
                    'sections' => [
                        [
                            'id' => 's1',
                            'fields' => [
                                [
                                    'id' => 1,
                                    'type' => 'invalid_type',
                                    'label' => 'Test',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertFalse($this->validator->validate($data));
        $errors = $this->validator->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('invalid type', implode(' ', $errors));
    }

    public function test_complex_valid_schema(): void
    {
        $data = [
            'title' => 'Complex Form',
            'description' => 'A complex form with multiple pages',
            'settings' => [
                'currency' => 'USD',
            ],
            'pages' => [
                [
                    'id' => 'p1',
                    'title' => 'Page 1',
                    'sections' => [
                        [
                            'id' => 's1',
                            'label' => 'Contact Info',
                            'fields' => [
                                [
                                    'id' => 1,
                                    'type' => 'text',
                                    'label' => 'Name',
                                    'isRequired' => true,
                                ],
                                [
                                    'id' => 2,
                                    'type' => 'email',
                                    'label' => 'Email',
                                    'isRequired' => true,
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'id' => 'p2',
                    'title' => 'Page 2',
                    'sections' => [
                        [
                            'id' => 's2',
                            'label' => 'Additional Info',
                            'fields' => [
                                [
                                    'id' => 3,
                                    'type' => 'textarea',
                                    'label' => 'Comments',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertTrue($this->validator->validate($data));
        $this->assertEmpty($this->validator->getErrors());
    }
}
