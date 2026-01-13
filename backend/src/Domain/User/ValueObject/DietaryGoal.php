<?php

namespace App\Domain\User\ValueObject;

enum DietaryGoal: string
{
    case WEIGHT_LOSS = 'weight_loss';
    case MAINTAIN = 'maintain';
    case WEIGHT_GAIN = 'weight_gain';
    case MUSCLE_BUILDING = 'muscle_building';
}
