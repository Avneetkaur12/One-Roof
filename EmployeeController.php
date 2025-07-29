<?php
namespace App\Controller;

use App\Model\Employee;
use Exception;

class EmployeeController {
    private Employee $employeeModel;

    public function __construct() {
        $this->employeeModel = new Employee();
    }

    /* Create a new employee */
    public function createEmployee(string $name, string $email, string $role, ?int $location_id): array {
        try {
            $result = $this->employeeModel->createEmployee($name, $email, $role, $location_id);
            if ($result) {
                return [
                    'status' => 'success',
                    'code' => 201,
                    'message' => 'Employee created successfully.'
                ];
            } else {
                throw new Exception('Failed to create employee.');
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    /* Retrieve employee or all employees */
    public function getEmployee(?int $id = null): array {
        try {
            if ($id === null) {
                $employees = $this->employeeModel->getEmployee();
                return [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $employees
                ];
            } else {
                $employee = $this->employeeModel->getEmployee($id);
                if ($employee) {
                    return [
                        'status' => 'success',
                        'code' => 200,
                        'data' => $employee
                    ];
                } else {
                    return [
                        'status' => 'error',
                        'code' => 404,
                        'message' => 'Employee not found.'
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

    /* Update an employee */
    public function updateEmployee(int $id, string $name, string $email, string $role, ?int $location_id): array {
        try {
            $result = $this->employeeModel->updateEmployee($id, $name, $email, $role, $location_id);
            if ($result) {
                return [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Employee updated successfully.'
                ];
            } else {
                throw new Exception('Failed to update employee.');
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    /* Delete an employee */
    public function deleteEmployee(int $id): array {
        try {
            $result = $this->employeeModel->deleteEmployee($id);
            if ($result) {
                return [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Employee deleted successfully.'
                ];
            } else {
                throw new Exception('Failed to delete employee.');
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
