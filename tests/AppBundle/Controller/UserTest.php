<?php
namespace Tests\AppBundle\Util;

use AppBundle\Entity\User;
use PHPUnit\Framework\TestCase;
use AppBundle\Entity\Responsability;

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

    public function testResponsabilities()
    {
        $user = new User();
        $responsability = new Responsability(null,'ROLE_ADMIN','admin');
        $responsabilitysecond = new Responsability(null,'ROLE_ADHERENT_E','adhé');
        $user->setResponsabilities([$responsability]);
        $user->addResponsability($responsabilitysecond->getLabel());
        $resp = $user->getResponsabilities();
        $roles = $user->getRoles();

        $this->assertContains('admin',$roles);
        $this->assertContains('ROLE_ADHé',$roles);
    }
}

?>
