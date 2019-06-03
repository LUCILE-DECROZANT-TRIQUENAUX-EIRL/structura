<?php
namespace Tests\AppBundle\Util;

use AppBundle\Entity\Responsability;
use PHPUnit\Framework\TestCase;

class ResponsabilityTest extends TestCase
{

    public function testId()
    {
        $resp = new Responsability();
        $id = $resp->getId();
        $this->assertEquals(-1,$id);
    }

    public function testLabel()
    {
        $resp = new Responsability();
        $resp = $resp->setLabel('Administrateurice');
        $label = $resp->getLabel();
        $this->assertContains('Administrateurice',$label);
    }

    public function testCode()
    {
        $resp = new Responsability();
        $resp = $resp->setCode('ROLE_ADMIN');
        $code = $resp->getCode();
        $this->assertContains('ROLE',$code);
    }

    public function testDescription()
    {
        $resp = new Responsability();
        $resp = $resp->setCode('Administre la base de données');
        $desc = $resp->getCode();
        $this->assertContains('Administre',$desc);
        $this->assertContains('données',$desc);
    }
}
?>
