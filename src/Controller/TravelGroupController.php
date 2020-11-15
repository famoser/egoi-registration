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
use App\Entity\TravelGroup;
use App\Enum\ArrivalOrDeparture;
use App\Security\Voter\DelegationVoter;
use App\Service\Interfaces\ExportServiceInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/travel_group")
 */
class TravelGroupController extends BaseDoctrineController
{
    /**
     * @Route("/new/{delegation}", name="travel_group_new")
     *
     * @return Response
     */
    public function newAction(Request $request, Delegation $delegation, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(DelegationVoter::DELEGATION_EDIT, $delegation);

        $travelGroup = new TravelGroup();
        $travelGroup->setDelegation($delegation);

        $arrivalOrDeparture = (int) $request->query->get('arrivalOrDeparture', ArrivalOrDeparture::ARRIVAL);
        $validArrivalOrDeparture = min(1, max($arrivalOrDeparture, 0));
        $travelGroup->setArrivalOrDeparture($validArrivalOrDeparture);

        $form = $this->createForm(AddTravelGroupType::class, $travelGroup);
        $form->add('submit', SubmitType::class, ['translation_domain' => 'travelGroup', 'label' => 'new.submit']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->canRoleBeChosen($delegation, $travelGroup)) {
                $message = $translator->trans('new.error.role_exceeded', [], 'travelGroup');
                $this->displayError($message);
            } else {
                $this->fastSave($travelGroup);

                $message = $translator->trans('new.success.created', [], 'travelGroup');
                $this->displaySuccess($message);

                return $this->redirectToRoute('delegation_view', ['delegation' => $delegation->getId()]);
            }
        }

        return $this->render('travelGroup/new.html.twig', ['form' => $form->createView()]);
    }

    use ReviewableContentEditTrait;

    /**
     * @Route("/edit_personal_data/{travelGroup}", name="travelGroup_edit_personal_data")
     *
     * @return Response
     */
    public function editPersonalDataAction(Request $request, TravelGroup $travelGroup, TranslatorInterface $translator)
    {
        return $this->editReviewableTravelGroupContent($request, $translator, $travelGroup, 'personal_data');
    }

    /**
     * @Route("/edit_immigration/{travelGroup}", name="travelGroup_edit_immigration")
     *
     * @return Response
     */
    public function editImmigrationAction(Request $request, TravelGroup $travelGroup, TranslatorInterface $translator)
    {
        return $this->editReviewableTravelGroupContent($request, $translator, $travelGroup, 'immigration');
    }

    /**
     * @Route("/edit_event_presence/{travelGroup}", name="travelGroup_edit_event_presence")
     *
     * @return Response
     */
    public function editEventPresenceAction(Request $request, TravelGroup $travelGroup, TranslatorInterface $translator)
    {
        return $this->editReviewableTravelGroupContent($request, $translator, $travelGroup, 'event_presence');
    }

    /**
     * @Route("/remove/{travelGroup}/", name="travelGroup_remove")
     *
     * @return Response
     */
    public function removeAction(Request $request, TravelGroup $travelGroup, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(TravelGroupVoter::TravelGroup_EDIT, $travelGroup);

        $form = $this->createForm(RemoveTravelGroupType::class);
        $form->add('submit', SubmitType::class, ['translation_domain' => 'travelGroup', 'label' => 'remove.submit']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->fastRemove($travelGroup);

            $message = $translator->trans('remove.success.removed', [], 'travelGroup');
            $this->displaySuccess($message);

            return $this->redirectToRoute('delegation_view', ['delegation' => $travelGroup->getDelegation()->getId()]);
        }

        return $this->render('travelGroup/remove.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/export", name="travelGroup_export")
     *
     * @return Response
     */
    public function exportAction(ExportServiceInterface $exportService)
    {
        $this->denyAccessUnlessGranted(TravelGroupVoter::TravelGroup_MODERATE);

        $travelGroups = $this->getDoctrine()->getRepository(TravelGroup::class)->findBy([], ['familyName' => 'ASC']);

        return $exportService->exportToCsv($travelGroups, 'travelGroup-export', 'travelGroups');
    }

    /**
     * @Route("/edit/{travelGroup}", name="travelGroup_edit")
     *
     * @return Response
     */
    public function editAction(Request $request, TravelGroup $travelGroup, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(TravelGroupVoter::TravelGroup_MODERATE, $travelGroup);

        $form = $this->createForm(EditTravelGroupType::class, $travelGroup, ['required' => false]);
        $form->add('submit', SubmitType::class, ['translation_domain' => 'travelGroup', 'label' => 'edit.submit']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->canRoleBeChosen($travelGroup)) {
                $message = $translator->trans('new.error.role_exceeded', [], 'travelGroup');
                $this->displaySuccess($message);
            } else {
                $this->fastSave($travelGroup);

                $message = $translator->trans('edit.success.edited', [], 'travelGroup');
                $this->displaySuccess($message);

                return $this->redirectToRoute('delegation_view', ['delegation' => $travelGroup->getDelegation()->getId()]);
            }
        }

        return $this->render('travelGroup/edit.html.twig', ['form' => $form->createView()]);
    }

    private function canRoleBeChosen(Delegation $delegation, TravelGroup $changedRoleTravelGroup): bool
    {
        $sameRoleCount = 0;

        foreach ($delegation->getTravelGroups() as $travelGroup) {
            if ($travelGroup === $changedRoleTravelGroup || $travelGroup->getRole() !== $changedRoleTravelGroup->getRole()) {
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
        if (TravelGroupRole::LEADER === $changedRoleTravelGroup->getRole()) {
            return 0 === $sameRoleCount;
        } elseif (TravelGroupRole::DEPUTY_LEADER === $changedRoleTravelGroup->getRole()) {
            return 0 === $sameRoleCount && $changedRoleTravelGroup->getDelegation()->getLeaderCount() > 1;
        } elseif (TravelGroupRole::CONTESTANT === $changedRoleTravelGroup->getRole()) {
            return $changedRoleTravelGroup->getDelegation()->getContestantCount() > $sameRoleCount;
        } else {
            return $changedRoleTravelGroup->getDelegation()->getGuestCount() > $sameRoleCount;
        }
    }
}
