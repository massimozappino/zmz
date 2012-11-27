<?php

class Model_UsersTest extends ControllerTestCase
{

    public function testHashPassword()
    {
        $modelUser = new Model_Users();
        $myPassword = "ciccio";
        $mySalt = "dsfgsdbsdbg";
        $result = Model_Users::hashPassword($myPassword, $mySalt);
        $this->assertEquals($result, "6e62e7be41fa490f007a2990f5d827bc6b4a9fdb531a1e5065f673fcda50010e");
        $this->assertNotEquals($result, "dgfdsgfdsghdhdsh");
    }

}
