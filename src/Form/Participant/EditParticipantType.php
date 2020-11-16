<?php

/*
 * This file is part of the famoser/egoi-registration project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Participant;

use App\Entity\Participant;
use App\Enum\ParticipantRole;
use App\Form\Traits\EditParticipantEventPresenceType;
use App\Form\Traits\EditParticipantImmigrationType;
use App\Form\Traits\EditParticipantPersonalDataType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EditParticipantType extends AbstractParticipantType
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * EditParticipantType constructor.
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('role', ChoiceType::class, ParticipantRole::getChoicesForBuilder());

        $builder->add('personalData', EditParticipantPersonalDataType::class, ['inherit_data' => true, 'label' => 'trait.name']);
        $builder->add('immigration', EditParticipantImmigrationType::class, ['inherit_data' => true, 'label' => 'trait.name']);
        $builder->add('eventPresence', EditParticipantEventPresenceType::class, ['inherit_data' => true, 'label' => 'trait.name']);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var Participant $participant */
            $participant = $event->getData();
            $form = $event->getForm();

            EditParticipantPersonalDataType::addFileFields($participant, $form->get('personalData'), $this->urlGenerator);
        });
    }
}
