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
use App\Entity\Participant;
use App\Enum\ParticipantRole;
use App\Enum\ReviewProgress;
use App\Form\Participant\AddParticipantType;
use App\Form\Participant\EditParticipantType;
use App\Form\Participant\RemoveParticipantType;
use App\Form\Traits\EditParticipantPersonalDataType;
use App\Security\Voter\DelegationVoter;
use App\Security\Voter\ParticipantVoter;
use App\Service\Interfaces\ExportServiceInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/participant")
 */
class ParticipantController extends BaseDoctrineController
{
    /**
     * @Route("/new/{delegation}", name="participant_new")
     *
     * @return Response
     */
    public function newAction(Request $request, Delegation $delegation, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(DelegationVoter::DELEGATION_EDIT, $delegation);

        $participant = new Participant();
        $participant->setDelegation($delegation);

        $defaultGender = $translator->trans('trait.default.gender', [], 'trait_participant_personal_data');
        $participant->setGender($defaultGender);

        $form = $this->createForm(AddParticipantType::class, $participant);
        $form->add('submit', SubmitType::class, ['translation_domain' => 'participant', 'label' => 'new.submit']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->canRoleBeChosen($participant)) {
                $message = $translator->trans('new.error.role_exceeded', [], 'participant');
                $this->displaySuccess($message);
            } else {
                $this->fastSave($participant);

                $message = $translator->trans('new.success.created', [], 'participant');
                $this->displaySuccess($message);

                return $this->redirectToRoute('delegation_view', ['delegation' => $delegation->getId()]);
            }
        }

        return $this->render('participant/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/edit_personal_data/{participant}", name="participant_edit_personal_data")
     *
     * @return Response
     */
    public function editPersonalData(Request $request, Participant $participant, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(ParticipantVoter::PARTICIPANT_EDIT, $participant);

        $readOnly = ReviewProgress::REVIEWED_AND_LOCKED === $participant->getPersonalDataReviewProgress();
        $form = $this->createForm(EditParticipantPersonalDataType::class, $participant, ['disabled' => $readOnly]);
        $form->add('submit', SubmitType::class, ['translation_domain' => 'participant', 'label' => 'edit.submit']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $participant->setPersonalDataReviewProgress(ReviewProgress::EDITED);

            $this->fastSave($participant);

            $message = $translator->trans('edit.success.edited', [], 'participant');
            $this->displaySuccess($message);

            return $this->redirectToRoute('delegation_view', ['delegation' => $participant->getDelegation()->getId()]);
        }

        return $this->render('participant/edit_personal_data.html.twig', ['form' => $form->createView(), 'readonly' => $readOnly]);
    }

    /**
     * @Route("/remove/{participant}/", name="participant_remove")
     *
     * @return Response
     */
    public function removeAction(Request $request, Participant $participant, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(ParticipantVoter::PARTICIPANT_MODERATE, $participant);

        $form = $this->createForm(RemoveParticipantType::class);
        $form->add('submit', SubmitType::class, ['translation_domain' => 'participant', 'label' => 'remove.submit']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->fastRemove($participant);

            $message = $translator->trans('remove.success.removed', [], 'participant');
            $this->displaySuccess($message);

            return $this->redirectToRoute('delegation_view', ['delegation' => $participant->getDelegation()->getId()]);
        }

        return $this->render('participant/remove.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/export", name="participant_export")
     *
     * @return Response
     */
    public function exportAction(ExportServiceInterface $exportService)
    {
        $this->denyAccessUnlessGranted(ParticipantVoter::PARTICIPANT_MODERATE);

        $participants = $this->getDoctrine()->getRepository(Participant::class)->findBy([], ['familyName' => 'ASC']);

        return $exportService->exportToCsv($participants, 'participant-export', 'participants');
    }

    /**
     * @Route("/edit/{participant}", name="participant_edit")
     *
     * @return Response
     */
    public function editAction(Request $request, Participant $participant, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(ParticipantVoter::PARTICIPANT_MODERATE, $participant);

        $form = $this->createForm(EditParticipantType::class, $participant);
        $form->add('submit', SubmitType::class, ['translation_domain' => 'participant', 'label' => 'edit.submit']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->canRoleBeChosen($participant)) {
                $message = $translator->trans('new.error.role_exceeded', [], 'participant');
                $this->displaySuccess($message);
            } else {
                $this->fastSave($participant);

                $message = $translator->trans('edit.success.edited', [], 'participant');
                $this->displaySuccess($message);

                return $this->redirectToRoute('delegation_view', ['delegation' => $participant->getDelegation()->getId()]);
            }
        }

        return $this->render('participant/edit.html.twig', ['form' => $form->createView()]);
    }

    private function canRoleBeChosen(Participant $changedRoleParticipant): bool
    {
        $participants = $this->getDoctrine()->getRepository(Participant::class)->findAll();
        $sameRoleCount = 0;

        foreach ($participants as $participant) {
            if ($participant === $changedRoleParticipant || $participant->getRole() !== $changedRoleParticipant->getRole()) {
                continue;
            }

            ++$sameRoleCount;
        }

        if (ParticipantRole::ATHLETE === $changedRoleParticipant->getRole()) {
            return $changedRoleParticipant->getDelegation()->getAthleteCount() > $sameRoleCount;
        } elseif (ParticipantRole::LEADER === $changedRoleParticipant->getRole()) {
            return $changedRoleParticipant->getDelegation()->getLeaderCount() > $sameRoleCount;
        } else {
            return $changedRoleParticipant->getDelegation()->getGuestCount() > $sameRoleCount;
        }
    }
}
