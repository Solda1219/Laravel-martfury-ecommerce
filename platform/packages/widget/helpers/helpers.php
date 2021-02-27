<?php

if (!function_exists('register_widget')) {
    /**
     * @param string $widgetId
     */
    function register_widget($widgetId)
    {
        Widget::registerWidget($widgetId);
    }
}

if (!function_exists('register_sidebar')) {
    /**
     * @param array $args
     */
    function register_sidebar($args)
    {
        WidgetGroup::setGroup($args);
    }
}

if (!function_exists('remove_sidebar')) {
    /**
     * @param string $sidebarId
     */
    function remove_sidebar(string $sidebarId)
    {
        WidgetGroup::removeGroup($sidebarId);
    }
}

if (!function_exists('dynamic_sidebar')) {
    /**
     * @param string $sidebarId
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    function dynamic_sidebar(string $sidebarId)
    {
        return WidgetGroup::render($sidebarId);
    }
}
