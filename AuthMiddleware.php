<?php

namespace App\Middleware;

use App\Database\ConnectionFactory;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PDO;

class AuthMiddleware
{
    private string $secretKey = "AvneetKaurProject"; // The secret key used to sign tokens

    // Authenticate the token and check user existence and role
    public function authenticate(string $requiredRole): bool
    {
        // Step 1: Get the token from the Authorization header
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            $token = str_replace('Bearer ', '', $authHeader);

            try {
                // Step 2: Decode the JWT token
                $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));

                // Step 3: Extract email and role from the decoded token
                $userEmail = $decoded->email ?? null;
                $userRole = $decoded->role ?? null;

                if ($userEmail && $userRole && $userRole === $requiredRole) {
                    // Step 4: Check if the user exists in the database
                    if ($requiredRole === "ADMIN" || $this->checkUserExists($userEmail)) {
                        return true; // Authentication successful
                    } else {
                        $this->sendErrorResponse(401, 'User not found.');
                        return false;
                    }
                } else {
                    $this->sendErrorResponse(403, 'Invalid role.');
                    return false;
                }
            } catch (\Exception $e) {
                // Token is invalid or expired
                $this->sendErrorResponse(401, 'Invalid or expired token.');
                return false;
            }
        } else {
            // Authorization header is missing
            $this->sendErrorResponse(400, 'Authorization header missing.');
            return false;
        }
    }

    // Generate a JWT token for the user
    public function generateToken(string $email, string $role): string
    {
        $payload = [
            'email' => $email,
            'role' => $role,
            'iat' => time(), // Issued at
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    // Check if the user exists in the database
    private function checkUserExists(string $userEmail): bool
    {
        $db = ConnectionFactory::getConnection();
        $stmt = $db->prepare('SELECT * FROM leased_companies WHERE email = :email');
        $stmt->execute([':email' => $userEmail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user !== false;
    }
    public function getUserDetailsByToken(): ?array
    {
        // Step 1: Get the token from the Authorization header
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            $token = str_replace('Bearer ', '', $authHeader);

            try {
                // Step 2: Decode the JWT token
                $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));

                // Step 3: Extract email from the decoded token
                $userEmail = $decoded->email ?? null;

                if ($userEmail) {
                    // Step 4: Check if the user exists in the database and fetch user details
                    $userDetails = $this->getUserByEmail($userEmail);

                    if ($userDetails) {
                        return $userDetails; // Return user details as an array
                    } else {
                        $this->sendErrorResponse(404, 'User not found.');
                        return ''; // User not found
                    }
                } else {
                    $this->sendErrorResponse(401, 'Invalid token: No email found.');
                    return ''; // Invalid token
                }
            } catch (\Exception $e) {
                // Token is invalid or expired
                $this->sendErrorResponse(401, 'Invalid or expired token.');
                return ''; // Invalid or expired token
            }
        } else {
            // Authorization header is missing
            $this->sendErrorResponse(400, 'Authorization header missing.');
            return ''; // Missing authorization header
        }
    }

    private function getUserByEmail(string $email): ?array
    {
        // Assuming you have a database connection object `$db`
        $db = ConnectionFactory::getConnection();        
        $stmt = $db->prepare("SELECT id, name, email, created_at FROM leased_companies WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null; // Return null if no user found
    }



    // Send error response
    private function sendErrorResponse(int $statusCode, string $message): void
    {
        http_response_code($statusCode);
        echo json_encode([
            'status' => 'error',
            'code' => $statusCode,
            'message' => $message
        ]);
    }
}
