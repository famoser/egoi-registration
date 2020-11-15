<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\TravelGroup;

use App\Entity\Participant;
use App\Entity\TravelGroup;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class EditTravelGroupType extends AbstractTravelGroupType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('location', TextType::class, ['required' => false]);
        $builder->add('dateTime', DateTimeType::class, ['required' => false, 'widget' => 'single_text']);
        $builder->add('provider', TextType::class, ['required' => false, 'help' => 'provider_help']);
        $builder->add('tripNumber', TextType::class, ['required' => false, 'help' => 'trip_number_help']);
        $builder->add('description', TextareaType::class, ['required' => false]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var TravelGroup $travelGroup */
            $travelGroup = $event->getData();
            $form = $event->getForm();

            $form->add('participants', EntityType::class, [
                'multiple' => true,
                'by_reference' => false,
                'class' => Participant::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) use ($travelGroup) {
                    /* @var ParticipantRepository $er */
                    return $er->createQueryBuilderForEligibleParticipants($travelGroup);
                },
            ]);
        });
    }
}
