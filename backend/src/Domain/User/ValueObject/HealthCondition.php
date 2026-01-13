<?php

namespace App\Domain\User\ValueObject;

enum HealthCondition: string
{
    case CELIAC = 'celiac';
    case LACTOSE_INTOLERANCE = 'lactose_intolerance';
    case IBS = 'ibs';
    case REFLUX = 'reflux';
    case DIABETES = 'diabetes';
    case HYPERTENSION = 'hypertension';
}
