<?php
require_once __DIR__ . '/../public/concrete.php';

class Form {
    private array $fields = [];

    public function __construct(array $fieldConfigs) {
        foreach ($fieldConfigs as $config) {
            $type = $config['type'];
            $name = $config['name'];
            $label = $config['label'];
            $value = $config['value'] ?? '';

            switch ($type) {
                case 'text':
                    $this->fields[] = new TextInput($name, $label, $value);
                    break;
                case 'number':
                    $this->fields[] = new NumberInput($name, $label, $value);
                    break;
                case 'email':
                    $this->fields[] = new EmailInput($name, $label, $value);
                    break;
            }
        }
    }

    public function render(): string {
        $html = '';
        foreach ($this->fields as $field) {
            $html .= $field->render() . '<br>';
        }
        return $html;
    }
}
?>
