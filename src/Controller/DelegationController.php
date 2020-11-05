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
use App\Entity\Delegation;
use App\Enum\ReviewProgress;
use App\Form\Delegation\AddMultipleDelegationsType;
use App\Form\Delegation\EditDelegationType;
use App\Form\Delegation\RemoveDelegationType;
use App\Form\Traits\EditDelegationAttendanceType;
use App\Security\Voter\DelegationVoter;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
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
            $existingDelegationNames = array_map(function (Delegation $delegation) {
                return $delegation->getName();
            }, $existingDelegations);

            $delegations = [];
            foreach ($delegationNames as $delegationName) {
                $cleanedDelegationName = trim($delegationName);
                if (0 === strlen($cleanedDelegationName) || in_array($cleanedDelegationName, $existingDelegationNames)) {
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
     * @Route("/export", name="delegation_export")
     *
     * @return Response
     */
    public function exportAction(SerializerInterface $serializer)
    {
        $this->denyAccessUnlessGranted(DelegationVoter::DELEGATION_MODERATE);

        $delegations = $this->getDoctrine()->getRepository(Delegation::class)->findBy([], ['name' => 'ASC']);
        $content = $serializer->serialize($delegations, 'csv', ['groups' => 'delegation-export']);

        $response = new StreamedResponse();
        $response->setCallback(
            function () use ($content) {
                echo $content;
            }
        );
        $response->setStatusCode(200);

        $filename = (new \DateTime())->format('c').' - delegations.csv';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Disposition', $dispositionHeader);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');

        return $response;
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
     * @Route("/edit/{delegation}", name="delegation_edit")
     *
     * @return Response
     */
    public function editAction(Request $request, Delegation $delegation, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(DelegationVoter::DELEGATION_MODERATE, $delegation);

        $form = $this->createForm(EditDelegationType::class, $delegation);
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

    /**
     * @Route("/edit_attendance/{delegation}", name="delegation_edit_attendance")
     *
     * @return Response
     */
    public function editAttendanceAction(Request $request, Delegation $delegation, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(DelegationVoter::DELEGATION_EDIT, $delegation);

        $readOnly = ReviewProgress::REVIEWED_AND_LOCKED === $delegation->getAttendanceReviewProgress();
        $form = $this->createForm(EditDelegationAttendanceType::class, $delegation, ['disabled' => $readOnly]);
        $form->add('submit', SubmitType::class, ['translation_domain' => 'delegation', 'label' => 'edit.submit']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $delegation->setAttendanceReviewProgress(ReviewProgress::EDITED);

            $this->fastSave($delegation);

            $message = $translator->trans('edit.success.edited', [], 'delegation');
            $this->displaySuccess($message);

            return $this->redirectToRoute('delegation_view', ['delegation' => $delegation->getId()]);
        }

        return $this->render('delegation/edit_attendance.html.twig', ['form' => $form->createView(), 'readonly' => $readOnly]);
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
}
