<?php
$phpmailer = new PHPMailer\PHPMailer\PHPMailer();
$phpmailer->isSMTP();
$phpmailer->Host = 'live.smtp.mailtrap.io';
$phpmailer->SMTPAuth = True;
$phpmailer->Port = 587;
$phpmailer->Username = 'api';
$phpmailer->Password = 'fb2b36b54231159f650414955d4807d3';