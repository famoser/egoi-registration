<?php

/*
 * This file is part of the famoser/egoi-registration project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

use App\Entity\Email;
use App\Entity\User;
use App\Service\Interfaces\EmailServiceInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailService implements EmailServiceInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var string
     */
    private $mailerFromEmail;

    /**
     * @var string
     */
    private $supportEmail;

    /**
     * EmailService constructor.
     */
    public function __construct(TranslatorInterface $translator, LoggerInterface $logger, RequestStack $request, ManagerRegistry $registry, MailerInterface $mailer, string $mailerFromEmail, string $supportEmail)
    {
        $this->translator = $translator;
        $this->logger = $logger;
        $this->request = $request;
        $this->manager = $registry->getManager();
        $this->mailer = $mailer;
        $this->mailerFromEmail = $mailerFromEmail;
        $this->supportEmail = $supportEmail;
    }

    public function sendRecoverConfirmLink(User $user): bool
    {
        $entity = Email::create(Email::TYPE_RECOVER_CONFIRM, $user);
        $subject = $this->translator->trans('recover_confirm.subject', ['%page%' => $this->getCurrentPage()], 'email');

        $message = (new TemplatedEmail())
            ->subject($subject)
            ->from($this->mailerFromEmail)
            ->to($user->getEmail())
            ->replyTo($this->supportEmail)
            ->returnPath($this->supportEmail)
            ->textTemplate('email/recover_confirm.txt.twig')
            ->htmlTemplate('email/recover_confirm.html.twig')
            ->context($entity->getContext());

        return $this->sendAndStoreEMail($message, $entity);
    }

    private function getCurrentPage()
    {
        return $this->request->getCurrentRequest() ? $this->request->getCurrentRequest()->getHttpHost() : 'localhost';
    }

    private function sendAndStoreEMail(TemplatedEmail $email, Email $entity): bool
    {
        try {
            $this->mailer->send($email);

            $this->manager->persist($entity);
            $this->manager->flush();

            return true;
        } catch (TransportExceptionInterface $exception) {
            $this->logger->error('email send failed', ['exception' => $exception, 'email' => $entity]);

            return false;
        }
    }
}
