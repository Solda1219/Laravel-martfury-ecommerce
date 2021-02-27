<?php

namespace Botble\Faq;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Schema;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        Schema::dropIfExists('faq_categories');
        Schema::dropIfExists('faqs');
    }
}
