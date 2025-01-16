<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\AI\Handler\OllamaTextWithContextToTextActionHandler;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class AnswerQuestionWithContextOptionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('system_prompt', TextareaType::class, [
            'required' => true,
            'disabled' => $options['translation_mode'],
            'label' => 'System message',
            'help' => 'must contain a single Context tag ' . OllamaTextWithContextToTextActionHandler::CONTEXT_PATTERN,
            'empty_data' => "Use the following pieces of context to answer the question of the user. If you don't know the answer, just say that you don't know, don't try to make up an answer.\n\n{context}.",
            'constraints' => [
                new Regex('/' . OllamaTextWithContextToTextActionHandler::CONTEXT_PATTERN . '/'),
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
