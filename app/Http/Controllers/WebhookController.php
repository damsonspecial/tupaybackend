<?php

namespace App\Http\Controllers;

use App\Actions\Webhooks\ProcessSettlementWebhookAction;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function settlement(Request $request, ProcessSettlementWebhookAction $action)
    {
        $action->execute($request->all());

        return $this->success(null, 'Webhook processed');
    }
}
