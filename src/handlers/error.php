<?php
require_once __DIR__ . '/../../config/config.php';

class UserInactiveException extends RuntimeException {}

final class ErrorHandler
{
  public static function handle(
    Throwable $e
  ): never {

  if ($e instanceof UserInactiveException) {
    session_destroy();
    header("Location: " . BASE_URL . "index.php?page=login&error=user_deactivated");
    exit;
  }

    $errorCode = $e->getCode();

    if ($errorCode < 100 || $errorCode >=600) $errorCode = 500;

    $errorMessage = match ($errorCode) {
      400 => "Bad Request",
      403 => "Forbidden",
      404 => "Not Found",
      default => "Internal Server Error",
    };

    http_response_code($errorCode);
    $errorDescription = $e->getMessage();

    include __DIR__ . '/../views/error-page.php';
    exit;
  }
}