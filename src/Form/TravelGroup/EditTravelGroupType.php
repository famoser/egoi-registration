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
use App\Enum\ArrivalOrDeparture;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class EditTravelGroupType extends AbstractTravelGroupType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('location', TextType::class, ['required' => false]);
        $builder->add('dateTime', TextType::class, ['required' => false]);
        $builder->add('provider', TextType::class, ['required' => false]);
        $builder->add('tripNumber', TextType::class, ['required' => false]);
        $builder->add('description', TextareaType::class, ['required' => false]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var TravelGroup $travelGroup */
            $travelGroup = $event->getData();
            $form = $event->getForm();

            $form->add('participants', EntityType::class, [
                'multiple' => true,
                'class' => Participant::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) use ($travelGroup) {
                    $qb = $er->createQueryBuilder('p')
                        ->orderBy('p.role', 'ASC')
                        ->orderBy('p.givenName', 'ASC');

                    if (ArrivalOrDeparture::ARRIVAL === $travelGroup->getArrivalOrDeparture()) {
                        $qb->where('p.arrivalTravelGroup IS NULL');
                    } else {
                        $qb->where('p.departureTravelGroup IS NULL');
                    }

                    return $qb;
                },
            ]);
        });

        $builder->add('participants', EntityType::class, ['required' => false]);
    }
}
