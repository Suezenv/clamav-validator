<?php

namespace Tests\ClamAV\Validator\Constraints;

use AppBundle\Validator\Constraints\ClamAv;
use AppBundle\Validator\Constraints\ClamAvValidator;
use Appwrite\ClamAV\Network;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Tests\SecurityAudit\UploadedFiles\InfectedFilesTest;

/**
 * Class ClamAvValidatorTest
 * @group security
 * @group clamAv
 */
class ClamAvValidatorTest extends TestCase
{

    /**
     * @param bool $expectedViolation
     * @return ClamAvValidator
     * @throws \AppBundle\Security\ClamAv\ClamAvServiceNotStartedException
     */
    public function getValidator($expectedViolation = true)
    {
        $clamAvvalidator = new ClamAvValidator(new Network());
        $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();

        $violation = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();
        $violation->expects($this->any())->method('setParameter')->willReturn($violation);

        if ($expectedViolation) {
            $violation->expects($this->once())->method('addViolation')->willReturn($violation);
            $context
                ->expects($this->once())
                ->method('buildViolation')->willReturn($violation);
        } else {
            $context
                ->expects($this->never())
                ->method('buildViolation');
        }

        $clamAvvalidator->initialize($context);

        return $clamAvvalidator;
    }
    /**
     * @group security
     * @group audit
     * @dataProvider clamAvUseCase
     */
    public function testClamAvValidate($filePath, $fileName, $expectedViolation)
    {
        $file = new UploadedFile($filePath, $fileName, $expectedViolation);
        $validatorMock = $this->getValidator($expectedViolation);
        $constraint = new ClamAv();
        $validatorMock->validate($file, $constraint);
    }

    public function clamAvUseCase()
    {
        return [
            [InfectedFilesTest::FILE_INFECTED, 'eicar.com.txt', true],
            [InfectedFilesTest::FILE_CLEAN, 'text.txt',  false],
        ];
    }
}

