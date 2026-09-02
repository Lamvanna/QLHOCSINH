<?php
// core/Validator.php

class Validator {
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules) {
        $this->data = $data;
        $this->rules = $rules;
        $this->validate();
    }

    public static function make(array $data, array $rules): self {
        return new self($data, $rules);
    }

    public function passes(): bool {
        return empty($this->errors);
    }

    public function fails(): bool {
        return !empty($this->errors);
    }

    public function errors(): array {
        return $this->errors;
    }

    public function firstError(): ?string {
        foreach ($this->errors as $fieldErrors) {
            if (!empty($fieldErrors)) {
                return $fieldErrors[0];
            }
        }
        return null;
    }

    private function validate(): void {
        foreach ($this->rules as $field => $fieldRules) {
            $rulesList = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;
            $value = $this->data[$field] ?? null;

            foreach ($rulesList as $rule) {
                $ruleName = $rule;
                $ruleParam = null;

                if (str_contains($rule, ':')) {
                    [$ruleName, $ruleParam] = explode(':', $rule, 2);
                }

                if ($ruleName === 'required' && (is_null($value) || $value === '' || (is_array($value) && empty($value)))) {
                    $this->addError($field, "Trường '{$field}' là bắt buộc.");
                }

                if ($value !== null && $value !== '') {
                    if ($ruleName === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->addError($field, "Trường '{$field}' phải là một địa chỉ email hợp lệ.");
                    }
                    if ($ruleName === 'numeric' && !is_numeric($value)) {
                        $this->addError($field, "Trường '{$field}' phải là dạng số.");
                    }
                    if ($ruleName === 'min' && strlen((string)$value) < (int)$ruleParam) {
                        $this->addError($field, "Trường '{$field}' phải có tối thiểu {$ruleParam} ký tự.");
                    }
                    if ($ruleName === 'max' && strlen((string)$value) > (int)$ruleParam) {
                        $this->addError($field, "Trường '{$field}' không được vượt quá {$ruleParam} ký tự.");
                    }
                    if ($ruleName === 'date' && !strtotime($value)) {
                        $this->addError($field, "Trường '{$field}' phải có định dạng ngày hợp lệ.");
                    }
                }
            }
        }
    }

    private function addError(string $field, string $message): void {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }
}
