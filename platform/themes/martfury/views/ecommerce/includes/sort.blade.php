<select class="ps-select submit-form-on-change" name="sort-by" data-placeholder="{{ __('Sort Items') }}">
    <option value="default_sorting" @if (request()->input('sort-by') == 'default_sorting') selected @endif>{{ __('Default') }}</option>
    <option value="date_asc" @if (request()->input('sort-by') == 'date_asc') selected @endif>{{ __('Oldest') }}</option>
    <option value="date_desc" @if (request()->input('sort-by') == 'date_desc') selected @endif>{{ __('Newest') }}</option>
    <option value="price_asc" @if (request()->input('sort-by') == 'price_asc') selected @endif>{{ __('Price') }}: {{ __('low to high') }}</option>
    <option value="price_desc" @if (request()->input('sort-by') == 'price_desc') selected @endif>{{ __('Price') }}: {{ __('high to low') }}</option>
    <option value="name_asc" @if (request()->input('sort-by') == 'name_asc') selected @endif>{{ __('Name') }}: {{ __('A-Z') }}</option>
    <option value="name_desc" @if (request()->input('sort-by') == 'name_desc') selected @endif>{{ __('Name') }}: {{ __('Z-A') }}</option>
</select>
