<?php

namespace Gitrub\Gateway\Database\User;

use Gitrub\Domain\User\Collection\UserCollection;

class UserCollectionFromPdoStatement {

	public function __construct(
		private \PDOStatement $statement
	) {}

	public function userCollection(): UserCollection {
		$generator = function (\PDOStatement $statement) {
			while($data = $statement->fetch()) {
				yield (new UserFromTableRow($data))->user();
			}
		};
		return new UserCollection($generator($this->statement));
	}
}
