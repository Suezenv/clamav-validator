<?php

namespace Suez\ClamAV\Validator\Constraint;

use Suez\ClamAV\AppWrite\ClamAV\NetworkStream;
use Suez\ClamAV\Validator\Constraint\ClamAv;
use Suez\ClamAV\Validator\Constraint\ClamAvUnreachableServiceException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException as ExceptionUnexpectedTypeException;

/**
 * Class ClamAvValidator
 */
class ClamAvValidator extends ConstraintValidator
{
    /**
     * @var NetworkStream
     */
    private $network;

    /**
     * ClamAvValidator constructor.
     * @param NetworkStream $network
     */
    public function __construct(NetworkStream $network)
    {
        $this->network = $network;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $values = is_array($value) ? $value : [$value];

        if (!$constraint instanceof ClamAv) {
            throw new ExceptionUnexpectedTypeException($constraint, ClamAv::class);
        }

        if (empty($values)) {
            return;
        }

        if (!$this->network->ping()) {
            throw new ClamAvUnreachableServiceException("ClamAV service is not reachable");
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