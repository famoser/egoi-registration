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

use App\Entity\Delegation;
use App\Enum\ParticipationMode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditDelegationAttendanceType extends AbstractType
{
    /**
     * @var string
     */
    private $guestSurcharge;

    /**
     * EditDelegationAttendanceType constructor.
     */
    public function __construct(string $guestSurcharge)
    {
        $this->guestSurcharge = $guestSurcharge;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('contestantCount', IntegerType::class, ['attr' => ['min' => 1, 'max' => 4]]);
        $builder->add('leaderCount', IntegerType::class, ['attr' => ['min' => 1, 'max' => 2]]);

        $builder->add('guestCount', IntegerType::class, ['attr' => ['min' => 0, 'max' => 5], 'help' => 'guest_count_help', 'help_translation_parameters' => ['%surcharge%' => $this->guestSurcharge]]);
        $builder->add('participationMode', ChoiceType::class, ParticipationMode::getChoicesForBuilder());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'trait_delegation_attendance',
            'data_class' => Delegation::class,
        ]);
    }
}
