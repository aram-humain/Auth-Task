<?php

function validateLoginInput(string $email, string $password): array
{
	$errors = [];

	if ($email === '') {
		$errors[] = 'Email is required.';
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$errors[] = 'Please enter a valid email address.';
	}

	if ($password === '') {
		$errors[] = 'Password is required.';
	}

	return $errors;
}