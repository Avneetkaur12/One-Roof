<?php
namespace App\Model;

use App\Database\ConnectionFactory;
use Exception;
use PDO;

class CompanyLocations {
    private PDO $db;

    /* Constructor that initializes the database connection */
    public function __construct() {
        $this->db = ConnectionFactory::getConnection();
        $this->createTableIfNotExists();
    }

    /* Create the company_locations table if it does not exist */
    private function createTableIfNotExists(): void {
        $sql = '
           CREATE TABLE IF NOT EXISTS company_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    location_id INT NOT NULL,
    contract_start DATE NOT NULL,
    contract_end DATE NOT NULL,
    payment_terms JSON NOT NULL,
    space INT NOT NULL DEFAULT 0, -- New column to track space (default to 0)
    FOREIGN KEY (company_id) REFERENCES leased_companies(id) ON DELETE CASCADE,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
        ';
        $this->db->exec($sql);
    }

    /* Insert a new company-location relationship */
    public function createCompanyLocation(int $company_id, int $location_id, string $contract_start, string $contract_end, array $payment_terms,int $space): bool {
        $stmt = $this->db->prepare('
            INSERT INTO company_locations (company_id, location_id, contract_start, contract_end, payment_terms,space)
            VALUES (:company_id, :location_id, :contract_start, :contract_end, :payment_terms,:space)
        ');
        return $stmt->execute([
            ':company_id' => $company_id,
            ':location_id' => $location_id,
            ':contract_start' => $contract_start,
            ':contract_end' => $contract_end,
            ':space'=>$space,
            ':payment_terms' => json_encode($payment_terms)
        ]);
    }

    /* Retrieve company-location relationships */
    public function getCompanyLocations(?int $id = null): array {
        if ($id === null) {
            $stmt = $this->db->prepare('SELECT * FROM company_locations');
            $stmt->execute();
            return $stmt->fetchAll();
        } else {
            $stmt = $this->db->prepare('SELECT * FROM company_locations WHERE company_id = :id');
            $stmt->execute([':id' => $id]);
            return $stmt->fetch() ?: [];
        }
    }

    public function updateLocationSpace(int $id, int $space): bool {
        $stmt = $this->db->prepare('UPDATE company_locations SET space = :space WHERE company_id = :id');
        return $stmt->execute([':space' => $space, ':id' => $id]);
    }
    
    
}
?>
