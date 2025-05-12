<?php

namespace App\Payments\Enums;

enum BillingTypeEnum: string
{
  case CREDIT_CARD = "credit_card";
  case DEBIT_CARD = "debit_card";
  case PIX = "pix";
}
