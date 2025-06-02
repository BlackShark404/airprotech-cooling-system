<?php

namespace App\Controllers;

use Config\Database;

class BaseController
{   protected $pdo;

    public function __construct()
    {
        // Get the PDO connection from the Database singleton
        $this->pdo = Database::getInstance()->getConnection();
    }
    
    protected function getViewPath(string $relativePath): string {
        $path = __DIR__ . "/../Views/{$relativePath}.php";
        if (!file_exists($path)) {
            $this->renderError("View not found: {$relativePath}", 404);
        }
        return $path;
    }

    protected function render($view, $data = []) 
    {
        // Start output buffering
        ob_start();
        
        $viewPath = $this->getViewPath($view);
        
        // Extract the data variables
        extract($data);
        
        // Include the view file which will use base.php to structure the page
        include $viewPath;
        
        // Get the complete rendered content
        $content = ob_get_clean();

        echo $content;
    }
    // Output the rendered content

    protected function renderError($message, $statusCode = 500) {
        http_response_code($statusCode);
        $errorView = __DIR__ . "/../Views/error/$statusCode.php";

        if (file_exists($errorView)) {
            // Avoid recursion: don't use getViewPath here
            extract(['message' => $message]);
            ob_start();
            include $errorView;
            $content = ob_get_clean();
            echo $content;
        } else {
            // Fallback plain error message
            echo "<h1>Error: $statusCode</h1><p>$message</p>";
        }

        exit;
    }

    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    protected function loadModel($model) {
        $modelClass = "App\\Models\\$model";

        if (class_exists($modelClass)) {
            return new $modelClass();
        } else {
            $this->renderError("Model class not found: $modelClass", 500);
        }
    }

    // ✅ Check if request is from Axios
    protected function isAjax() {
        // For debugging purposes, accept all requests as AJAX
        // This makes our API endpoints work with fetch() and other modern AJAX methods
        return true;
        
        // Original implementation - only for XMLHttpRequest
        // return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        //     strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    // ✅ Respond with JSON (generic)
    protected function json($data = [], $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // ✅ Respond with JSON success (standardized)
    protected function jsonSuccess($data = [], $message = 'Success', $statusCode = 200) {
        $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    // ✅ Respond with JSON error (standardized)
    protected function jsonError($message = 'An error occurred', $statusCode = 400, $data = []) {
        $this->json([
            'success' => false,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    // ✅ Parse JSON from request body
    protected function getJsonInput(): array {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    // ✅ Input helpers
    protected function request($key = null, $default = null) {
        $request = array_merge($_GET, $_POST);
        if ($key) {
            return $request[$key] ?? $default;
        }
        return $request;
    }

    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Check if user has the specified permission
     * 
     * @param string $permission Permission to check
     * @return bool True if user has permission, false otherwise
     */
    protected function checkPermission(string $permission): bool {
        // Get user role from session
        $role = $_SESSION['user_role'] ?? '';
        
        // Basic permission check based on role
        if ($permission === 'admin' && $role === 'admin') {
            return true;
        }
        
        // Add more complex permission logic here as needed
        
        return false;
    }
}
