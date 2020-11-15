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
use App\Controller\Traits\ReviewableContentEditTrait;
use App\Entity\Delegation;
use App\Form\Delegation\AddMultipleDelegationsType;
use App\Form\Delegation\EditDelegationType;
use App\Form\Delegation\RemoveDelegationType;
use App\Security\Voter\DelegationVoter;
use App\Service\Interfaces\ExportServiceInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/delegation")
 */
class DelegationController extends BaseDoctrineController
{
    /**
     * @Route("/new", name="delegation_new")
     *
     * @return Response
     */
    public function newAction(Request $request, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(DelegationVoter::DELEGATION_MODERATE);

        $form = $this->createForm(AddMultipleDelegationsType::class);
        $form->add('submit', SubmitType::class, ['translation_domain' => 'delegation', 'label' => 'new.submit']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $commaSeparatedDelegationNames = $form->get('commaSeparatedDelegationNames')->getData();
            $delegationNames = explode(',', $commaSeparatedDelegationNames);

            $existingDelegations = $this->getDoctrine()->getRepository(Delegation::class)->findAll();
            $existingDelegationNamesLowercase = array_map(function (Delegation $delegation) {
                return strtolower($delegation->getName());
            }, $existingDelegations);

            $delegations = [];
            foreach ($delegationNames as $delegationName) {
                $cleanedDelegationName = trim($delegationName);
                if (0 === strlen($cleanedDelegationName) || in_array(strtolower($cleanedDelegationName), $existingDelegationNamesLowercase)) {
                    continue;
                }

                $delegation = new Delegation();
                $delegation->setName($cleanedDelegationName);
                $delegations[] = $delegation;
            }

            $this->fastSave(...$delegations);

            $message = $translator->trans('new.success.created', ['%count%' => count($delegations)], 'delegation');
            $this->displaySuccess($message);

            return $this->redirectToRoute('index');
        }

        return $this->render('delegation/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/view/{delegation}", name="delegation_view")
     *
     * @return Response
     */
    public function viewAction(Delegation $delegation)
    {
        $this->denyAccessUnlessGranted(DelegationVoter::DELEGATION_VIEW, $delegation);

        return $this->render('delegation/view.html.twig', ['delegation' => $delegation]);
    }

    /**
     * @Route("/users/{delegation}", name="delegation_users")
     *
     * @return Response
     */
    public function usersAction(Delegation $delegation)
    {
        $this->denyAccessUnlessGranted(DelegationVoter::DELEGATION_VIEW, $delegation);

        return $this->render('delegation/users.html.twig', ['delegation' => $delegation]);
    }

    use ReviewableContentEditTrait;

    /**
     * @Route("/edit_attendance/{delegation}", name="delegation_edit_attendance")
     *
     * @return Response
     */
    public function editAttendanceAction(Request $request, Delegation $delegation, TranslatorInterface $translator)
    {
        return $this->editReviewableDelegationContent($request, $translator, $delegation, 'attendance');
    }

    /**
     * @Route("/edit_contribution/{delegation}", name="delegation_edit_contribution")
     *
     * @return Response
     */
    public function editContributionAction(Request $request, Delegation $delegation, TranslatorInterface $translator)
    {
        return $this->editReviewableDelegationContent($request, $translator, $delegation, 'contribution');
    }

    /**
     * @Route("/edit_travel/{delegation}", name="delegation_edit_travel")
     *
     * @return Response
     */
    public function editTravelAction(Request $request, Delegation $delegation, TranslatorInterface $translator)
    {
        return $this->editReviewableDelegationContent($request, $translator, $delegation, 'travel');
    }

    /**
     * @Route("/remove/{delegation}/", name="delegation_remove")
     *
     * @return Response
     */
    public function removeAction(Request $request, Delegation $delegation, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(DelegationVoter::DELEGATION_MODERATE, $delegation);

        $form = $this->createForm(RemoveDelegationType::class);
        $form->add('submit', SubmitType::class, ['translation_domain' => 'delegation', 'label' => 'remove.submit']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $toRemove = [$delegation];
            foreach ($delegation->getParticipants() as $participant) {
                $toRemove[] = $participant;
            }
            foreach ($delegation->getUsers() as $user) {
                $toRemove[] = $user;
            }
            $this->fastRemove(...$toRemove);

            $message = $translator->trans('remove.success.removed', [], 'delegation');
            $this->displaySuccess($message);

            return $this->redirectToRoute('index');
        }

        return $this->render('delegation/remove.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/export", name="delegation_export")
     *
     * @return Response
     */
    public function exportAction(ExportServiceInterface $exportService)
    {
        $this->denyAccessUnlessGranted(DelegationVoter::DELEGATION_MODERATE);

        $delegations = $this->getDoctrine()->getRepository(Delegation::class)->findBy([], ['name' => 'ASC']);

        return $exportService->exportToCsv($delegations, 'delegation-export', 'delegations');
    }

    /**
     * @Route("/edit/{delegation}", name="delegation_edit")
     *
     * @return Response
     */
    public function editAction(Request $request, Delegation $delegation, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(DelegationVoter::DELEGATION_MODERATE, $delegation);

        $form = $this->createForm(EditDelegationType::class, $delegation, ['required' => false]);
        $form->add('submit', SubmitType::class, ['translation_domain' => 'delegation', 'label' => 'edit.submit']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $existingDelegations = $this->getDoctrine()->getRepository(Delegation::class)->findBy(['name' => $delegation->getName()]);
            if (count($existingDelegations) > 0) {
                $message = $translator->trans('edit.error.name_already_taken', [], 'delegation');
                $this->displayError($message);
            } else {
                $this->fastSave($delegation);

                $message = $translator->trans('edit.success.edited', [], 'delegation');
                $this->displaySuccess($message);

                return $this->redirectToRoute('index');
            }
        }

        return $this->render('delegation/edit.html.twig', ['form' => $form->createView()]);
    }
}
