<?php

namespace Suez\ClamAV\Tests\Validator\Constraint;

use PHPUnit\Framework\TestCase;
use Suez\ClamAV\AppWrite\ClamAV\NetworkStream;
use Suez\ClamAV\Validator\Constraint\ClamAv;
use Suez\ClamAV\Validator\Constraint\ClamAvValidator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

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
        $clamAvvalidator = new ClamAvValidator(new NetworkStream('clamav-service'));
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
        
        /** @var ExecutionContextInterface $context */
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
            ['/usr/share/clamav-testfiles/clam-aspack.exe', 'eicar.com.txt', true],
            ['/usr/share/clamav-testfiles/clam-aspack.exe', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam-fsg.exe', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam-mew.exe', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam-nsis.exe', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam-pespin.exe', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam-petite.exe', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam-upack.exe', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam-upx.exe', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam-v2.rar', 'name.xx', false],
            ['/usr/share/clamav-testfiles/clam-v3.rar', 'name.xx', false],
            ['/usr/share/clamav-testfiles/clam-wwpack.exe', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam-yc.exe', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam.7z', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam.arj', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam.bin-be.cpio', 'name.xxx', true],
            ['/usr/share/clamav-testfiles/clam.bin-le.cpio', 'name.xxx', true],
            ['/usr/share/clamav-testfiles/clam.bz2.zip', 'name.xxx', true],
            ['/usr/share/clamav-testfiles/clam.cab', 'name.xxx', true],
            ['/usr/share/clamav-testfiles/clam.chm', 'name.xxx', true],
            ['/usr/share/clamav-testfiles/clam.d64.zip', 'name.xxx', true],
            ['/usr/share/clamav-testfiles/clam.ea05.exe', 'name.xxx', true],
            ['/usr/share/clamav-testfiles/clam.ea06.exe', 'name.xxx', true],
            ['/usr/share/clamav-testfiles/clam.exe', 'name.xxx', true],
            ['/usr/share/clamav-testfiles/clam.exe.binhex', 'name.xxx', true],
            ['/usr/share/clamav-testfiles/clam.exe.bz2', 'name.xxx', true],
            ['/usr/share/clamav-testfiles/clam.exe.html', 'name.xxx', true],
            ['/usr/share/clamav-testfiles/clam.exe.mbox.base64', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam.exe.mbox.uu', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam.exe.rtf', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam.exe.szdd', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam.impl.zip', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam.mail', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam.newc.cpio', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam.odc.cpio', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam.ole.doc', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam.pdf', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam.ppt', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam.sis', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam.tar.gz', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam.tnef', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam.zip', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam_IScab_ext.exe', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam_IScab_int.exe', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam_ISmsi_ext.exe', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam_ISmsi_int.exe', 'name.xx', true],
            ['/usr/share/clamav-testfiles/clam_cache_emax.tgz', 'name.xx', true],
        ];
    }
}

