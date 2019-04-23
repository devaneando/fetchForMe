<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType as SymfonyAbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class SortType extends SymfonyAbstractType
{
    /** @var TranslatorInterface $translator */
    private $translator;

    const SORT_PRICE = 'price';
    const SORT_PROXIMITY = 'proximity';

    /** @required */
    public function setTranslator(TranslatorInterface $translator): self
    {
        $this->translator = $translator;

        return $this;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                'sort.none' => null,
                'sort.price' => self::SORT_PRICE,
                'sort.proximity' => self::SORT_PROXIMITY,
            ],
            'choices_as_values' => true,
            'choice_translation_domain' => 'messages',
            'data' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sort_type';
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
