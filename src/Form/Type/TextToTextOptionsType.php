<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextToTextOptionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('system_prompt', TextareaType::class, [
            'required' => false,
            'disabled' => $options['translation_mode'],
            'label' => 'System message',
        ]);

        $builder->add('llm', ChoiceType::class, [
            'required' => true,
            'disabled' => $options['translation_mode'],
            'label' => 'LLM to use',
            'choices' => [
                'llama3.2:3b' => 'llama3.2:3b',
                'mistral:7b-instruct-v0.2-q4_K_S' => 'mistral:7b-instruct-v0.2-q4_K_S',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'app_ai',
            'translation_mode' => false,
        ]);

        $resolver->setAllowedTypes('translation_mode', 'bool');
    }
}
