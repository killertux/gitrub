<?php

namespace Test\Gitrub\Gateway\Github\RestApi;

use Gitrub\Gateway\Github\RestApi\GithubPersonalAccessToken;
use Test\Gitrub\GitrubTestCase;

class GithubPersonalAccessTokenTest extends GitrubTestCase {

	public static function dataProviderForTestShouldUseToken(): \Generator {
		yield 'Both are null' => [
			'username' => null,
			'token' => null,
			'should_use_token' => false,
		];
		yield 'Username is null' => [
			'username' => null,
			'token' => 'some-token',
			'should_use_token' => false,
		];
		yield 'Username is empty' => [
			'username' => '',
			'token' => 'some-token',
			'should_use_token' => false,
		];
		yield 'Token is null' => [
			'username' => 'some-username',
			'token' => null,
			'should_use_token' => false,
		];
		yield 'Token is empty' => [
			'username' => 'some-username',
			'token' => '',
			'should_use_token' => false,
		];
		yield 'Both are empty' => [
			'username' => '',
			'token' => '',
			'should_use_token' => false,
		];
		yield 'Both are filled' => [
			'username' => 'some-username',
			'token' => 'some-token',
			'should_use_token' => true,
		];
	}

	/** @dataProvider  dataProviderForTestShouldUseToken */
	public function testShouldUseToken(?string $username, ?string $token, bool $should_use_token): void {
		self::assertEquals(
			$should_use_token,
			(new GithubPersonalAccessToken($username, $token))->shouldUseToken()
		);
	}

	public function testGetAuthorizationToken(): void {
		self::assertEquals(
			'Basic ' . base64_encode('username:token'),
			(new GithubPersonalAccessToken('username', 'token'))->getAuthorizationToken()
		);
	}
}
