<div class="form-group">
    <label class="control-label">Title</label>
    <input type="text" name="title" data-shortcode-attribute="title" class="form-control" placeholder="Title">
</div>

<div class="form-group">
    <label class="control-label">Description</label>
    <input type="text" name="description" data-shortcode-attribute="description" class="form-control" placeholder="Description">
</div>

<div class="form-group">
    <label class="control-label">Screenshot</label>
    {!! Form::mediaImage('screenshot', null, ['data-shortcode-attribute' => 'screenshot']) !!}
</div>

<div class="form-group">
    <label class="control-label">Android app URL</label>
    <input type="text" name="android_app_url" data-shortcode-attribute="android_app_url" class="form-control" placeholder="Android app URL">
</div>

<div class="form-group">
    <label class="control-label">iOS app URL</label>
    <input type="text" name="ios_app_url" data-shortcode-attribute="ios_app_url" class="form-control" placeholder="iOS app URL">
</div>
