<div id="product-variations-wrapper">
    <div class="variation-actions">
        <a href="#" class="btn-trigger-select-product-attributes" data-target="{{ route('products.store-related-attributes', $product->id) }}">{{ trans('plugins/ecommerce::products.edit_attribute') }}</a>
        <a href="#" class="btn-trigger-add-new-product-variation" data-target="{{ route('products.add-version', $product->id) }}">{{ trans('plugins/ecommerce::products.add_new_variation') }}</a>
        <a href="#" class="btn-trigger-generate-all-versions" data-target="{{ route('products.generate-all-versions', $product->id) }}">{{ trans('plugins/ecommerce::products.generate_all_variations') }}</a>
    </div>
    @if (!$productVariations->isEmpty())
        <table class="table table-hover-variants">
            <thead>
            <tr>
                <th>{{ trans('plugins/ecommerce::products.form.image') }}</th>
                @foreach ($productAttributeSets->where('is_selected', '<>', null)->whereIn('id', $productVariationsInfo->pluck('attribute_set_id')->all())->sortBy('id') as $attributeSet)
                    <th>{{ $attributeSet->title }}</th>
                @endforeach
                @foreach ($productAttributeSets->where('is_selected', '<>', null)->whereNotIn('id', $productVariationsInfo->pluck('attribute_set_id')->all())->sortBy('id') as $attributeSet)
                    <th>{{ $attributeSet->title }}</th>
                @endforeach
                <th>{{ trans('plugins/ecommerce::products.form.price') }}</th>
                <th>{{ trans('plugins/ecommerce::products.form.is_default') }}</th>
                <th class="text-center">{{ trans('plugins/ecommerce::products.form.action') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($productVariations as $variation)
                @php
                    $currentRelatedProduct = $productsRelatedToVariation->where('variation_id', $variation->id)->first();
                @endphp
                <tr>
                    <td>
                        <div class="wrap-img-product">
                            <img src="{{ RvMedia::getImageUrl($currentRelatedProduct && $currentRelatedProduct->image ? $currentRelatedProduct->image : $product->image, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ trans('plugins/ecommerce::products.form.image')  }}">
                        </div>
                    </td>
                    @foreach ($productVariationsInfo->where('variation_id', $variation->id)->sortBy('attribute_set_id') as $key => $item)
                        <td>{{ $item->title }}</td>
                    @endforeach
                    @for($index = 0; $index < ($productAttributeSets->where('is_selected', '<>', null)->count() - $productVariationsInfo->where('variation_id', $variation->id)->count()); $index++)
                        <td>--</td>
                    @endfor
                    <td>
                        @if ($currentRelatedProduct)
                            {{ format_price($currentRelatedProduct->front_sale_price) }}
                            @if ($currentRelatedProduct->front_sale_price != $currentRelatedProduct->price)
                                <del class="text-danger">{{ format_price($currentRelatedProduct->price) }}</del>
                            @endif
                        @else
                            {{ format_price($product->front_sale_price) }}
                            @if ($product->front_sale_price != $product->price)
                                <del class="text-danger">{{ format_price($product->price) }}</del>
                            @endif
                        @endif
                    </td>
                    <td>
                        <label>
                            <input type="radio" class="hrv-radio"
                                   {{ $variation->is_default ? 'checked' : '' }}
                                   name="variation_default_id"
                                   value="{{ $variation->id }}">
                        </label>
                    </td>
                    <td style="width: 180px;" class="text-center">
                        <a href="#" class="btn btn-info btn-trigger-edit-product-version"
                                data-target="{{ route('products.update-version', [$variation->id]) }}"
                                data-load-form="{{ route('products.get-version-form', [$variation->id]) }}"
                        >{{ trans('plugins/ecommerce::products.edit_variation_item') }}</a>
                        <a href="#" data-target="{{ route('products.delete-version', [$variation->id]) }}"
                           class="btn-trigger-delete-version btn btn-danger">{{ trans('plugins/ecommerce::products.delete') }}</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <p>{{ trans('plugins/ecommerce::products.variations_box_description') }}</p>
    @endif
    {!! Form::modalAction('select-attribute-sets-modal', trans('plugins/ecommerce::products.select_attribute'), 'info', view('plugins/ecommerce::products.partials.attribute-sets', compact('productAttributeSets'))->render(), 'store-related-attributes-button', trans('plugins/ecommerce::products.save_changes')) !!}
    {!! Form::modalAction('add-new-product-variation-modal', trans('plugins/ecommerce::products.add_new_variation'), 'info', view('plugins/ecommerce::products.partials.product-variation-form', ['productAttributeSets' => $productAttributeSets, 'productAttributes' => $productAttributes, 'product' => null, 'originalProduct' => $product, 'productVariationsInfo' => null])->render(), 'store-product-variation-button', trans('plugins/ecommerce::products.save_changes')) !!}
    {!! Form::modalAction('edit-product-variation-modal', trans('plugins/ecommerce::products.edit_variation'), 'info', view('plugins/ecommerce::products.partials.product-variation-form', ['productAttributeSets' => $productAttributeSets, 'productAttributes' => $productAttributes, 'product' => null, 'originalProduct' => $product, 'productVariationsInfo' => null])->render(), 'update-product-variation-button', trans('plugins/ecommerce::products.save_changes')) !!}
    {!! Form::modalAction('generate-all-versions-modal', trans('plugins/ecommerce::products.generate_all_variations'), 'info', trans('plugins/ecommerce::products.generate_all_variations_confirmation'), 'generate-all-versions-button', trans('plugins/ecommerce::products.continue')) !!}
    {!! Form::modalAction('confirm-delete-version-modal', trans('plugins/ecommerce::products.delete_variation'), 'danger', trans('plugins/ecommerce::products.delete_variation_confirmation'), 'delete-version-button', trans('plugins/ecommerce::products.continue')) !!}
</div>
