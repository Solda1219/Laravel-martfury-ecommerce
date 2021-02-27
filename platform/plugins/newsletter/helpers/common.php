<?php

if (!function_exists('render_newsletter_subscribe_form')) {
    /**
     * @return string
     * @throws Throwable
     */
    function render_newsletter_subscribe_form(array $hiddenFields = [])
    {
        return view('plugins/newsletter::partials.form', compact('hiddenFields'))->render();
    }
}
