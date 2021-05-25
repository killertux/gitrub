<?php

namespace Gitrub\Gateway\Database\Repository;

use EBANX\Stream\Stream;
use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\Repository\Collection\RepositoryCollection;
use Gitrub\Domain\Repository\Entity\Repository;
use Gitrub\Domain\Repository\Exception\RepositoryNotFound;
use Gitrub\Domain\Repository\Gateway\RepositoryGateway;
use Gitrub\Domain\User\Collection\UserCollection;
use Gitrub\Domain\User\Gateway\UserGateway;
use Gitrub\Gateway\Database\MultiRowInsertStatementCreator;

class DatabaseRepositoryGateway implements RepositoryGateway {

	public function __construct(
		private \PDO $connection,
		private UserGateway $user_gateway,
	) {}

	public function listRepositories(FromLimit $from_limit): RepositoryCollection {
		$sql = <<<SQL
SELECT r.*,
       owner.login owner_login, owner.node_id owner_node_id, owner.avatar_url owner_avatar_url,
       owner.gravatar_id owner_gravatar_id, owner.url owner_url, owner.html_url owner_html_url,
       owner.followers_url owner_followers_url, owner.following_url owner_following_url, owner.gists_url owner_gists_url,
       owner.starred_url owner_starred_url, owner.subscriptions_url owner_subscriptions_url,owner.organizations_url owner_organizations_url,
       owner.repos_url owner_repos_url, owner.events_url owner_events_url, owner.received_events_url owner_received_events_url,
       owner.type owner_type, owner.site_admin owner_site_admin
FROM repositories r
JOIN users owner on r.owner_id = owner.id
WHERE r.id >= :from ORDER BY r.id LIMIT :limit;
SQL;
		$statement = $this->connection->prepare($sql);
		$statement->bindParam(':from', $from_limit->from, \PDO::PARAM_INT);
		$statement->bindParam(':limit', $from_limit->limit, \PDO::PARAM_INT);
		$statement->execute();
		return (new RepositoryCollectionFromPdoStatement(
			$statement
		))->repositoryCollection();
	}

	public function listForkRepositories(FromLimit $from_limit): RepositoryCollection {
		$sql = <<<SQL
SELECT r.*,
       owner.login owner_login, owner.node_id owner_node_id, owner.avatar_url owner_avatar_url,
       owner.gravatar_id owner_gravatar_id, owner.url owner_url, owner.html_url owner_html_url,
       owner.followers_url owner_followers_url, owner.following_url owner_following_url, owner.gists_url owner_gists_url,
       owner.starred_url owner_starred_url, owner.subscriptions_url owner_subscriptions_url,owner.organizations_url owner_organizations_url,
       owner.repos_url owner_repos_url, owner.events_url owner_events_url, owner.received_events_url owner_received_events_url,
       owner.type owner_type, owner.site_admin owner_site_admin
FROM repositories r
JOIN users owner on r.owner_id = owner.id
WHERE r.id >= :from AND r.fork ORDER BY r.id LIMIT :limit;
SQL;
		$statement = $this->connection->prepare($sql);
		$statement->bindParam(':from', $from_limit->from, \PDO::PARAM_INT);
		$statement->bindParam(':limit', $from_limit->limit, \PDO::PARAM_INT);
		$statement->execute();
		return (new RepositoryCollectionFromPdoStatement(
			$statement
		))->repositoryCollection();
	}

	public function listRepositoriesFromOwner(int $owner_id, FromLimit $from_limit): RepositoryCollection {
		$sql = <<<SQL
SELECT r.*,
       owner.login owner_login, owner.node_id owner_node_id, owner.avatar_url owner_avatar_url,
       owner.gravatar_id owner_gravatar_id, owner.url owner_url, owner.html_url owner_html_url,
       owner.followers_url owner_followers_url, owner.following_url owner_following_url, owner.gists_url owner_gists_url,
       owner.starred_url owner_starred_url, owner.subscriptions_url owner_subscriptions_url,owner.organizations_url owner_organizations_url,
       owner.repos_url owner_repos_url, owner.events_url owner_events_url, owner.received_events_url owner_received_events_url,
       owner.type owner_type, owner.site_admin owner_site_admin
FROM repositories r
JOIN users owner on r.owner_id = owner.id
WHERE r.id >= :from AND r.owner_id = :owner_id ORDER BY r.id LIMIT :limit;
SQL;
		$statement = $this->connection->prepare($sql);
		$statement->bindParam(':owner_id', $owner_id, \PDO::PARAM_INT);
		$statement->bindParam(':from', $from_limit->from, \PDO::PARAM_INT);
		$statement->bindParam(':limit', $from_limit->limit, \PDO::PARAM_INT);
		$statement->execute();
		return (new RepositoryCollectionFromPdoStatement(
			$statement
		))->repositoryCollection();
	}

	public function listRepositoriesWithName(string $name, FromLimit $from_limit): RepositoryCollection {
		$sql = <<<SQL
SELECT r.*,
       owner.login owner_login, owner.node_id owner_node_id, owner.avatar_url owner_avatar_url,
       owner.gravatar_id owner_gravatar_id, owner.url owner_url, owner.html_url owner_html_url,
       owner.followers_url owner_followers_url, owner.following_url owner_following_url, owner.gists_url owner_gists_url,
       owner.starred_url owner_starred_url, owner.subscriptions_url owner_subscriptions_url,owner.organizations_url owner_organizations_url,
       owner.repos_url owner_repos_url, owner.events_url owner_events_url, owner.received_events_url owner_received_events_url,
       owner.type owner_type, owner.site_admin owner_site_admin
FROM repositories r
JOIN users owner on r.owner_id = owner.id
WHERE r.id >= :from AND r.name = :name ORDER BY r.id LIMIT :limit;
SQL;
		$statement = $this->connection->prepare($sql);
		$statement->bindParam(':name', $name, \PDO::PARAM_STR);
		$statement->bindParam(':from', $from_limit->from, \PDO::PARAM_INT);
		$statement->bindParam(':limit', $from_limit->limit, \PDO::PARAM_INT);
		$statement->execute();
		return (new RepositoryCollectionFromPdoStatement(
			$statement
		))->repositoryCollection();
	}

	public function getRepositoryByFullName(string $full_name): Repository {
		$sql = <<<SQL
SELECT r.*,
       owner.login owner_login, owner.node_id owner_node_id, owner.avatar_url owner_avatar_url,
       owner.gravatar_id owner_gravatar_id, owner.url owner_url, owner.html_url owner_html_url,
       owner.followers_url owner_followers_url, owner.following_url owner_following_url, owner.gists_url owner_gists_url,
       owner.starred_url owner_starred_url, owner.subscriptions_url owner_subscriptions_url,owner.organizations_url owner_organizations_url,
       owner.repos_url owner_repos_url, owner.events_url owner_events_url, owner.received_events_url owner_received_events_url,
       owner.type owner_type, owner.site_admin owner_site_admin
FROM repositories r
JOIN users owner on r.owner_id = owner.id
WHERE r.full_name = :full_name;
SQL;
		$statement = $this->connection->prepare($sql);
		$statement->execute([':full_name' => $full_name,]);
		if ($statement->rowCount() != 1) {
			throw new RepositoryNotFound('Repository not found with full name ' . $full_name);
		}
		return (new RepositoryFromTableRow($statement->fetch()))->repository();
	}

	public function getRepositoryById(int $id): Repository {
		$sql = <<<SQL
SELECT r.*,
       owner.login owner_login, owner.node_id owner_node_id, owner.avatar_url owner_avatar_url,
       owner.gravatar_id owner_gravatar_id, owner.url owner_url, owner.html_url owner_html_url,
       owner.followers_url owner_followers_url, owner.following_url owner_following_url, owner.gists_url owner_gists_url,
       owner.starred_url owner_starred_url, owner.subscriptions_url owner_subscriptions_url,owner.organizations_url owner_organizations_url,
       owner.repos_url owner_repos_url, owner.events_url owner_events_url, owner.received_events_url owner_received_events_url,
       owner.type owner_type, owner.site_admin owner_site_admin
FROM repositories r
JOIN users owner on r.owner_id = owner.id
WHERE r.id = :id;
SQL;
		$statement = $this->connection->prepare($sql);
		$statement->execute([':id' => $id,]);
		if ($statement->rowCount() != 1) {
			throw new RepositoryNotFound('Repository not found with id ' . $id);
		}
		return (new RepositoryFromTableRow($statement->fetch()))->repository();
	}

	public function storeRepositories(RepositoryCollection $repositories): void {
		Stream::of($repositories)
			->map(fn(Repository $repository) => (array)$repository)
			->map(function (array $repository) {
				$repository['private'] = $repository['private'] ?: 0;
				$repository['fork'] = $repository['fork'] ?: 0;
				return $repository;
			})
			->chunkEvery(500)
			->forEach(function (array $repositories): void {
				$owners = Stream::of($repositories)
					->pluck('owner');
				$this->user_gateway->storeUsers(new UserCollection($owners));
				$repositories = Stream::of($repositories)
					->map(function(array $repository) {
						$repository['owner_id'] = $repository['owner']->id;
						unset($repository['owner']);
						return $repository;
					})
					->collect();
				$statement = (new MultiRowInsertStatementCreator($this->connection, $repositories, 'repositories'))->statement();
				$statement->execute(Stream::of($repositories)->flatten()->collect());
			});
	}
}
