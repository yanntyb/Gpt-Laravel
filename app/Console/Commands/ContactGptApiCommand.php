<?php

namespace App\Console\Commands;

use App\Actions\AskGptApi;
use App\DTO\GptApiMessageDTO;
use App\DTO\GptApiRequestDTO;
use App\DTO\GptApiResponseDTO;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ContactGptApiCommand extends Command
{

    public int $usedToken = 0;
    public int $maxResponseToken;

    protected function configure(): void
    {
        $this->setName('chat:gpt')
            ->addArgument('prompt', InputArgument::REQUIRED, 'Message a envoyer')
            ->addArgument(
                'max_token',
                InputArgument::OPTIONAL,
                'Maximum de token utilisé',
                1
            )
            ->addArgument(
                'max_response_token',
                InputArgument::OPTIONAL,
                'Maximum de token utilisé',
                1
            )
            ->addOption(
                'conversation',
                'c',
                InputArgument::OPTIONAL,
                'Défini la commande en mode conversation',
                1
            );
    }


    public function __construct(public AskGptApi $askGptApi)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if ($this->option('conversation')) {
            $this->conversation();
            return true;
        }
        $this->singleMessage();
        return true;
    }

    /**
     * @return void
     */
    public function conversation(): void
    {
        $firstMessage = $this->sendSingleMessage($this->argument('prompt'));
        $this->info($firstMessage->getFirstResponseMessageContent());

        $usedTokenProgressbar = $this->output->createProgressBar($this->argument('max_token'));

        $usedTokenProgressbar->start();

        while ($this->usedToken < $this->argument('max_token')) {
            $response = $this->sendSingleMessageWithAsk();
            $this->info($response->getFirstResponseMessageContent());
            $usedTokenProgressbar->advance($response->usage->totalToken);
        }

        $usedTokenProgressbar->finish();

    }

    public function singleMessage(): void
    {
        $userMessage = $this->argument('prompt');
        $message = $this->sendSingleMessage(message: $userMessage);
        $this->info($message->getFirstResponseMessageContent());
    }

    public function sendSingleMessage(string $message): GptApiResponseDTO
    {

        $gptApiRequestDto = $this->getRequestSetup();
        $gptApiRequestDto->message->changePropertyValue('content', $message);


        $response = $this->askGptApi->handle($gptApiRequestDto);
        $this->usedToken += $response->usage->totalToken;
        $this->infoTokenUsed();

        return $response;
    }

    /**
     * @return GptApiResponseDTO
     */
    public function sendSingleMessageWithAsk(): GptApiResponseDTO
    {
        $userMessage = $this->ask('Message');
        return $this->sendSingleMessage($userMessage);
    }

    /**
     * @return GptApiRequestDTO
     */
    public function getRequestSetup(): GptApiRequestDTO
    {
        return GptApiRequestDTO::from([
            'apiKey' => env('OPEN_AI_KEY'),
            'model' => 'gpt-3.5-turbo',
            'maxToken' => $this->argument('max_response_token'),
            'message' => GptApiMessageDTO::from([
                'content' => 'Placeholder',
                'role' => 'user',
            ])
        ]);
    }

    /**
     * @return void
     */
    public function infoTokenUsed(): void
    {
        $this->info('Token utilisé au total: ' . $this->usedToken);
    }
}
