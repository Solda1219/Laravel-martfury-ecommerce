<div class="text-swatches-wrapper attribute-swatches-wrapper attribute-swatches-wrapper form-group product__attribute product__color" data-type="text">
    <label class="attribute-name">{{ $set->title }}</label>
    <div class="attribute-values">
        <ul class="text-swatch attribute-swatch color-swatch">
            @foreach($attributes->where('attribute_set_id', $set->id) as $attribute)
                <li data-slug="{{ $attribute->slug }}" class="attribute-swatch-item">
                    <div>
                        <label>
                            <input class="product-filter-item" type="radio" name="attribute_{{ $set->slug }}" value="{{ $attribute->id }}" {{ in_array($attribute->id, $selected) ? 'checked' : '' }}>
                            <span>{{ $attribute->title }}</span>
                        </label>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
