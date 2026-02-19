<?php

declare(strict_types=1);

namespace App\Domain\Nutrition\Exception;

/**
 * Exception thrown when raw data from datasource are invalid.
 *
 * Invalid cases include:
 * - Empty string or not setted required fields
 * - Invalid string for specific fields
 */
final class InvalidFoodDataException extends \InvalidArgumentException
{
    /**
     * @param array<string>                                  $missingFields
     * @param array<int, array{name: string, error: string}> $notValidFields
     */
    public function __construct(
        string $message,
        private readonly string $productCode,
        private readonly array $missingFields = [],
        private readonly array $notValidFields = [],
    ) {
        parent::__construct($message);
    }

    public function getProductCode(): string
    {
        return $this->productCode;
    }

    /**
     * @return array<string>
     */
    public function getMissingFields(): array
    {
        return $this->missingFields;
    }

    /**
     * @return array<int, array{name: string, error: string}> $invalidFields
     */
    public function getNotValidFields(): array
    {
        return $this->notValidFields;
    }

    /**
     * Validate required fields.
     *
     * @param array<string> $missingFields
     *
     * @return self Exception with structured error datas
     */
    public static function missingRequiredFields(string $productCode, array $missingFields): self
    {
        $fieldsList = implode(', ', $missingFields);
        $message = sprintf(
            'Product %s has missing required fields: %s',
            $productCode,
            $fieldsList
        );

        return new self($message, $productCode, $missingFields);
    }

    /**
     * Validate invaild fields.
     *
     * @param array<int, array{name: string, error: string}> $invalidFields
     *
     * @return self Exception with structured error data
     */
    public static function notValidFields(string $productCode, array $invalidFields): self
    {
        $fieldsList = [];
        foreach ($invalidFields as $field) {
            $fieldsList[] = sprintf('%s: %s', $field['name'], $field['error']);
        }

        $message = sprintf(
            'Product %s has invalid fields:'.PHP_EOL.'%s',
            $productCode,
            implode(PHP_EOL, $fieldsList)
        );

        return new self($message, $productCode, [], $invalidFields);
    }
}
