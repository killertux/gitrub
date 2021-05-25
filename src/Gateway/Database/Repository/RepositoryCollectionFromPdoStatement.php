<?php

namespace Gitrub\Gateway\Database\Repository;

use Gitrub\Domain\Repository\Collection\RepositoryCollection;

class RepositoryCollectionFromPdoStatement {

	public function __construct(
		private \PDOStatement $statement
	) {}

	public function repositoryCollection(): RepositoryCollection {
		$generator = function (\PDOStatement $statement) {
			while($data = $statement->fetch()) {
				yield (new RepositoryFromTableRow($data))->repository();
			}
		};
		return new RepositoryCollection($generator($this->statement));
	}
}
