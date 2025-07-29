<?php
namespace App\Model;

use App\Database\ConnectionFactory;
use Exception;
use PDO;

class Employee {
    private PDO $db;

    /* Constructor that initializes the database connection */
    public function __construct() {
        $this->db = ConnectionFactory::getConnection();
        $this->createTableIfNotExists();
    }

    /* Create the employees table if it does not exist */
    private function createTableIfNotExists(): void {
        $sql = '
            CREATE TABLE IF NOT EXISTS employees (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                role ENUM("admin", "staff") NOT NULL,
                location_id INT,
                FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            );
        ';
        $this->db->exec($sql);
    }

    /* Insert a new employee */
    public function createEmployee(string $name, string $email, string $role, ?int $location_id): bool {
        $stmt = $this->db->prepare('INSERT INTO employees (name, email, role, location_id) VALUES (:name, :email, :role, :location_id)');
        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':role' => $role,
            ':location_id' => $location_id
        ]);
    }

    /* Retrieve employee(s) */
    public function getEmployee(?int $id = null): array {
        if ($id === null) {
            $stmt = $this->db->prepare('SELECT * FROM employees');
            $stmt->execute();
            $employees = $stmt->fetchAll();
            return $employees;
        } else {
            $stmt = $this->db->prepare('SELECT * FROM employees WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $employee = $stmt->fetch();
            return $employee ? $employee : [];
        }
    }

    /* Update employee data */
    public function updateEmployee(int $id, string $name, string $email, string $role, ?int $location_id): bool {
        $stmt = $this->db->prepare('UPDATE employees SET name = :name, email = :email, role = :role, location_id = :location_id WHERE id = :id');
        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':email' => $email,
            ':role' => $role,
            ':location_id' => $location_id
        ]);
    }

    /* Delete an employee */
    public function deleteEmployee(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM employees WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
?>
