<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Participant;

use App\Enum\ParticipantRole;
use App\Form\Traits\EditParticipantEventPresenceType;
use App\Form\Traits\EditParticipantImmigrationType;
use App\Form\Traits\EditParticipantPersonalDataType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class EditParticipantType extends AbstractParticipantType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('role', ChoiceType::class, ParticipantRole::getChoicesForBuilder());

        $builder->add('personalData', EditParticipantPersonalDataType::class, ['inherit_data' => true, 'label' => 'trait.name']);
        $builder->add('immigration', EditParticipantImmigrationType::class, ['inherit_data' => true, 'label' => 'trait.name']);
        $builder->add('eventPresence', EditParticipantEventPresenceType::class, ['inherit_data' => true, 'label' => 'trait.name']);
    }
}
