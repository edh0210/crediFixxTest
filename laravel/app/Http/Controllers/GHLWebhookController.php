<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class GHLWebhookController extends Controller
{
    public function contactUpdated(Request $request)
    {

        Log::info('Webhook GHL recibido', $request->all());

        $contactId = $request->input('contact.id');
        if (!$contactId) {
            return response()->json(['error' => 'No contactId'], 400);
        }


        $user = User::where('ghl_contact_id', $contactId)->first();
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }


        $user->name  = trim($request->input('contact.firstName', $user->name) . ' ' . $request->input('contact.lastName', ''));
        $user->email = $request->input('contact.email', $user->email);
        $user->phone = $request->input('contact.phone', $user->phone);
        $user->save();

        return response()->json(['message' => 'Usuario actualizado correctamente']);
    }
}
