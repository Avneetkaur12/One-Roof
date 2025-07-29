<?php
namespace App\Controller;

use App\Model\Location;
use Exception;

class LocationController
{
    private Location $locationModel;

    public function __construct()
    {
        $this->locationModel = new Location();
    }

    // Function to create a new location using the model
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
    ): array {
        try {
            $result = $this->locationModel->createLocation(
                $name,
                $address,
                $capacity,
                $amenities,
                $image_url,
                $type_of_service,
                $state,
                $rent,
                $is_available,
                $space_available
            );
            if ($result) {
                return [
                    'status' => 'success',
                    'code' => 201,
                    'message' => 'Location created successfully.'
                ];
            } else {
                throw new Exception('Failed to create location.');
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ];
        }
    }


    // Function to retrieve a location or all locations
    public function getLocation(?int $id = null, ?string $typeOfService = null, ?string $state = null, ?float $minRent = null, ?float $maxRent = null): array
    {
        try {
            if ($id !== null) {
                $location = $this->locationModel->getLocation($id);
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $location
                ];
            } else {
                $locations = $this->locationModel->getLocation(null, $typeOfService, $state, $minRent, $maxRent);
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $locations
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ];
        }
    }


    // Function to update a location
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
    ): array {
        try {
            $result = $this->locationModel->updateLocation(
                $id,
                $name,
                $address,
                $capacity,
                $amenities,
                $image_url,
                $type_of_service,
                $state,
                $rent,
                $is_available,
                $space_available
            );
            if ($result) {
                return [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Location updated successfully.'
                ];
            } else {
                throw new Exception('Failed to update location.');
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    // Function to delete a location
    public function deleteLocation(int $id): array
    {
        try {
            $result = $this->locationModel->deleteLocation($id);
            if ($result) {
                return [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Location deleted successfully.'
                ];
            } else {
                throw new Exception('Failed to delete location.');
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
