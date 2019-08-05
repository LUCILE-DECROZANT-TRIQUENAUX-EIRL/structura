<?php
namespace Tests\App\Entity;

use App\Entity\UserUpdater;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use App\Entity\Responsibility;

class UserUpdaterTest extends TestCase
{

    public function testUser()
    {
        $updater = new UserUpdater();
        $user = new User();
        $updater->setUser($user);
        $resp = $updater->getUser();
        $resp = $resp->getId();

        $this->assertEquals(-1,$resp);
    }

    public function testUpdater()
    {
        $updater = new UserUpdater();
        $user = new User();
        $updater->setUpdater($user);
        $resp = $updater->getUpdater();
        $resp = $resp->getId();

        $this->assertEquals(-1,$resp);
    }

    public function testUsername()
    {
        $updater = new UserUpdater();
        $updater->setDate('20/07/2017');
        $resp = $updater->getDate();
        $this->assertContains('20/07/2017',$resp);
    }
}
?>
