
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TelegramService
{
    protected string $botToken;
    protected string $apiUrl;
    
    public function __construct()
    {
        $this->botToken = config('telegram.bot_token');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}/";
    }

    public function startListening()
    {
        $offset = 0;
        
        while (true) {
            $updates = $this->getUpdates($offset);
            
            foreach ($updates as $update) {
                $offset = $update->update_id + 1;
                $this->processUpdate($update);
            }
            
            sleep(1);
        }
    }

    protected function processUpdate($update)
    {
        $message = $update->message ?? null;
        if (!$message) return;

        $chatId = $message->chat->id;
        $text = $message->text ?? '';
        $user = $message->from;

        $this->storeUser($user);

        switch ($text) {
            case '/start':
                $this->sendWelcomeMessage($chatId);
                break;
            case '/quiz':
                $this->sendQuiz($chatId);
                break;
            case '/subscribe':
                $this->handleSubscription($chatId, $user);
                break;
            default:
                $this->handleTextMessage($chatId, $text);
        }
    }

    protected function storeUser($user)
    {
        $users = $this->getJsonData('users.json');
        
        if (!isset($users[$user->id])) {
            $users[$user->id] = [
                'id' => $user->id,
                'first_name' => $user->first_name ?? '',
                'username' => $user->username ?? '',
                'language_code' => $user->language_code ?? 'en',
                'subscribed' => false,
                'created_at' => now()->toDateTimeString()
            ];
            
            $this->saveJsonData('users.json', $users);
        }
    }

    protected function sendWelcomeMessage($chatId)
    {
        $emoji = json_decode('"\u2B50\uFE0F"'); // ⭐️
        $message = "{$emoji} *Welcome to Cosmic Stars Blog!* {$emoji}\n\n";
        $message .= "Explore the universe with us!\n\n";
        $message .= "Available commands:\n";
        $message .= "/start - Show this message\n";
        $message .= "/quiz - Take a stars quiz\n";
        $message .= "/subscribe - Get blog updates";

        $this->sendMessage($chatId, $message, 'Markdown');
    }

    protected function sendQuiz($chatId)
    {
        $quizzes = $this->getJsonData('quizzes.json');
        $quiz = $quizzes[array_rand($quizzes)];
        
        $this->sendMessage($chatId, "Quiz: {$quiz['question']}\n\nOptions:\n".implode("\n", $quiz['options']));
    }

    protected function handleSubscription($chatId, $user)
    {
        $users = $this->getJsonData('users.json');
        $users[$user->id]['subscribed'] = true;
        $this->saveJsonData('users.json', $users);
        
        $this->sendMessage($chatId, "You've been subscribed to our blog updates!");
    }

    protected function handleTextMessage($chatId, $text)
    {
        $this->sendMessage($chatId, "I'm a bot for Cosmic Stars Blog. Use /start to see available commands.");
    }

    protected function sendMessage($chatId, $text, $parseMode = null)
    {
        Http::post($this->apiUrl.'sendMessage', [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $parseMode
        ]);
    }

    protected function getJsonData($filename)
    {
        return json_decode(Storage::get("telegram/{$filename}"), true) ?? [];
    }

    protected function saveJsonData($filename, $data)
    {
        Storage::put("telegram/{$filename}", json_encode($data));
    }
}
