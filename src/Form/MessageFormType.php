<?php

namespace App\Form;
use App\Entity\User;
use App\Entity\Messages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;

class MessageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject')
            ->add('message',TextareaType::class, [
                'attr' => ['rows' => 5],
            ])
            // ->add('created_at')
            // ->add('is_read')
            // ->add('sender')
        ->add('recipient')
            // -> add('recipient',EntityType::class,[
            //     'class' => User::class,
            //     'choice_label' => 'email',
            //     'multiple' => true,
            //     'expanded' => true
            // ])
            ->add('image', FileType::class, [
                'label' => "Insert an image:  ",
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif', 
                            'image/png', 
                            'image/jpeg', 
                            'image/bmp', 
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid file!',
                    ])
                ]
            ])
            ->add('file', FileType::class, [
                'label' => "Insert a PDF file:  ",
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf', 
                            'application/x-pdf', 
                        ],
                        'mimeTypesMessage' => 'Please upload a valid file!',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Messages::class,
        ]);
    }
}
