<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @Annotation
 */
final class RecaptchaValidator extends ConstraintValidator
{
    public function __construct(private string $secret) {}

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Recaptcha) {
            throw new UnexpectedTypeException($constraint, Recaptcha::class);
        }

        if (null === $value || '' === $value) {
            return;
        }
return;
        $recaptcha = new \ReCaptcha\ReCaptcha($this->secret);

        if (!$recaptcha->verify($value)->isSuccess()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ code }}', $value)
                ->addViolation();
        }
    }
}
