<?php

namespace Botble\Newsletter\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Newsletter\Enums\NewsletterStatusEnum;
use Botble\Newsletter\Events\SubscribeNewsletterEvent;
use Botble\Newsletter\Http\Requests\NewsletterRequest;
use Botble\Newsletter\Repositories\Interfaces\NewsletterInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Newsletter;
use SendGrid;
use URL;

class PublicController extends Controller
{
    /**
     * @var NewsletterInterface
     */
    protected $newsletterRepository;

    /**
     * PublicController constructor.
     * @param NewsletterInterface $newsletterRepository
     */
    public function __construct(NewsletterInterface $newsletterRepository)
    {
        $this->newsletterRepository = $newsletterRepository;
    }

    /**
     * @param NewsletterRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postSubscribe(NewsletterRequest $request, BaseHttpResponse $response)
    {
        $newsletter = $this->newsletterRepository->getFirstBy(['email' => $request->input('email')]);
        if (!$newsletter) {
            $newsletter = $this->newsletterRepository->createOrUpdate($request->input());
            if (config('plugins.newsletter.general.mailchimp.api_key') && config('plugins.newsletter.general.mailchimp.list_id')) {
                Newsletter::subscribe($newsletter->email);
            }

            if (config('plugins.newsletter.general.sendgrid.api_key') && config('plugins.newsletter.general.sendgrid.list_id')) {
                $sg = new SendGrid(config('plugins.newsletter.general.sendgrid.api_key'));

                $requestBody = json_decode(
                    '{
                        "list_ids": [
                            "' . config('plugins.newsletter.general.sendgrid.list_id') . '"
                        ],
                        "contacts": [
                            {
                                "first_name": "' . $request->input('first_name') . '",
                                "last_name": "' . $request->input('last_name') . '",
                                "email": "' . $request->input('email') . '"
                            }
                        ]
                    }'
                );

                try {
                    $sg->client->marketing()->contacts()->put($requestBody);
                } catch (Exception $exception) {
                    info('Caught exception: ' . $exception->getMessage());
                }
            }
        }

        event(new SubscribeNewsletterEvent($newsletter));

        return $response->setMessage(__('Subscribe to newsletter successfully!'));
    }

    /**
     * Unsubscribe newsletter with token. change status to false
     * @param string $email
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getUnsubscribe($email, Request $request, BaseHttpResponse $response)
    {
        if (!URL::hasValidSignature($request)) {
            abort(404);
        }

        $newsletter = $this->newsletterRepository->getFirstBy([
            'email'  => $email,
            'status' => NewsletterStatusEnum::SUBSCRIBED,
        ]);

        if ($newsletter) {
            $newsletter->status = NewsletterStatusEnum::UNSUBSCRIBED;
            $this->newsletterRepository->createOrUpdate($newsletter);

            if (config('plugins.newsletter.general.mailchimp.api_key') && config('plugins.newsletter.general.mailchimp.list_id')) {
                Newsletter::unsubscribe($newsletter->email);
            }

            return $response
                ->setNextUrl(url('/'))
                ->setMessage(__('Unsubscribe to newsletter successfully'));
        }

        return $response
            ->setError()
            ->setNextUrl(url('/'))
            ->setMessage(__('Your email does not exist in the system or you have unsubscribed already!'));
    }
}
