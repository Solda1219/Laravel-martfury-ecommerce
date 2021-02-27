<figure class="visual-swatches-wrapper widget--colors widget-filter-item" data-type="visual">
    <h4 class="widget-title">{{ __('By') }} {{ $set->title }}</h4>
    <div class="widget__content">
        <div class="attribute-values">
            <ul class="visual-swatch color-swatch">
                @foreach($attributes->where('attribute_set_id', $set->id) as $attribute)
                    <li data-slug="{{ $attribute->slug }}"
                        title="{{ $attribute->title }}">
                        <div class="custom-checkbox">
                            <label>
                                <input class="form-control product-filter-item" type="checkbox" name="attributes[]" value="{{ $attribute->id }}" {{ in_array($attribute->id, $selected) ? 'checked' : '' }}>
                                <span style="{{ $attribute->image ? 'background-image: url(' . asset($attribute->image) . ');' : 'background-color: ' . $attribute->color . ';' }}"></span>
                            </label>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</figure>
