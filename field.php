<?php
abstract class Field {
    protected string $name;
    protected string $label;
    protected $value;

    public function __construct(string $name, string $label, $value = '') {
        $this->name = $name;
        $this->label = $label;
        $this->value = $value;
    }

    abstract public function render(): string;
}
?>
