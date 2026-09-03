<?php

function validateRegistrationInput(string $name, string $email, string $password): array
{
	$errors = [];

	if ($name === '') {
		$errors[] = 'Name is required.';
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

