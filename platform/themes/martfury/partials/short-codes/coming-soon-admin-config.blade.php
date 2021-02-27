<div class="form-group">
    <label class="control-label">Time</label>
    <input type="text" name="time" data-shortcode-attribute="time" class="form-control" placeholder="Time">
</div>

<div class="form-group">
    <label class="control-label">Image</label>
    {!! Form::mediaImage('image', null, ['data-shortcode-attribute' => 'image']) !!}
</div>
