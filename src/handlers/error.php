<?php
require_once __DIR__ . '/../../config/config.php';

final class ErrorHandler
{
  public static function handle(
    int $code,
    string $message,
    string $description = "",
  ): never {

    http_response_code($code);

    $errorCode = $code;
    $errorMessage = $message;
    $errorDescription = $description;

    include __DIR__ . '/../views/error-page.php';
    exit;
  }
}