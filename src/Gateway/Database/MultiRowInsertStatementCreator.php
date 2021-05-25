<?php

namespace Gitrub\Gateway\Database;

use EBANX\Stream\Stream;

class MultiRowInsertStatementCreator {

	public function __construct(
		private \PDO $connection,
		private array $data,
		private string $table
	) {}

	public function statement(): \PDOStatement {
		$columns = array_keys($this->data[0]);

		$insert_sql = 'INSERT INTO ' . $this->table . '(' . implode(', ', $columns) . ') VALUES ';
		$row = '(' . implode(',', array_fill(0, count($columns), '?')) . ')';
		$insert_sql .= implode(',', array_fill(0, count($this->data), $row));
		$insert_sql .= ' AS new ON DUPLICATE KEY UPDATE ';
		$insert_sql .= Stream::of($columns)
			->map(fn (string $column) => "$column = new.$column")
			->join(', ') . ';';
		return $this->connection->prepare($insert_sql);
	}
}
