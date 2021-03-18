<?php

namespace Suez\ClamAV\Validator\Constraint;

use AppBundle\Security\ClamAv\ClamAvServiceNotStartedException;
use Appwrite\ClamAV\Network;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ClamAvValidator
 */
class ClamAvValidator extends ConstraintValidator
{
    /**
     * @var Network
     */
    private $network;

    /**
     * ClamAvValidator constructor.
     * @param Network $network
     */
    public function __construct(Network $network)
    {
        $this->network = $network;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     * @throws ClamAvServiceNotStartedException
     */
    public function validate($value, Constraint $constraint)
    {
        $values = is_array($value) ? $value : [$value];

        if (!$constraint instanceof ClamAv) {
            throw new UnexpectedTypeException($constraint, ClamAv::class);
        }

        if (empty($values) || '' === $values) {
            return;
        }

        if (!$this->network->ping()) {
            throw new ClamAvServiceNotStartedException("Le service ClamAV est indisponible");
        }

        foreach($values as $fileUpload) {
            if (!$this->fileScan($fileUpload, $constraint)) {
                break;
            }
        }
    }

    /**
     * @param $fileUpload
     * @param $constraint
     * @return bool
     */
    private function fileScan($fileUpload, $constraint)
    {
        $success = true;

        if ($fileUpload instanceof UploadedFile && is_file($fileUpload->getRealPath()) /*&& $fileUpload->isValid()*/) {

            $initPerm = (substr(decoct(fileperms($fileUpload->getRealPath())), -4));
            #Workaroudnd for ""Access denied. ERROR""
            #Ensure file is readable by clamAV
            chmod($fileUpload->getRealPath(), 0664);

            if (false === $this->network->fileScan($fileUpload->getRealPath())) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ file }}', $fileUpload->getClientOriginalName())
                    ->addViolation();

                $success = false;
            }
            #Restore previous permission file
            chmod($fileUpload->getRealPath(), $initPerm);

        }

        return $success;
    }
}