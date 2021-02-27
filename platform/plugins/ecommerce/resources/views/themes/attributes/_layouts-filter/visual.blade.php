<li class="visual-swatches-wrapper" data-type="visual">
    <h6 class="widget-title">{{ $set->title }}</h6>
    <div class="attribute-values">
        <ul class="visual-swatch">
            @foreach($attributes->where('attribute_set_id', $set->id) as $attribute)
                <li data-slug="{{ $attribute->slug }}"
                    data-toggle="tooltip"
                    data-placement="top"
                    title="{{ $attribute->title }}">
                    <div class="custom-checkbox">
                        <label>
                            <input class="product-filter-item" type="checkbox" name="attributes[]" value="{{ $attribute->id }}" {{ in_array($attribute->id, $selected) ? 'checked' : '' }}>
                            <span style="{{ $attribute->image ? 'background-image: url(' . asset($attribute->image) . ');' : 'background-color: ' . $attribute->color . ';' }}"></span>
                        </label>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</li>
