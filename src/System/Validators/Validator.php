<?php

namespace Src\System\Validators;

class Validator
{
    /**
     * @var array $patterns
     */
    public $patterns = array(
        'int' => '[0-9]+',
        'float' => '[0-9\.,]+',
        'text' => '[\p{L}0-9\s-.,;:!"%&()?+\'°#\/@]+',
    );

    public $errors = array();

    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    public function value($value)
    {
        $this->value = $value;
        return $this;
    }

    public function pattern($name)
    {
        $regex = '/^(' . $this->patterns[$name] . ')$/u';
        if ($this->value != '' && !preg_match($regex, $this->value)) {
            $this->errors[] = 'The ' . $this->name . ' field must be ' . $name . '.';
        }
        return $this;
    }

    public function required()
    {
        if (($this->value == '' || $this->value == null)) {
            $this->errors[] = 'The ' . $this->name . ' field is required.';
        }
        return $this;
    }

    public function max($length)
    {
        if (is_string($this->value)) {
            if (strlen($this->value) > $length) {
                $this->errors[] = 'The ' . $this->name . ' must be at least ' . $length . ' characters.';
            }
        } else {
            if ($this->value > $length) {
                $this->errors[] = 'The ' . $this->name . ' must be at least ' . $length . '.';
            }
        }
        return $this;
    }

    public function paymentType()
    {
        if (!in_array($this->value, ['virtual_account', 'credit_card'])) {
            $this->errors[] = 'The ' . $this->name . ' must be virtual_account or credit_card';
        }
        return $this;
    }

    public function unique(array $list)
    {
        if (in_array($this->value, $list)) {
            $this->errors[] = 'The ' . $this->name . ' has already been taken.';
        }
        return $this;
    }

    public function validate()
    {
        if (!empty($this->errors)) {
            return false;
        }
        return true;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
