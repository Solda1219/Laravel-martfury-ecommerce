@for ($i = 1; $i < 5; $i++)
    <div class="form-group">
        <label class="control-label">Ad {{ $i }}</label>
        <select name="key_{{ $i }}" class="form-control" data-shortcode-attribute="key_{{ $i }}">
            <option value="">{{ __('-- select --') }}</option>
            @foreach($ads as $ad)
                <option value="{{ $ad->key }}">{{ $ad->name }}</option>
            @endforeach
        </select>
    </div>
@endfor
