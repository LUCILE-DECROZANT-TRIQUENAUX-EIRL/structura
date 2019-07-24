<?php
namespace Tests\App\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use App\Entity\Responsibility;

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

    public function testResponsibilities()
    {
        $user = new User();
        $responsibility = new Responsibility(null,'ROLE_ADMIN','admin');
        $responsibilitysecond = new Responsibility(null,'ROLE_ADHE','adh');
        $user->setResponsibilities([$responsibility]);
        $user->addResponsibility($responsibilitysecond);
        $resp = $user->getResponsibilities();
        $roles = $user->getRoles();

        $this->assertContains('ROLE_ADMIN',$roles);
    }
}

?>
