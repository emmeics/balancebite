<?php

namespace App\Application\Exception;

/**
 * Exception thrown when validation fails.
 *
 * Contains detailed information about which fields failed validation
 * and why, allowing for structured error responses.
 */
class ValidationException extends \Exception
{
    /** @var array<string, string> Field names mapped to error messages */
    private array $fields;

    /**
     * @param array<string, string> $fields   Array of field => error message
     * @param int                   $code     Exception code
     * @param \Throwable|null       $previous Previous exception
     */
    public function __construct(array $fields = [], int $code = 0, ?\Throwable $previous = null)
    {
        $this->fields = $fields;

        parent::__construct('Validation Failed', $code, $previous);
    }

    /**
     * Get validation errors formatted for JSON response.
     *
     * @return array<int, array{field: string, message: string}>
     */
    public function getErrors(): array
    {
        $errors = [];
        foreach ($this->fields as $field => $message) {
            $errors[] = [
                'field' => $field,
                'message' => $message,
            ];
        }

        return $errors;
    }
}
