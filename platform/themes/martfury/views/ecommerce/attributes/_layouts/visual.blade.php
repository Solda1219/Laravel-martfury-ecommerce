<div class="visual-swatches-wrapper attribute-swatches-wrapper form-group product__attribute product__color" data-type="visual">
    <label class="attribute-name">{{ $set->title }}</label>
    <div class="attribute-values">
        <ul class="visual-swatch color-swatch attribute-swatch">
            @foreach($attributes->where('attribute_set_id', $set->id) as $attribute)
                <li data-slug="{{ $attribute->slug }}" class="attribute-swatch-item"
                    title="{{ $attribute->title }}">
                    <div class="custom-radio">
                        <label>
                            <input class="form-control product-filter-item" type="radio" name="attribute_{{ $set->slug }}" value="{{ $attribute->id }}" {{ in_array($attribute->id, $selected) ? 'checked' : '' }}>
                            <span style="{{ $attribute->image ? 'background-image: url(' . RvMedia::getImageUrl($attribute->image) . ');' : 'background-color: ' . $attribute->color . ';' }}"></span>
                        </label>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
