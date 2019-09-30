<?php

namespace App\Form;

use App\Entity\Episode;
use App\Entity\Podcast;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EpisodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
            ])
            ->add('description', TextType::class, [
                'required' => false,
            ])
            ->add('episodeNumber', IntegerType::class, [
                'required' => true,
            ])
            ->add('downloadUrl', UrlType::class, [
                'required' => false,
            ])
            ->add('podcast', EntityType::class, [
                'required' => true,
                'class' => Podcast::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Episode::class,
            'csrf_protection' => false,
        ]);
    }
}
