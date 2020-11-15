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
use App\Entity\Participant;
use App\Enum\ParticipantRole;
use App\Form\Participant\EditParticipantType;
use App\Form\Participant\RemoveParticipantType;
use App\Form\Traits\EditParticipantPersonalDataType;
use App\Security\Voter\DelegationVoter;
use App\Security\Voter\ParticipantVoter;
use App\Service\Interfaces\ExportServiceInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
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
     * @Route("/new/{delegation}/{role}", name="participant_new")
     *
     * @return Response
     */
    public function newAction(Request $request, Delegation $delegation, int $role, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(DelegationVoter::DELEGATION_EDIT, $delegation);

        if (!$this->canRoleBeChosen($role, $translator, $delegation, $delegation->getParticipants()->toArray())) {
            throw new BadRequestException();
        }

        $participant = new Participant();
        $participant->setRole($role);
        $participant->setDelegation($delegation);
        $participant->setCountryOfResidence($delegation->getName());
        $participant->setNationality($delegation->getName());
        $participant->setPlaceOfBirth($delegation->getName());

        $form = $this->createForm(EditParticipantPersonalDataType::class, $participant);
        $form->add('submit', SubmitType::class, ['translation_domain' => 'participant', 'label' => 'new.submit']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->fastSave($participant);

            $roleTranslation = ParticipantRole::getTranslationForValue($role, $translator);
            $message = $translator->trans('new.success.created', ['%role%' => $roleTranslation], 'participant');
            $this->displaySuccess($message);

            return $this->redirectToRoute('delegation_view', ['delegation' => $delegation->getId()]);
        }

        return $this->render('participant/new.html.twig', ['form' => $form->createView(), 'role' => $role]);
    }

    use ReviewableContentEditTrait;

    /**
     * @Route("/edit_personal_data/{participant}", name="participant_edit_personal_data")
     *
     * @return Response
     */
    public function editPersonalDataAction(Request $request, Participant $participant, TranslatorInterface $translator)
    {
        return $this->editReviewableParticipantContent($request, $translator, $participant, 'personal_data');
    }

    /**
     * @Route("/edit_immigration/{participant}", name="participant_edit_immigration")
     *
     * @return Response
     */
    public function editImmigrationAction(Request $request, Participant $participant, TranslatorInterface $translator)
    {
        return $this->editReviewableParticipantContent($request, $translator, $participant, 'immigration');
    }

    /**
     * @Route("/edit_event_presence/{participant}", name="participant_edit_event_presence")
     *
     * @return Response
     */
    public function editEventPresenceAction(Request $request, Participant $participant, TranslatorInterface $translator)
    {
        return $this->editReviewableParticipantContent($request, $translator, $participant, 'event_presence');
    }

    /**
     * @Route("/remove/{participant}/", name="participant_remove")
     *
     * @return Response
     */
    public function removeAction(Request $request, Participant $participant, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(ParticipantVoter::PARTICIPANT_EDIT, $participant);

        $form = $this->createForm(RemoveParticipantType::class);
        $form->add('submit', SubmitType::class, ['translation_domain' => 'participant', 'label' => 'remove.submit']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->fastRemove($participant);

            $roleTranslation = ParticipantRole::getTranslationForValue($participant->getRole(), $translator);
            $message = $translator->trans('remove.success.removed', ['%role%' => $roleTranslation], 'participant');
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

        $form = $this->createForm(EditParticipantType::class, $participant, ['required' => false]);
        $form->add('submit', SubmitType::class, ['translation_domain' => 'participant', 'label' => 'edit.submit']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->fastSave($participant);

            $roleTranslation = ParticipantRole::getTranslationForValue($participant->getRole(), $translator);
            $message = $translator->trans('edit.success.edited', ['%role%' => $roleTranslation], 'participant');
            $this->displaySuccess($message);

            return $this->redirectToRoute('delegation_view', ['delegation' => $participant->getDelegation()->getId()]);
        }

        return $this->render('participant/edit.html.twig', ['form' => $form->createView()]);
    }

    private function canRoleBeChosen(int $role, TranslatorInterface $translator, Delegation $delegation, array $participants): bool
    {
        if ($role > 3 || $role < 0) {
            return false;
        }

        $sameRoleCount = 0;

        foreach ($delegation->getParticipants() as $participant) {
            if ($role !== $participant->getRole()) {
                continue;
            }

            ++$sameRoleCount;
        }

        /*
         * leader if only single leader count
         * leader & deputy leader if two leaders
         * contestants if quota not exceeded
         * guest if quota not exceeded
         */
        if (ParticipantRole::LEADER === $role) {
            $result = 0 === $sameRoleCount;
        } elseif (ParticipantRole::DEPUTY_LEADER === $role) {
            $result = 0 === $sameRoleCount && $delegation->getLeaderCount() > 1;
        } elseif (ParticipantRole::CONTESTANT === $role) {
            $result = $delegation->getContestantCount() > $sameRoleCount;
        } else {
            $result = $delegation->getGuestCount() > $sameRoleCount;
        }

        if (!$result) {
            return false;
        }

        return true;
    }
}
