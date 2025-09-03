<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI;

class AICoachController extends Controller
{
    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $userMessage = strtolower($validated['message']);
        $mode = 'default';

        // Mode wisselen, maar NIET opslaan in sessie
        if (str_contains($userMessage, 'ga mo mode')) {
            $mode = 'mo';
            // Sla bericht van Mo ook in de sessie op
            session()->push('chat_history', ['role' => 'assistant', 'content' => "Fakka broertje, het is je favoriete straatjunkie Mo hier. Ik ga jou die summer body geven niffo! Vertel me waarbij ik je moet helpen en ik fix die dingen."]);
            return response()->json([
                'reply' => "Fakka broertje, het is je favoriete straatjunkie Mo hier. Ik ga jou die summer body geven niffo! Vertel me waarbij ik je moet helpen en ik fix die dingen."
            ]);
        }

        if (str_contains($userMessage, 'stop mo mode')) {
            session()->push('chat_history', ['role' => 'assistant', 'content' => "✅ Terug naar de normale coach mode 👊"]);
            return response()->json([
                'reply' => "✅ Terug naar de normale coach mode 👊"
            ]);
        }

        $client = OpenAI::client(config('openai.api_key'));

        // Basis prompt
        $systemPrompt = "Je bent een professionele personal trainer en coach van 2BeFit.  
        Je mag uitsluitend antwoorden over fitness, krachttraining, conditietraining, sportprestaties, voeding, supplementen en gezonde leefstijl.  

        Als een vraag NIET met deze onderwerpen te maken heeft:
        - Reageer vriendelijk, kort en menselijk.
        - Leg kort uit dat je je alleen richt op training, voeding en leefstijl.
        - Geef eventueel een speelse of motiverende twist (bijv. een smiley of korte aanmoediging).
        - Wissel je formuleringen af, zodat het niet altijd hetzelfde klinkt.

        Wees positief, motiverend en concreet. Houd je antwoorden kort en krachtig.
        
        Geen geen tekens zoals — in je berichten!";

        // Mo mode activeren
        if ($mode === 'mo') {
            $systemPrompt .= "\n\n🔄 EXTRA: Je staat nu in 'Mo mode'.  
            - Je bent een Turkse straatjongen, mattie van de buurt.  
            - Je praat losjes en gebruikt straattaal: woorden als bro, mattie, wallah, ey, ouwe, snap je.  
            - Je houdt van Turkse dingen: döner, shoarma, baklava, Turkse thee, voetbal (Galatasaray, Fenerbahçe).  
            - Gebruik dit om kleur te geven aan je antwoorden, maar blijf altijd bij fitness, sport en voeding.  
            - Houd je antwoorden kort, krachtig en met swagger, alsof je op de hoek van de straat advies geeft.  
            - Voeg humor toe: maak af en toe een vergelijking met eten of voetbal.  
            - Je motiveert alsof je een broer bent die je pusht in de gym.  
            - Je scheldt nooit, maar mag wel stoer en uitdagend klinken.  
            - Houd je adviezen praktisch, alsof je tips geeft na een sessie in de gym of tijdens een broodje döner.
            
            Houd je antwoorden kort en krachtig!!!! DIT IS HEEL BELANGRIJK";
        }

        // Haal eerdere chatgeschiedenis op uit sessie
        $history = session('chat_history', []);

        // Voeg system prompt altijd aan het begin toe
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Voeg eerdere berichten toe
        $messages = array_merge($messages, $history);

        // Voeg huidige user bericht toe
        $messages[] = ['role' => 'user', 'content' => $validated['message']];

        $response = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'temperature' => 0.9,
            'messages' => $messages,
        ]);

        $reply = $response->choices[0]->message->content;

        // Update sessiegeschiedenis
        session()->push('chat_history', ['role' => 'user', 'content' => $validated['message']]);
        session()->push('chat_history', ['role' => 'assistant', 'content' => $reply]);

        return response()->json([
            'reply' => $reply,
        ]);
    }
}