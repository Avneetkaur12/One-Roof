<?php
require_once __DIR__ . '/../public/field.php';

class TextInput extends Field {
    public function render(): string {
        return "<input type='text' name='{$this->name}' value='{$this->value}' placeholder='{$this->label}' />";
    }
}

class NumberInput extends Field {
    public function render(): string {
        return "<input type='number' name='{$this->name}' value='{$this->value}'placeholder='{$this->label}' />";
    }
}

class EmailInput extends Field {
    public function render(): string {
        return "<input type='email' name='{$this->name}' value='{$this->value}' placeholder='{$this->label}'/>";
    }
}
?>
