<?php
namespace Tests\App\Entity;

use App\Entity\Address;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    public function testId()
    {
        $address = new Address();
        $id = $address->getId();
        $this->assertEquals(null,$id);
    }

    public function testLine()
    {
        $address = new Address();
        $address->setLine('Avenue Carnot');
        $resp = $address->getLine();
        $this->assertContains('Avenue',$resp);
    }

    public function testPostalCode()
    {
        $address = new Address();
        $address->setPostalCode('01400');
        $resp = $address->getPostalCode();
        $this->assertContains('01400',$resp);
    }

    public function testCity()
    {
        $address = new Address();
        $address->setCity('LaVille');
        $resp = $address->getCity();
        $this->assertContains('LaVille',$resp);
    }

    public function testCountry()
    {
        $address = new Address();
        $address->setCountry('France');
        $resp = $address->getCountry();
        $this->assertContains('France',$resp);
    }
}

