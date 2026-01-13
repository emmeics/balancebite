<?php

namespace App\Domain\User\ValueObject;

enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case OTHER = 'other';
}
