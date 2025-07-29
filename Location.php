<?php
namespace App\Model;

use App\Database\ConnectionFactory;
use Exception;
use PDO;

class Location
{
    private PDO $db;

    /* Constructor that initializes the database connection and creates the locations table if it doesn't exist. */
    public function __construct()
    {
        $this->db = ConnectionFactory::getConnection();
        $this->createTableIfNotExists();
    }

    private function createTableIfNotExists(): void
    {
        $sql = '
           CREATE TABLE IF NOT EXISTS locations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            address VARCHAR(255) NOT NULL,
            capacity INT NOT NULL,
            amenities JSON NOT NULL,
            image_url VARCHAR(2083) NOT NULL,
            type_of_service ENUM("coworking_space", "office_space", "meeting_room") NOT NULL,
            state VARCHAR(100) NOT NULL,
            rent DECIMAL(10, 2) NOT NULL,
            is_available BOOLEAN NOT NULL DEFAULT TRUE,
            space_available INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );
 ';
        $this->db->exec($sql);
    }

    // Function to insert a location into the database table
    public function createLocation(
        string $name,
        string $address,
        int $capacity,
        array $amenities,
        string $image_url,
        string $type_of_service,
        string $state,
        float $rent,
        bool $is_available,
        int $space_available
    ): bool {
        $stmt = $this->db->prepare('
            INSERT INTO locations (name, address, capacity, amenities, image_url, type_of_service, state, rent, is_available, space_available) 
            VALUES (:name, :address, :capacity, :amenities, :image_url, :type_of_service, :state, :rent, :is_available, :space_available)
        ');
        return $stmt->execute([
            ':name' => $name,
            ':address' => $address,
            ':capacity' => $capacity,
            ':amenities' => json_encode($amenities),
            ':image_url' => $image_url,
            ':type_of_service' => $type_of_service,
            ':state' => $state,
            ':rent' => $rent,
            ':is_available' => $is_available,
            ':space_available' => $space_available
        ]);
    }


    // Function to retrieve the locations
    public function getLocation(?int $id = null, ?string $typeOfService = null, ?string $state = null, ?float $minRent = null, ?float $maxRent = null): array
    {
        if ($id !== null) {
            // Search by ID
            $stmt = $this->db->prepare('SELECT * FROM locations WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $location = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($location === false) {
                throw new Exception('Location not found', 404);
            }

            return $location;
        } else {
            // Build dynamic query based on optional filters
            $query = 'SELECT * FROM locations WHERE 1=1';
            $params = [];

            if ($typeOfService !== null) {
                $query .= ' AND type_of_service = :typeOfService';
                $params[':typeOfService'] = $typeOfService;
            }

            if ($state !== null) {
                $query .= ' AND state = :state';
                $params[':state'] = $state;
            }

            if ($minRent !== null) {
                $query .= ' AND rent >= :minRent';
                $params[':minRent'] = $minRent;
            }

            if ($maxRent !== null) {
                $query .= ' AND rent <= :maxRent';
                $params[':maxRent'] = $maxRent;
            }

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);

            $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($locations)) {
                return [];
            }

            return $locations;
        }
    }


    // Function to update the locations
    public function updateLocation(
        int $id,
        string $name,
        string $address,
        int $capacity,
        array $amenities,
        string $image_url,
        string $type_of_service,
        string $state,
        float $rent,
        bool $is_available,
        int $space_available
    ): bool {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM locations WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            throw new Exception('Location not found', 404);
        }

        $stmt = $this->db->prepare('
            UPDATE locations 
            SET 
                name = :name, 
                address = :address, 
                capacity = :capacity, 
                amenities = :amenities, 
                image_url = :image_url, 
                type_of_service = :type_of_service, 
                state = :state, 
                rent = :rent,
                is_available = :is_available,
                space_available = :space_available
            WHERE id = :id
        ');
        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':address' => $address,
            ':capacity' => $capacity,
            ':amenities' => json_encode($amenities),
            ':image_url' => $image_url,
            ':type_of_service' => $type_of_service,
            ':state' => $state,
            ':rent' => $rent,
            ':is_available' => $is_available,
            ':space_available' => $space_available
        ]);
    }

    // Function to delete locations
    public function deleteLocation(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM locations WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            throw new Exception('Location not found', 404);
        }

        $stmt = $this->db->prepare('DELETE FROM locations WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    // Function to delete locations
    public function getAllLocations(): array
    {
        $stmt = $this->db->prepare('SELECT * FROM locations');
        $stmt->execute();

        $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($locations)) {
            return []; // Return empty array if no locations exist
        }

        return $locations;
    }
}