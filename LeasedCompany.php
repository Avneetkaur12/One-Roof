<?php
namespace App\Model;

use App\Database\ConnectionFactory;
use Exception;
use PDO;

class LeasedCompany
{
    private PDO $db;

    /* Constructor that initializes the database connection */
    public function __construct()
    {
        $this->db = ConnectionFactory::getConnection();
        $this->createTableIfNotExists();
    }

    /* Create the leased_companies table if it does not exist */
    private function createTableIfNotExists(): void
    {
        $sql = '
            CREATE TABLE IF NOT EXISTS leased_companies (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            );
        ';
        $this->db->exec($sql);
    }

    /* Insert a new leased company */
    public function createLeasedCompany(string $name, string $email, string $password_hash): bool
    {
        $stmt = $this->db->prepare('INSERT INTO leased_companies (name, email, password_hash) VALUES (:name, :email, :password_hash)');
        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password_hash' => $password_hash
        ]);
    }

    /* Retrieve leased company(s) */
    public function getLeasedCompany(?int $id = null): array
    {
        if ($id === null) {
            $stmt = $this->db->prepare('SELECT * FROM leased_companies');
            $stmt->execute();
            $companies = $stmt->fetchAll();
            return $companies;
        } else {
            $stmt = $this->db->prepare('SELECT * FROM leased_companies WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $company = $stmt->fetch();
            return $company ? $company : [];
        }
    }

    /* Update leased company data */
    public function updateLeasedCompany(string $email, string $old_password, string $password_hash): bool
    {
        // Step 1: Fetch the existing password hash for the given email
        $stmt = $this->db->prepare('SELECT password_hash FROM leased_companies WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Step 2: Check if the user exists
        if (!$user) {
            return false; // User with the given email does not exist
        }

        // Step 3: Verify the old password
        if (!password_verify($old_password, $user['password_hash'])) {
            return false; // Old password is incorrect
        }

        // Step 4: Update the password with the new hash
        $stmt = $this->db->prepare('UPDATE leased_companies SET password_hash = :password_hash WHERE email = :email');
        return $stmt->execute([
            ':email' => $email,
            ':password_hash' => $password_hash
        ]);
    }
    public function getLeasedCompanyByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM leased_companies WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null; // Return null if no user found
    }



    /* Delete a leased company */
    public function deleteLeasedCompany(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM leased_companies WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
?>