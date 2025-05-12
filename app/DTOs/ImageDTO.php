<?php

namespace App\DTOs;

use Illuminate\Http\UploadedFile;

readonly class ImageDTO
{
  public function __construct(
    public ?UploadedFile $file,
    public ?string $description,
    public ?string $alt,
    public bool $featured
  ) {}

  public static function make(?UploadedFile $file, array $data): self
  {
    return new self(
      file: $file,
      description: $data['description'] ?? null,
      alt: $data['alt'] ?? null,
      featured: $data['featured'] ?? false,
    );
  }
}
