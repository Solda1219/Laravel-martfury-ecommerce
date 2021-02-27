<?php

namespace Botble\Base\Events;

use Eloquent;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class BeforeEditContentEvent extends Event
{
    use SerializesModels;

    /**
     * @var Request
     */
    public $request;

    /**
     * @var Eloquent|false
     */
    public $data;

    /**
     * CreatedContentEvent constructor.
     * @param Request $request
     * @param Eloquent|false $data
     */
    public function __construct($request, $data)
    {
        $this->request = $request;
        $this->data = $data;
    }
}
