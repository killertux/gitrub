<?php

namespace Gitrub\Domain\User\Collection;

use Gitrub\Domain\User\Entity\User;

class UserCollection extends \IteratorIterator {

	public function current(): User {
		return parent::current();
	}
}
