<figure class="dropdown-swatches-wrapper widget-filter-item">
    <h4 class="widget-title">{{ __('By') }} {{ $set->title }}</h4>
    <div class="widget-content">
        <div class="attribute-values">
            <div class="dropdown-swatch">
                <label>
                    <select class="form-control" name="attributes[]">
                        @foreach($attributes->where('attribute_set_id', $set->id) as $attribute)
                            <option class="product-filter-item" value="{{ $attribute->id }}" {{ in_array($attribute->id, $selected) ? 'selected' : '' }}>{{ $attribute->title }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </div>
    </div>
</figure>
