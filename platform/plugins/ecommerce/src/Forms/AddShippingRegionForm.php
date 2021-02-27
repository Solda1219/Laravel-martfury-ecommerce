<?php

namespace Botble\Ecommerce\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Ecommerce\Http\Requests\AddShippingRegionRequest;
use Botble\Ecommerce\Models\Shipping;

class AddShippingRegionForm extends FormAbstract
{
    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $this
            ->setupModel(new Shipping)
            ->setFormOption('template', 'core/base::forms.form-modal')
            ->setFormOption('class', 'form-sm')
            ->setTitle(trans('plugins/ecommerce::shipping.add_shipping_region'))
            ->setValidatorClass(AddShippingRegionRequest::class)
            ->add('region', 'select', [
                'label'      => trans('plugins/ecommerce::shipping.country'),
                'label_attr' => [
                    'class' => 'control-label required',
                ],
                'choices'    => [],
            ])
            ->add('close', 'button', [
                'label' => trans('core/base::forms.cancel'),
                'attr'  => [
                    'class'               => 'btn btn-warning',
                    'data-fancybox-close' => true,
                ],
            ])
            ->add('submit', 'submit', [
                'label' => trans('core/base::forms.save'),
                'attr'  => [
                    'class' => 'btn btn-info float-right',
                ],
            ]);
    }
}
