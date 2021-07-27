<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 26/07/2021
 * Time: 20:27
 */

namespace App\Services;


use App\Models\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    public function register(string $event, string $url, $resourceToken = null)
    {
        if(!$resourceToken) {
            $resourceToken = env('JUNO__PRIVATE_TOKEN');
        }
        $webhooksService = new \App\Integrations\Juno\Services\WebhookService([], $resourceToken);
        $webhookResponse = $webhooksService->register([
            'url' => $url,
            'eventTypes' => [
                $event,
            ]
        ]);

        $webhook = null;
        if(isset($webhookResponse->secret)) {
            $webhook = new Webhook();
            $webhook->event = $event;
            $webhook->status = 'ACTIVE';
            $webhook->secret = $webhookResponse->secret;
            $webhook->url = $webhookResponse->url;
            $webhook->save();

            Log::info('juno.notifications.' . $event . ' - Was successfully registered.', ['url' => $webhook->url]);
        }

        return $webhook;
    }

    public function read(Request $request, string $event)
    {
        $eventTitle = 'notifications.juno.' . $event;
        Log::info($eventTitle, ['request' => $request->all()]);

        $webhook = Webhook::event($event)->active()->first();

        $payload = file_get_contents('php://input');
        $signature = hash_hmac('sha256', $payload, $webhook->secret);

        if($signature !== $request->headers->get('X-Signature')) {
            //Do all the stuff
            Log::error($eventTitle . ' - Signature did not match:', ['request' => $request->all(), 'payload' => $payload]);
            return abort(403, 'Can\'t assure payload reliability');
        }

        Log::info($eventTitle . ' - Success:', ['request' => $request, 'payload' => $payload]);

        return $request->all();
    }
}