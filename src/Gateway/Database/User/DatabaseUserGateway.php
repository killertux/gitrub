<?php

namespace Gitrub\Gateway\Database\User;

use EBANX\Stream\Stream;
use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\User\Collection\UserCollection;
use Gitrub\Domain\User\Entity\User;
use Gitrub\Domain\User\Exception\UserNotFound;
use Gitrub\Domain\User\Gateway\UserGateway;
use Gitrub\Gateway\Database\MultiRowInsertStatementCreator;

class DatabaseUserGateway implements UserGateway {

	public function __construct(
		private \Pdo $connection
	) {}

	public function listUsers(FromLimit $from_limit): UserCollection {
		$statement = $this->connection->prepare('SELECT * FROM users WHERE id >= :from ORDER BY id LIMIT :limit;');
		$statement->bindParam(':from', $from_limit->from, \PDO::PARAM_INT);
		$statement->bindParam(':limit', $from_limit->limit, \PDO::PARAM_INT);
		$statement->execute();
		return (new UserCollectionFromPdoStatement(
			$statement
		))->userCollection();
	}

	public function listAdminUsers(FromLimit $from_limit): UserCollection {
		$statement = $this->connection->prepare('SELECT * FROM users WHERE id >= :from AND site_admin ORDER BY id LIMIT :limit;');
		$statement->bindParam(':from', $from_limit->from, \PDO::PARAM_INT);
		$statement->bindParam(':limit', $from_limit->limit, \PDO::PARAM_INT);
		$statement->execute();
		return (new UserCollectionFromPdoStatement(
			$statement
		))->userCollection();
	}

	public function getUserByLogin(string $login): User {
		$statement = $this->connection->prepare('SELECT * FROM users WHERE login = :login;');
		$statement->execute([':login' => $login,]);
		if ($statement->rowCount() != 1) {
			throw new UserNotFound("User not found with login $login");
		}
		return (new UserFromTableRow($statement->fetch()))->user();
	}

	public function getUserById(int $id): User {
		$statement = $this->connection->prepare('SELECT * FROM users WHERE id = :id;');
		$statement->execute([':id' => $id,]);
		if ($statement->rowCount() != 1) {
			throw new UserNotFound("User not found with id $id");
		}
		return (new UserFromTableRow($statement->fetch()))->user();
	}

	public function storeUsers(UserCollection $users): void {
		Stream::of($users)
			->map(fn (User $user) => (array)$user)
			->map(function(array $user) {
				$user['site_admin'] = $user['site_admin'] ?: 0;
				return $user;
			})
			->chunkEvery(500)
			->forEach(function (array $users): void {
				$statement = (new MultiRowInsertStatementCreator($this->connection, $users, 'users'))->statement();
				$statement->execute(Stream::of($users)->flatten()->collect());
			});
	}
}
