<li class="dropdown-swatches-wrapper" data-type="dropdown">
    <div class="attribute-name">{{ $set->title }}</div>
    <div class="attribute-values">
        <div class="dropdown-swatch">
            <label>
                <select class="form-control" name="attributes[]" multiple>
                    <option value=""></option>
                    @foreach($attributes->where('attribute_set_id', $set->id) as $attribute)
                        <option class="product-filter-item" value="{{ $attribute->id }}" {{ in_array($attribute->id, $selected) ? 'selected' : '' }}>{{ $attribute->title }}</option>
                    @endforeach
                </select>
            </label>
        </div>
    </div>
</li>
