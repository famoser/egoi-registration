<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Traits;

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use App\Entity\Base\BaseEntity;
use App\Entity\Delegation;
use App\Entity\Participant;
use App\Enum\ReviewProgress;
use App\Security\Voter\DelegationVoter;
use App\Security\Voter\ParticipantVoter;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

trait ReviewableContentEditTrait
{
    abstract protected function denyAccessUnlessGranted($attribute, $subject = null, string $message = 'Access Denied.'): void;

    abstract protected function createForm(string $type, $data = null, array $options = []): FormInterface;

    abstract protected function fastSave(...$entities);

    abstract protected function displaySuccess($message, $link = null);

    abstract protected function redirectToRoute(string $route, array $parameters = [], int $status = 302): RedirectResponse;

    abstract protected function render(string $view, array $parameters = [], Response $response = null): Response;

    /**
     * assumes that $editablePart follows some conventions, then generates & processes form.
     */
    private function editReviewableDelegationContent(Request $request, TranslatorInterface $translator, Delegation $delegation, string $editablePart, ?callable $validation = null)
    {
        $this->denyAccessUnlessGranted(DelegationVoter::DELEGATION_EDIT, $delegation);

        return $this->editReviewableContentBase($request, $translator, $delegation, $delegation, 'delegation', $editablePart, $validation);
    }

    /**
     * assumes that $editablePart follows some conventions, then generates & processes form.
     */
    private function editReviewableParticipantContent(Request $request, TranslatorInterface $translator, Participant $participant, string $editablePart, ?callable $validation = null)
    {
        $this->denyAccessUnlessGranted(ParticipantVoter::PARTICIPANT_EDIT, $participant);

        return $this->editReviewableContentBase($request, $translator, $participant->getDelegation(), $participant, 'participant', $editablePart, $validation);
    }

    /**
     * assumes that $editablePart follows some conventions, then generates & processes form.
     */
    private function editReviewableContentBase(Request $request, TranslatorInterface $translator, Delegation $delegation, BaseEntity $entity, string $collection, string $editablePart, ?callable $validation): Response
    {
        // normalizers
        $editablePart = strtolower($editablePart);
        $templatePrefix = 'edit_'.$editablePart;
        $editablePartPascalCase = str_replace('_', '', ucwords($editablePart, '_'));
        $collection = strtolower($collection);
        $collectionFirstCharacterUppercase = strtoupper(substr($collection, 0, 1)).substr($collection, 1);

        // CONVENTIONS TO FOLLOW
        $getter = 'get'.$editablePartPascalCase.'ReviewProgress';
        $setter = 'set'.$editablePartPascalCase.'ReviewProgress';
        $formType = 'App\Form\Traits\Edit'.$collectionFirstCharacterUppercase.$editablePartPascalCase.'Type';
        // end CONVENTIONS TO FOLLOW

        $readOnly = ReviewProgress::REVIEWED_AND_LOCKED === $entity->$getter();
        $form = $this->createForm($formType, $entity, ['disabled' => $readOnly]);
        $form->add('submit', SubmitType::class, ['translation_domain' => $collection, 'label' => $templatePrefix.'.submit']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && !$readOnly) {
            $entity->$setter(ReviewProgress::EDITED);

            if (is_callable($validation) && $validation($form)) {
                $this->fastSave($entity);

                $message = $translator->trans($templatePrefix.'.success.saved', [], $collection);
                $this->displaySuccess($message);

                return $this->redirectToRoute('delegation_view', ['delegation' => $delegation->getId()]);
            }
        }

        return $this->render($collection.'/'.$templatePrefix.'.html.twig', ['form' => $form->createView(), 'readonly' => $readOnly]);
    }
}
