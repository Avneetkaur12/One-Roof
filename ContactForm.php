<?php
namespace App\Model;

use PDO;
use Exception;

class ContactForm {
    private PDO $db;

    public function __construct() {
        // Assuming the ConnectionFactory is set up to provide a PDO connection
        $this->db = \App\Database\ConnectionFactory::getConnection();
    }

    /* Insert the contact form data into the database */
    public function submitContactForm(string $name, string $email, string $phone, string $companyEmail, string $question): bool {
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO contact_form (name, email, phone, company_email, question) 
                 VALUES (:name, :email, :phone, :company_email, :question)'
            );
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':company_email' => $companyEmail,
                ':question' => $question,
            ]);
            return true;
        } catch (Exception $e) {
            // Log the error and handle it gracefully
            error_log('Error submitting contact form: ' . $e->getMessage());
            return false;
        }
    }
}
?>
