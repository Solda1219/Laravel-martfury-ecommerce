<div class="form-group">
    <label class="control-label">Title</label>
    <input type="text" name="title" data-shortcode-attribute="title" class="form-control" placeholder="Title">
</div>

<div class="form-group">
    <label class="control-label">{{ __('Select a flash sale') }}</label>
    <select name="flash_sale_id" class="form-control" data-shortcode-attribute="flash_sale_id">
        @foreach($flashSales as $flashSale)
            <option value="{{ $flashSale->id }}">{{ $flashSale->name }}</option>
        @endforeach
    </select>
</div>
