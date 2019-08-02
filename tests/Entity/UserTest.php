<?php
namespace Tests\App\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use App\Entity\Responsibility;
use App\Entity\UserUpdater;
use App\Entity\People;

class UserTest extends TestCase
{
    public function testId()
    {
        $user = new User();
        $id = $user->getId();
        $this->assertEquals(-1,$id);
    }

    public function testUsername()
    {
        $user = new User();
        $user->setUsername('usertest');
        $username = $user->getUsername();
        $this->assertContains('usertest',$username);
    }

    public function testPlainPassword()
    {
        $user = new User();
        $user->setPlainPassword('mdptest');
        $plain = $user->getPlainPassword();
        $this->assertContains('mdptest',$plain);
    }

    public function testPassword()
    {
        $user = new User();
        $user->setPassword('bWRwdGVzdA==');
        $encode = $user->getPassword();
        $this->assertContains('bWRwdGVzdA==',$encode);
    }

    public function testSetResponsibilities()
    {
        $user = new User();
        $responsibility = new Responsibility(null,'ROLE_ADMIN','admin');
        $user->setResponsibilities([$responsibility]);
        // We need both functions because responsibility variables are private
        $resp = $user->getResponsibilities();
        $roles = $user->getRoles();

        $this->assertContains('ROLE_ADMIN',$roles);
    }

    public function testAddResponsibilities()
    {
        $user = new User();
        $responsibility = new Responsibility(null,'ROLE_ADHE','adh');
        $user->addResponsibility($responsibility);
        $resp = $user->getResponsibilities();
        $roles = $user->getRoles();

        $this->assertContains('ROLE_ADHE',$roles);
    }

    public function testSetUpdaters()
    {
        $user = new User();
        $updater = new UserUpdater($user,$user,NULL);
        $user->setUpdaters([$updater]);
        $resp = $user->getUpdaters();

        $this->assertContains($updater,$resp);
    }

    public function testAddUpdaters()
    {
        $user = new User();
        $updater = new UserUpdater($user,$user,NULL);
        $user->addUpdater($updater);
        $resp = $user->getUpdaters();

        $this->assertContains($updater,$resp);
    }

    public function testPeople()
    {
        $user = new User();
        $people = new People(NULL,$user,NULL,NULL,NULL);
        $user->setPeople($people);
        $resp = $user->getPeople();
        $this->assertEquals($people,$resp);
    }

}

?>
