<?php
namespace Tests\App\Entity;

use App\Entity\People;
use PHPUnit\Framework\TestCase;
use App\Entity\Denomination;
use App\Entity\User;
use App\Entity\UserUpdater;
use App\Entity\Address;

class PeopleTest extends TestCase
{
    public function testId()
    {
        $people = new People();
        $id = $people->getId();
        $this->assertEquals(-1,$id);
    }

    public function testFirstName()
    {
        $people = new People();
        $people->setFirstName('Léa');
        $resp = $people->getFirstName();
        $this->assertContains('Léa',$resp);
    }

    public function testLastName()
    {
        $people = new People();
        $people->setLastName('Marnier');
        $resp = $people->getLastName();
        $this->assertContains('Marnier',$resp);
    }

    public function testEmailAddress()
    {
        $people = new People();
        $people->setEmailAddress('lea@mail.com');
        $resp = $people->getEmailAddress();
        $this->assertContains('lea@mail.com',$resp);
    }

    public function testCellPhoneNumber()
    {
        $people = new People();
        $people->setCellPhoneNumber('0123456789');
        $resp = $people->getCellPhoneNumber();
        $this->assertContains('0123456789',$resp);
    }

    public function testHomePhoneNumber()
    {
        $people = new People();
        $people->setHomePhoneNumber('0123456789');
        $resp = $people->getHomePhoneNumber();
        $this->assertContains('0123456789',$resp);
    }

    public function testWorkPhoneNumber()
    {
        $people = new People();
        $people->setWorkPhoneNumber('0123456789');
        $resp = $people->getWorkPhoneNumber();
        $this->assertContains('0123456789',$resp);
    }

    public function testWorkFaxNumber()
    {
        $people = new People();
        $people->setWorkFaxNumber('0123456789');
        $resp = $people->getWorkFaxNumber();
        $this->assertContains('0123456789',$resp);
    }

    public function testObservations()
    {
        $people = new People();
        $people->setObservations('coin coin');
        $resp = $people->getObservations();
        $this->assertContains('coin',$resp);
    }

    public function testSensitiveObservations()
    {
        $people = new People();
        $people->setSensitiveObservations('ouaf ouaf');
        $resp = $people->getSensitiveObservations();
        $this->assertContains('ouaf',$resp);
    }

    public function testDenomination()
    {
        $people = new People();
        $denomination = new Denomination('Mix');
        $people->setDenomination($denomination);
        $resp = $people->getDenomination();

        $this->assertSame($denomination,$resp);
    }

    public function testUser()
    {
        $people = new People();
        $user = new User();
        $people->setUser($user);
        $resp = $people->getUser();

        $this->assertSame($user,$resp);
    }

    public function testIsReceivingNewsletter()
    {
        $people = new People();
        $people->setIsReceivingNewsletter(true);
        $resp = $people->getIsReceivingNewsletter();
        $this->assertEquals(true,$resp);
    }

    public function testNewsletterDematerialization()
    {
        $people = new People();
        $people->setNewsletterDematerialization(true);
        $resp = $people->getNewsletterDematerialization();
        $this->assertEquals(true,$resp);
    }

    public function testSetUpdaters()
    {
        $people = new People();
        $updater = new UserUpdater();
        $people->setUpdaters([$updater]);
        $resp = $people->getUpdaters();

        $this->assertContains($updater,$resp);
    }

    public function testAddUpdaters()
    {
        $people = new People();
        $updater = new UserUpdater();
        $people->addUpdater($updater);
        $resp = $people->getUpdaters();

        $this->assertContains($updater,$resp);
    }

    public function testGetAddress()
    {
        $people = new People();
        $address = new Address();
        $people->setAddresses([$address]);
        $resp = $people->getAddresses();

        $this->assertContains($address,$resp);
    }

    public function testAddAddress()
    {
        $people = new People();
        $address = new Address();
        $people->addAddress($address);
        $resp = $people->getAddresses();

        $this->assertContains($address,$resp);
    }

}

?>
