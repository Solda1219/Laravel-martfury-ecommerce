<aside class="widget widget_footer">
    <h4 class="widget-title">{{ $config['name'] }}</h4>
    {!!
        Menu::generateMenu(['slug' => $config['menu_id'], 'options' => ['class' => 'ps-list--link']])
    !!}
</aside>
