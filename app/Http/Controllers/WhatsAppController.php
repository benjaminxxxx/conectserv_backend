<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;

class WhatsAppController extends Controller
{
    public function sendVerification(Request $request, $phoneNumber)
    {
        try {
            // Configurar Twilio
            $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));

            // Enviar código de verificación por WhatsApp
            $verification = $twilio->verify->v2
                ->services(env('TWILIO_SERVICE_SID'))
                ->verifications
                ->create($phoneNumber, "whatsapp"); // Solo string, no array

            return response()->json(['status' => $verification->status]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function checkVerification(Request $request)
    {
        $request->validate([
            'phoneNumber' => 'required|string',
            'code' => 'required|string'
        ]);

        try {
            // Configurar Twilio
            $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));

            $phoneNumber = $request->phoneNumber;
            $code = $request->code;

            if (!str_starts_with($phoneNumber, '+')) {
                $phoneNumber = '+' . $phoneNumber;
            }
            
            // Verificar código
            $verificationCheck = $twilio->verify->v2
                ->services(env('TWILIO_SERVICE_SID'))
                ->verificationChecks
                ->create([
                    'to' => $phoneNumber,
                    'code' => $code
                ]);

            if ($verificationCheck->status === 'approved') {
                return response()->json(['message' => 'Código verificado correctamente.']);
            } else {
                return response()->json(['error' => 'Código incorrecto o expirado.'], 400);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
