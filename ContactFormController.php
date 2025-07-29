<?php
namespace App\Controller;

use App\Model\ContactForm;
use Exception;

class ContactFormController {
    private ContactForm $contactFormModel;

    public function __construct() {
        $this->contactFormModel = new ContactForm();
    }

    public function submitContactForm(array $data): array {
        try {
            // Sanitize and validate the input data
            $name = htmlspecialchars($data['name']);
            $email = htmlspecialchars($data['email']);
            $phone = htmlspecialchars($data['phone']);
            $companyEmail = htmlspecialchars($data['company-email']);
            $question = htmlspecialchars($data['question']);

            // Insert data into the database via the model
            $result = $this->contactFormModel->submitContactForm($name, $email, $phone, $companyEmail, $question);

            if ($result) {
                return [
                    'status' => 'success',
                    'code' => 201,
                    'message' => 'Contact form submitted successfully.'
                ];
            } else {
                throw new Exception('Failed to submit contact form.');
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ];
        }
    }
}
?>
