<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FunctionAwareTextToTextOptionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('system_prompt', TextareaType::class, [
            'required' => true,
            'disabled' => $options['translation_mode'],
            'label' => 'System message',
        ]);
        $builder->add('llm', ChoiceType::class, [
            'required' => true,
            'disabled' => $options['translation_mode'],
            'label' => 'LLM to use',
            'choices' => [
                'llama3.2:3b' => 'llama3.2:3b',
            ],
        ]);
        $builder->add('name', TextType::class, [
            'required' => true,
            'disabled' => $options['translation_mode'],
            'label' => 'Function name',
        ]);
        $builder->add('description', TextareaType::class, [
            'required' => true,
            'disabled' => $options['translation_mode'],
            'label' => 'Function description',
        ]);
        $builder->add('parameter_name', TextType::class, [
            'required' => true,
            'disabled' => $options['translation_mode'],
            'label' => 'Function parameter name',
        ]);
        $builder->add('parameter_description', TextareaType::class, [
            'required' => true,
            'disabled' => $options['translation_mode'],
            'label' => 'Function parameter description',
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
