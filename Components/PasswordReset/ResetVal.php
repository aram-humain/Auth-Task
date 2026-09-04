<?php

function validateResetPassword(string $password, string $passwordConfirmation): array
{
	$errors = [];

	$minimumPasswordLength = 8;

	if ($password === '') {
		$errors[] = 'Password is required.';
	} elseif (strlen($password) < $minimumPasswordLength) {
		$errors[] = 'Password must be at least 8 characters long.';
	}

	if ($password !== $passwordConfirmation) {
		$errors[] = 'Passwords do not match.';
	}

	return $errors;
}

