<?php
namespace Tests\App\Entity;

use App\Entity\Responsibility;
use PHPUnit\Framework\TestCase;

class ResponsibilityTest extends TestCase
{

    public function testId()
    {
        $resp = new Responsibility();
        $id = $resp->getId();
        $this->assertEquals(-1,$id);
    }

    public function testLabel()
    {
        $resp = new Responsibility();
        $resp = $resp->setLabel('Administrateurice');
        $label = $resp->getLabel();
        $this->assertContains('Administrateurice',$label);
    }

    public function testCode()
    {
        $resp = new Responsibility();
        $resp = $resp->setCode('ROLE_ADMIN');
        $code = $resp->getCode();
        $this->assertContains('ROLE',$code);
    }

    public function testDescription()
    {
        $resp = new Responsibility();
        $resp = $resp->setDescription('Administre la base de données');
        $desc = $resp->getDescription();
        $this->assertContains('Administre',$desc);
    }

}
?>
