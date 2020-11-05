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
use App\Form\Traits\EditParticipantPersonalDataType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class EditParticipantType extends AbstractParticipantType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('role', ChoiceType::class, ParticipantRole::getChoicesForBuilder());
        $builder->add('personal', EditParticipantPersonalDataType::class, ['inherit_data' => true]);
    }
}
