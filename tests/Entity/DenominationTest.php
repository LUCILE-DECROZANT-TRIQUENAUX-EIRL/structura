<?php
namespace Tests\App\Entity;

use App\Entity\Denomination;
use PHPUnit\Framework\TestCase;

class DenominationTest extends TestCase
{

    public function testId()
    {
        $resp = new Denomination('Monsieur');
        $id = $resp->getId();
        $this->assertEquals(null,$id);
    }

    public function testLabel()
    {
        $resp = new Denomination('Monsieur');
        $resp = $resp->setLabel('Monsieurr');
        $label = $resp->getLabel();
        $this->assertContains('Monsieurr',$label);
    }
}

