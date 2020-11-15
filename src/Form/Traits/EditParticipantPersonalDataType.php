<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Traits;

use App\Entity\Participant;
use App\Enum\ParticipantRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditParticipantPersonalDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('givenName', TextType::class, ['required' => false]);
        $builder->add('familyName', TextType::class, ['required' => false]);
        $builder->add('birthday', DateType::class, ['widget' => 'single_text', 'required' => false]);
        $builder->add('gender', TextType::class, ['required' => false]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var Participant $participant */
            $participant = $event->getData();
            $form = $event->getForm();

            if (ParticipantRole::LEADER === $participant->getRole() || ParticipantRole::DEPUTY_LEADER === $participant->getRole()) {
                $form->add('email', EmailType::class, ['required' => false]);
                $form->add('phone', TextType::class, ['required' => false]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'trait_participant_personal_data',
            'data_class' => Participant::class,
        ]);
    }
}
