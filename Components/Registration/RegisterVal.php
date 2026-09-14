<?php

function validateRegistrationInput(
	string $firstName,
	string $lastName,
	string $email,
	string $password
): array {
	$errors = [];

	if ($firstName === '') {
		$errors[] = 'First name is required.';
	}

	if ($lastName === '') {
		$errors[] = 'Last name is required.';
	}

	if ($email === '') {
		$errors[] = 'Email is required.';
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$errors[] = 'Please enter a valid email address.';
	}

	$minimumPasswordLength = 8;

	if ($password === '') {
		$errors[] = 'Password is required.';
	} elseif (strlen($password) < $minimumPasswordLength) {
		$errors[] = 'Password must be at least 8 characters long.';
	}

	return $errors;
}

