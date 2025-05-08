<?php

namespace App\DTOs;

use App\Http\Requests\UpdateRoomRequest;
use Illuminate\Http\UploadedFile;

readonly class UpdateRoomDTO
{

  public function __construct(
    public ?string $name,
    public ?string $slug,
    public ?string $description,
    public ?bool $featured,
    public ?string $size,
    public ?int $max_adults,
    public ?int $max_children,
    public ?int $double_beds,
    public ?int $single_beds,
    public ?string $floor,
    public ?string $type,
    public ?string $number,
    public ?array $images,
    public ?array $tariffs,
    public ?array $availabilities,
  ) {}

  public static function makeFromRequest(UpdateRoomRequest $request): self
  {
    $data = $request->validated();

    $images = collect($data['images'] ?? [])
      ->map(function ($image, $index) use ($request) {
        return ImageDTO::make(
          $request->file("images.$index.file"),
          $image
        );
      })
      ->toArray();

    $tariffs = collect($data['tariffs'] ?? [])
      ->map(fn(array $tariff) => TariffDTO::make($tariff))
      ->toArray();

    $availabilities = collect($data['availabilities'] ?? [])
      ->map(fn(array $availability) => AvailabilityDTO::make($availability))
      ->toArray();

    return new self(
      name: $data['name'] ?? null,
      slug: $data['slug'] ?? null,
      description: $data['description'] ?? null,
      featured: $data['featured'] ?? null,
      size: $data['size'] ?? null,
      max_adults: $data['max_adults'] ?? null,
      max_children: $data['max_children'] ?? null,
      double_beds: $data['double_beds'] ?? null,
      single_beds: $data['single_beds'] ?? null,
      floor: $data['floor'] ?? null,
      type: $data['type'] ?? null,
      number: $data['number'] ?? null,
      images: $images ?: null,
      tariffs: $tariffs ?: null,
      availabilities: $availabilities ?: null,
    );
  }
}
