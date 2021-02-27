<figure class="text-swatches-wrapper widget-filter-item" data-type="text">
    <h4 class="widget-title">{{ __('By') }} {{ $set->title }}</h4>
    <div class="widget-content">
        <div class="attribute-values">
            <ul class="text-swatch">
                @foreach($attributes->where('attribute_set_id', $set->id) as $attribute)
                    <li data-slug="{{ $attribute->slug }}">
                        <div>
                            <label>
                                <input class="product-filter-item" type="checkbox" name="attributes[]" value="{{ $attribute->id }}" {{ in_array($attribute->id, $selected) ? 'checked' : '' }}>
                                <span>{{ $attribute->title }}</span>
                            </label>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</figure>
