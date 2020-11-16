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
use App\Enum\Gender;
use App\Service\Interfaces\FileServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\File;

class EditParticipantPersonalDataType extends AbstractType
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * EditParticipantPersonalDataType constructor.
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('givenName', TextType::class, ['required' => false]);
        $builder->add('familyName', TextType::class, ['required' => false]);
        $builder->add('birthday', DateType::class, ['widget' => 'single_text', 'required' => false]);
        $builder->add('gender', ChoiceType::class, Gender::getChoicesForBuilder() + ['required' => false]);

        $builder->add('nameOnDocuments', TextType::class, ['required' => false, 'help' => 'name_on_documents_help']);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var Participant $participant */
            $participant = $event->getData();
            $form = $event->getForm();

            $defaultOptions = ['required' => false, 'mapped' => false];
            $portraitOptions = $defaultOptions + ['help' => 'portrait_file_help', 'constraints' => $this->createFileConstraints('1m', true)];
            if ($participant->getPortrait()) {
                $url = $this->urlGenerator->generate('participant_image', ['participant' => $participant->getId(), 'filename' => $participant->getPortrait(), 'type' => FileServiceInterface::PORTRAIT]);
                $portraitOptions += ['attr' => ['portrait_url' => $url]];
            }
            $form->add('portraitFile', FileType::class, $portraitOptions);

            $papersOptions = $defaultOptions + ['help' => 'papers_file_help', 'constraints' => $this->createFileConstraints('5m', true)];
            $form->add('papersFile', FileType::class, $papersOptions);

            $consentOptions = $defaultOptions + ['help' => 'consent_file_help', 'constraints' => $this->createFileConstraints('10m', false)];
            $form->add('consentFile', FileType::class, $consentOptions);
        });
    }

    private function createFileConstraints(string $fileLimit, bool $isImage)
    {
        $mimeTypes = [
            'application/pdf',
            'application/x-pdf',
        ];

        if ($isImage) {
            $mimeTypes = [
                'image/jpeg',
                'image/png',
                'image/gif',
            ];
        }

        return new File([
            'maxSize' => $fileLimit,
            'mimeTypes' => $mimeTypes,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'trait_participant_personal_data',
            'data_class' => Participant::class,
        ]);
    }
}
