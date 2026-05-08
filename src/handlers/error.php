<?php
require_once __DIR__ . '/../../config/config.php';

class UserInactiveException extends RuntimeException {}

final class ErrorHandler
{
  public function handle(
    Throwable $e
  ): never {
    if ($e instanceof PDOException) {
      require_once __DIR__ . '/../views/error.php';
      exit;
    }

    if ($e instanceof UserInactiveException) {
      $_SESSION = [];
      session_destroy();
    }

    $errorCode = $e->getCode();
    if ($errorCode < 100 || $errorCode >=600) $errorCode = 500;

    $errorMessage = match ($errorCode) {
      400 => "Bad Request",
      401 => "Unauthorized",
      403 => "Forbidden",
      404 => "Not Found",
      default => "Internal Server Error",
    };
    $errorMessage = $e->getMessage();

    $errorDescription = match ($errorCode) {
      400 => "Request is invalid",
      401 => "Invalid credentials",
      403 => "Access denied",
      404 => "Resource does not exist",
      default => "An error has occured in the server",
    };

    http_response_code($errorCode);

    header("Location: " . BASE_URL . "index.php?page=error&code={$errorCode}&message={$errorMessage}&description={$errorDescription}");
    exit;
  }
}