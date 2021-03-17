<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class ClamAv
 * @Annotation
 */
class ClamAv extends Constraint
{
    /**
     * @var string
     */
    public $message = 'L\'analyse antivirus à échouée pour le fichier "{{ file }}".';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return ClamAvValidator::class;
    }
}