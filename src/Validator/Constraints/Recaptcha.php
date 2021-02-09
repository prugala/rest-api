<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class Recaptcha extends Constraint
{
    public string $message = 'Code "{{ code }}" is not a valid Recaptcha.';
}
