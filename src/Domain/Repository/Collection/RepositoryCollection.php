<?php

namespace Gitrub\Domain\Repository\Collection;

use Gitrub\Domain\Repository\Entity\Repository;

class RepositoryCollection extends \IteratorIterator {

	public function current(): Repository {
		return parent::current();
	}
}
