<?php

use App\Payments\Processors\Cielo\Cielo;

return [
  'current_processor' => Cielo::class,

  'processors' => [
    'cielo' => Cielo::class,

  ],
];
