<?php

/*
 * This file is part of the famoser/egoi-registration project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Traits;

use App\Entity\Participant;
use App\Enum\Diet;
use App\Enum\ShirtFit;
use App\Enum\ShirtSize;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditParticipantEventPresenceType extends AbstractType
{
    /**
     * @var string
     */
    private $singleRoomSurcharge;

    /**
     * @var string
     */
    private $currency;

    /**
     * EditParticipantEventPresenceType constructor.
     */
    public function __construct(string $singleRoomSurcharge, string $currency)
    {
        $this->singleRoomSurcharge = $singleRoomSurcharge;
        $this->currency = $currency;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('shirtSize', ChoiceType::class, ShirtSize::getChoicesForBuilder() + ['required' => false]);
        $builder->add('shirtFit', ChoiceType::class, ShirtFit::getChoicesForBuilder() + ['required' => false]);

        $builder->add('diet', ChoiceType::class, Diet::getChoicesForBuilder() + ['required' => false]);
        $builder->add('allergies', TextareaType::class, ['required' => false]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var Participant $participant */
            $participant = $event->getData();
            $form = $event->getForm();

            if ($participant->isLeader()) {
                $form->add('singleRoom', CheckboxType::class, ['required' => false, 'help' => 'single_room_help', 'help_translation_parameters' => ['%surcharge%' => $this->singleRoomSurcharge, '%currency%' => $this->currency]]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'trait_participant_event_presence',
            'data_class' => Participant::class,
        ]);
    }
}
