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
use App\Enum\Gender;
use App\Service\Interfaces\FileServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
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

            if ($participant->isLeader()) {
                $form->add('email', EmailType::class, ['required' => false]);
                $form->add('phone', TextType::class, ['required' => false]);
            }

            $this->addFileFields($participant, $form, $this->urlGenerator);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'trait_participant_personal_data',
            'data_class' => Participant::class,
        ]);
    }

    public static function addFileFields(Participant $participant, FormInterface $form, UrlGeneratorInterface $urlGenerator): void
    {
        $defaultOptions = ['required' => false, 'mapped' => false];
        $portraitOptions = $defaultOptions + ['help' => 'portrait_file_help', 'constraints' => self::createFileConstraints('1m', true)];
        if ($participant->getPortrait()) {
            $url = $urlGenerator->generate('participant_download', ['participant' => $participant->getId(), 'filename' => $participant->getPortrait(), 'type' => FileServiceInterface::PORTRAIT]);
            $portraitOptions += ['attr' => ['portrait_url' => $url]];
        }
        $form->add('portraitFile', FileType::class, $portraitOptions);

        $papersOptions = $defaultOptions + ['help' => 'papers_file_help', 'constraints' => self::createFileConstraints('5m', true)];
        if ($participant->getPapers()) {
            $url = $urlGenerator->generate('participant_download', ['participant' => $participant->getId(), 'filename' => $participant->getPapers(), 'type' => FileServiceInterface::PAPERS]);
            $papersOptions += ['attr' => ['papers_url' => $url]];
        }
        $form->add('papersFile', FileType::class, $papersOptions);

        $consentOptions = $defaultOptions + ['help' => 'consent_file_help', 'constraints' => self::createFileConstraints('10m', false)];
        if ($participant->getConsent()) {
            $url = $urlGenerator->generate('participant_download', ['participant' => $participant->getId(), 'filename' => $participant->getConsent(), 'type' => FileServiceInterface::CONSENT]);
            $consentOptions += ['attr' => ['consent_url' => $url]];
        }
        $form->add('consentFile', FileType::class, $consentOptions);
    }

    private static function createFileConstraints(string $fileLimit, bool $isImage): File
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
}
