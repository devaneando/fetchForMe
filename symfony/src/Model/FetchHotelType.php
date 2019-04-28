<?php

namespace App\Model;

use App\Form\SortType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FetchHotelType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('latitude', NumberType::class)
            ->add('longitude', NumberType::class)
            ->add('kilometers', IntegerType::class)
            ->add('sortBy', SortType::class);
    }
}
