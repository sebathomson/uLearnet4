<?php

namespace AppSecurityBundle\Services;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class SfguardEncoderService implements PasswordEncoderInterface
{
	public function encodePassword($raw, $salt)
	{
		echo('<pre>');var_dump('holi');exit();
		return sha1($salt . $raw);
	}

	public function isPasswordValid($encoded, $raw, $salt)
	{
		return $encoded === $this->encodePassword($raw, $salt);
	}
}
