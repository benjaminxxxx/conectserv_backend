<?php

namespace App\Http\Controllers;

use App\Models\Profesional;
use App\Models\VerificacionWhatsapp;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    public function sendWhatsappVerification(Request $request)
    {
        $request->validate([
            'numero' => 'required|string'
        ]);

        try {

            $type = $request->input('type') ?? null;
            if ($type == 'login') {
                //en caso sea de tipo login el usuario ya debe existir para verificar
                $profesional = Profesional::where('whatsapp', $request->input('numero'))->first();
                if (!$profesional) {
                    return response()->json([
                        "success" => false,
                        "error" => "Error al enviar mensaje.",
                        "details" => "El número no esta registrado en ninguna cuenta"
                    ], 500);
                }
            }
            $codigoGenerado = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $token = env('WHATSAPP_ACCESS_TOKEN');
            $phoneId = env('WHATSAPP_PHONE_ID');
            $numero = (string) $request->input('numero');

            VerificacionWhatsapp::create([
                'numero' => $numero,
                'codigo' => $codigoGenerado,
                'fecha_expira' => Carbon::now()->addMinutes(1) // Expira en 1 minuto
            ]);

            $response = Http::withToken($token)->post("https://graph.facebook.com/v18.0/$phoneId/messages", [
                "messaging_product" => "whatsapp",
                "to" => $numero,
                "type" => "template",
                "template" => [
                    "name" => "codigo_otp",
                    "language" => ["code" => "es"],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                ["type" => "text", "text" => $codigoGenerado]
                            ]
                        ],
                        [
                            "type" => "button",
                            "sub_type" => "url",
                            "index" => "0",
                            "parameters" => [
                                ["type" => "text", "text" => $codigoGenerado]
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                return response()->json([
                    "success" => true,
                    "message" => "Mensaje enviado correctamente.",
                    "data" => $response->json()
                ], 200);
            } else {
                return response()->json([
                    "success" => false,
                    "error" => "Error al enviar mensaje.",
                    "details" => $response->json()
                ], $response->status());
            }
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "error" => "Error al enviar mensaje.",
                "details" => $th->getMessage()
            ], 500);
        }
    }

    public function checkVerification(Request $request)
    {
        try {
            // Validar que los datos existen en el request
            $request->validate([
                'numero' => 'required|string',
                'codigo' => 'required|string' // Asegurar que se trate como cadena
            ]);

            // Obtener datos del request
            $numero = $request->input('numero');
            $codigo = (string) $request->input('codigo'); // Convertir a string por seguridad

            // Buscar verificación válida
            $verification = VerificacionWhatsapp::where('numero', $numero)
                ->where('codigo', $codigo)
                ->where('fecha_expira', '>', Carbon::now())
                ->first();

            if ($verification) {
                // Eliminar código después de usarlo
                $verification->delete();

                return response()->json(["success" => true, "message" => "Código verificado."]);
            } else {
                return response()->json(["success" => false, "error" => "Código incorrecto o expirado."], 400);
            }

        } catch (\Exception $e) {
            return response()->json(["success" => false, "error" => $e->getMessage()], 400);
        }
    }
}
