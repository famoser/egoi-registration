<?php

/*
 * This file is part of the mangel.io project.
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
     * @Route("", name="user_index")
     *
     * @return Response
     */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted(UserVoter::USER_VIEW);

        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        $usersByDelegations = [];
        foreach ($users as $user) {
            if (null === $user->getDelegation()) {
                $usersByDelegations[''][] = $user;
            } else {
                $usersByDelegations[$user->getDelegation()->getName()][] = $user;
            }
        }

        ksort($usersByDelegations);

        return $this->render('user/index.html.twig', ['users' => $users]);
    }

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
            $admins = $this->getDoctrine()->getRepository(User::class)->findBy(['isAdmin' => true]);
            if (1 === count($admins) && $admins[0] === $this->getUser() && $admins[0] === $user) {
                $message = $translator->trans('remove.error.can_not_remove_last_admin', [], 'user');
                $this->displayError($message);
            } else {
                $this->fastRemove($user);

                $message = $translator->trans('remove.success.removed', [], 'user');
                $this->displaySuccess($message);
            }

            if ($this->getUser()->getDelegation()) {
                return $this->redirectToRoute('delegation_users', ['delegation' => $user->getDelegation()->getId()]);
            }

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/remove.html.twig', ['form' => $form->createView()]);
    }
}
