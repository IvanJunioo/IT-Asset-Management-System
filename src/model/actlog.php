<?php
declare (strict_types= 1);

final class LogSearchCriteria {
  public function __construct(
    public readonly ?int $empID = null,
    public readonly string $message = "",
    public readonly string $metadata = "",
    public readonly int $limit = 50,
  ) {}
}
