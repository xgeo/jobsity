<?php

namespace App\Api\Jobs;

class EmailSenderJob
{
    private \Swift_Mailer $mailer;
    public function __construct()
    {
        $transport = (new \Swift_SmtpTransport($_ENV['MAIL_SERVICE'], $_ENV['MAIL_PORT']))
            ->setUsername($_ENV['MAIL_ACCOUNT'])
            ->setPassword($_ENV['MAIL_PASSWORD'])
            ->setEncryption('tls');

        $this->mailer = new \Swift_Mailer($transport);
    }
    public function handle(string $email, string $stockInfo)
    {
        $this->mailer->send((new \Swift_Message('Hello from PHP Challenge'))
            ->setFrom(['phpchallenge@jobsity.io' => 'PHP Challenge'])
            ->setTo([$email])
            ->setBody($stockInfo));
    }
}