<?php

/*
 * This file is part of the famoser/egoi-registration project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Controller\Base\BaseDoctrineController;
use App\Entity\User;
use App\Form\User\RemoveUserType;
use App\Security\Voter\UserVoter;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/users")
 */
class UserController extends BaseDoctrineController
{
    /**
     * @Route("/remove/{user}/", name="user_remove")
     *
     * @return Response
     */
    public function removeAction(Request $request, User $user, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(UserVoter::USER_REMOVE, $user);

        $form = $this->createForm(RemoveUserType::class);
        $form->add('submit', SubmitType::class, ['translation_domain' => 'user', 'label' => 'remove.submit']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $delegation = $user->getDelegation();

            $this->fastRemove($user);

            $message = $translator->trans('remove.success.removed', [], 'user');
            $this->displaySuccess($message);

            return $this->redirectToRoute('delegation_users', ['delegation' => $delegation->getId()]);
        }

        return $this->render('user/remove.html.twig', ['form' => $form->createView()]);
    }
}
