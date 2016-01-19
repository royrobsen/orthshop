<?php

namespace Orth\IndexBundle\Form\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePassword
{
    /**
     * @SecurityAssert\UserPassword(
     *     message = "Falsches Passwort eingegeben"
     * )
     */
     public $oldPassword;

    /**
     * @Assert\Length(
     *     min = 6,
     *     minMessage = "Das Passwort muss mindestens 6 Zeichen haben"
     * )
     */
     public $newPassword;
     
     /**
     * Get newPassword
     *
     * @return integer 
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }
}