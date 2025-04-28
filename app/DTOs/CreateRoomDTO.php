<?php

namespace App\DTOs;

use App\Http\Requests\CreateRoomRequest;

readonly class CreateRoomDTO
{
  public function __construct(
    public string $name,
    public string $slug,
    public ?string $description,
    public bool $featured,
    public string $size,
    public int $max_adults,
    public int $max_children,
    public int $double_beds,
    public int $single_beds,
    public string $floor,
    public string $type,
    public string $number,
    public array $images,
    public array $tariffs,
    public array $availabilities,
  ) {}

  public static function makeFromRequest(CreateRoomRequest $request): self
  {
    $data = $request->validated();

    $images = collect($data['images'] ?? [])
      ->map(function ($image, $index) use ($request) {
        return [
          'file'        => $request->file("images.$index.file"),
          'description' => $image['description'] ?? null,
          'alt'         => $image['alt'] ?? null,
          'featured'    => $image['featured'] ?? false,
        ];
      })
      ->toArray();

    return new self(
      name: $data['name'],
      slug: $data['slug'],
      description: $data['description'] ?? null,
      featured: $data['featured'],
      size: $data['size'],
      max_adults: $data['max_adults'],
      max_children: $data['max_children'],
      double_beds: $data['double_beds'],
      single_beds: $data['single_beds'],
      floor: $data['floor'],
      type: $data['type'],
      number: $data['number'],
      images: $images,
      tariffs: $data['tariffs'] ?? [],
      availabilities: $data['availabilities'] ?? [],
    );
  }

  public function toArray(): array
  {
    return [
      'name'           => $this->name,
      'slug'           => $this->slug,
      'description'    => $this->description,
      'featured'       => $this->featured,
      'size'           => $this->size,
      'max_adults'     => $this->max_adults,
      'max_children'   => $this->max_children,
      'double_beds'    => $this->double_beds,
      'single_beds'    => $this->single_beds,
      'floor'          => $this->floor,
      'type'           => $this->type,
      'number'         => $this->number,
    ];
  }
}
