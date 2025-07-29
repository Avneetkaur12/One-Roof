<?php
namespace App\Controller;

use App\Model\LeasedCompany;
use Exception;

class LeasedCompanyController {
    private LeasedCompany $leasedCompanyModel;

    public function __construct() {
        $this->leasedCompanyModel = new LeasedCompany();
    }

    /* Create a new leased company */
    public function createLeasedCompany(string $name, string $email, string $password_hash): array {
        try {
            $result = $this->leasedCompanyModel->createLeasedCompany($name, $email, $password_hash);
            if ($result) {
                return [
                    'status' => 'success',
                    'code' => 201,
                    'message' => 'Leased company created successfully.'
                ];
            } else {
                throw new Exception('Failed to create leased company.');
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    /* Retrieve leased company or all leased companies */
    public function getLeasedCompany(?int $id = null): array {
        try {
            if ($id === null) {
                $companies = $this->leasedCompanyModel->getLeasedCompany();
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $companies
                ];
            } else {
                $company = $this->leasedCompanyModel->getLeasedCompany($id);
                if ($company) {
                    return [
                        'status' => 'success',
                        'code' => 200,
                        'data' => $company
                    ];
                } else {
                    return [
                        'status' => 'error',
                        'code' => 404,
                        'message' => 'Leased company not found.'
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
    public function login(string $email, string $password): array {
        try {
            // Step 1: Retrieve the user by email
            $user = $this->leasedCompanyModel->getLeasedCompanyByEmail($email);
    
            // Step 2: Check if the user exists
            if (!$user) {
                return [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Leased company not found.'
                ];
            }
    
            // Step 3: Verify the password
           
            if (!password_verify($password, $user['password_hash'])) {
                return [
                    'status' => 'error',
                    'code' => 401,
                    'message' => 'Invalid credentials.'
                ];
            }
    
     
            return [
                'status' => 'success',
                'code' => 200,
                'message' => 'Login successful.'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ];
        }
    }
    

    /* Update a leased company */
    public function updateLeasedCompany(string $email, string $old_password, string $password_hash): array {
        try {
            $result = $this->leasedCompanyModel->updateLeasedCompany($email,$old_password,  $password_hash);
            if ($result) {
                return [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Leased company updated successfully.'
                ];
            } else {
                throw new Exception('Failed to update leased company.');
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    /* Delete a leased company */
    public function deleteLeasedCompany(int $id): array {
        try {
            $result = $this->leasedCompanyModel->deleteLeasedCompany($id);
            if ($result) {
                return [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Leased company deleted successfully.'
                ];
            } else {
                throw new Exception('Failed to delete leased company.');
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
