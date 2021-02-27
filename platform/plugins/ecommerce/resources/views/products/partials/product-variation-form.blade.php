<div class="variation-form-wrapper">
    <div class="row">
        @foreach ($productAttributeSets as $attributeSet)
            @if ($attributeSet->is_selected)
                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <label for="attribute-{{ $attributeSet->slug }}" class="text-title-field required">{{ $attributeSet->title }}</label>
                        <div class="ui-select-wrapper">
                            <select class="ui-select" id="attribute-{{ $attributeSet->slug }}" name="attribute_sets[{{ $attributeSet->id }}]">
                                @foreach ($productAttributes->where('attribute_set_id', $attributeSet->id) as $attribute)
                                    <option value="{{ $attribute->id }}" @if ($productVariationsInfo && $productVariationsInfo->where('attribute_set_id', $attributeSet->id)->where('id', $attribute->id)->first()) selected @endif>
                                        {{ $attribute->title }}
                                    </option>
                                @endforeach
                            </select>
                            <svg class="svg-next-icon svg-next-icon-size-16">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    @include('plugins/ecommerce::products.partials.general', compact('product', 'originalProduct'))
    <div class="variation-images">
        @include('core/base::forms.partials.images', ['name' => 'images[]', 'values' => isset($product) ? $product->images : []])
    </div>
</div>

@once
    <script id="gallery_select_image_template" type="text/x-custom-template">
        <div class="list-photo-hover-overlay">
            <ul class="photo-overlay-actions">
                <li>
                    <a class="mr10 btn-trigger-edit-gallery-image" data-toggle="tooltip" data-placement="bottom"
                       data-original-title="{{ trans('core/base::base.change_image') }}">
                        <i class="fa fa-edit"></i>
                    </a>
                </li>
                <li>
                    <a class="mr10 btn-trigger-remove-gallery-image" data-toggle="tooltip" data-placement="bottom"
                       data-original-title="{{ trans('core/base::base.delete_image') }}">
                        <i class="fa fa-trash"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="custom-image-box image-box">
            <input type="hidden" name="__name__" class="image-data">
            <img src="{{ RvMedia::getDefaultImage(false) }}" alt="{{ trans('core/base::base.preview_image') }}" class="preview_image">
            <div class="image-box-actions">
                <a class="btn-images" data-result="images[]" data-action="select-image">
                    {{ trans('core/base::forms.choose_image') }}
                </a> |
                <a class="btn_remove_image">
                    <span></span>
                </a>
            </div>
        </div>
    </script>
@endonce
