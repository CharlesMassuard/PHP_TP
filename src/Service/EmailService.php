<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig
    ) {}

    public function sendReservationEmail(string $to, array $data): void
    {
        $html = $this->twig->render('emails/reservation.html.twig', $data);

        $email = (new Email())
            ->from('ne-pas-repondre@librashelf.fr')
            ->to($to)
            ->subject('Confirmation de réservation')
            ->html($html);

        $this->mailer->send($email);
    }

    public function sendEmpruntEmail(string $to, array $data): void
    {
        $html = $this->twig->render('emails/emprunt.html.twig', $data);

        $email = (new Email())
            ->from('ne-pas-repondre@librashelf.fr')
            ->to($to)
            ->subject('Confirmation d’emprunt')
            ->html($html);

        $this->mailer->send($email);
    }
}
