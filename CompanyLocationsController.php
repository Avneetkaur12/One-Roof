<?php
namespace App\Controller;

use App\Model\CompanyLocations;
use Exception;

class CompanyLocationsController {
    private CompanyLocations $companyLocationsModel;

    /* Constructor to initialize the CompanyLocations model */
    public function __construct() {
        $this->companyLocationsModel = new CompanyLocations();
    }

    /* Create a new company-location relationship */
    public function createCompanyLocation(int $company_id, int $location_id, string $contract_start, string $contract_end, array $payment_terms,$space): array {
        try {
            $result = $this->companyLocationsModel->createCompanyLocation($company_id, $location_id, $contract_start, $contract_end, $payment_terms,$space);
            if ($result) {
                return [
                    'status' => 'success',
                    'code' => 201,
                    'message' => 'Company-location relationship created successfully.'
                ];
            } else {
                throw new Exception('Failed to create company-location relationship.');
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    /* Retrieve company-location relationships */
    public function getCompanyLocations(?int $id = null): array {
        try {
            if ($id === null) {
                $companyLocations = $this->companyLocationsModel->getCompanyLocations();
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $companyLocations
                ];
            } else {
                $companyLocation = $this->companyLocationsModel->getCompanyLocations($id);
                if ($companyLocation) {
                    return [
                        'status' => 'success',
                        'code' => 200,
                        'data' => $companyLocation
                    ];
                } else {
                    return [
                        'status' => 'error',
                        'code' => 404,
                        'message' => 'Company-location relationship not found.'
                    ];
                }
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ];
        }
    }
    public function updateCompanyLocationSpace(int $id, int $additionalSpace): array {
        try {
            $existingLocation = $this->companyLocationsModel->getCompanyLocations($id);
            if (!$existingLocation) {
                return [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Company-location relationship not found.'
                ];
            }
    
            // Update the space column by adding the new value
            $updatedSpace = $existingLocation['space'] + $additionalSpace;
            $result = $this->companyLocationsModel->updateLocationSpace($id, $updatedSpace);
    
            if ($result) {
                return [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Space updated successfully.',
                    'data' => ['id' => $id, 'updated_space' => $updatedSpace]
                ];
            } else {
                return [
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Failed to update space.'
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
    
}
?>
