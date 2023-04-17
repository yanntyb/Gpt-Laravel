<?php

namespace App\Console\Commands;

use App\Actions\AskGptApi;
use App\DTO\GptApi\GptApiMessageDTO;
use App\DTO\GptApi\History\GptApiHistoryCollectionDTO;
use App\DTO\GptApi\History\GptApiHistoryContentDTO;
use App\DTO\GptApi\History\GptApiHistoryDTO;
use App\DTO\GptApi\Request\GptApiRequestDTO;
use App\DTO\GptApi\Response\GptApiResponseDTO;
use ConsoleService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ContactGptApiCommand extends Command
{

    public int $usedToken = 0;
    public GptApiHistoryCollectionDTO $apiHistory;

    protected function configure(): void
    {
        $this->setName('chat:gpt')
            ->addArgument('prompt', InputArgument::REQUIRED, 'Message a envoyer')
            ->addArgument(
                'max_token',
                InputArgument::OPTIONAL,
                'Maximum de token utilisé',
                1000
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
                0
            )
            ->addOption(
                'memory',
                'm',
                InputArgument::OPTIONAL,
                'Défini la commande en mode conversation avec memoire',
                0
            )
        ;
    }

    public function __construct(public AskGptApi $askGptApi)
    {
        $this->apiHistory = GptApiHistoryCollectionDTO::from([
           'items' => [],
        ]);
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
        while ($this->usedToken < $this->argument('max_token')) {
            ConsoleService::clearConsole();
            $this->infoTokenUsed();
            $this->showMessagesHistory();

            if ($this->option('memory')) {
                $response = $this->sendHistoryMessageWithAsk();
                $this->info($response->getMessage());
                continue;
            }

            $response = $this->sendSingleMessageWithAsk();
            $this->info($response->getMessage());

        }

    }

    public function singleMessage(): void
    {
        $userMessage = $this->argument('prompt');
        $message = $this->sendSingleMessage(message: $userMessage);
        $this->info($message->getMessage());
    }

    public function sendSingleMessage(string $message, string $history = ''): GptApiResponseDTO
    {
        $gptApiRequestDto = $this->getRequestSetup();

        $gptApiRequestDto->message = GptApiMessageDTO::from([
            'role' => $gptApiRequestDto->message->role,
            'content' => $message,
        ]);


        $this->updateConversationHistory($gptApiRequestDto);

        $gptApiRequestWithHistory = clone $gptApiRequestDto;
        $history && ($gptApiRequestWithHistory->message = GptApiMessageDTO::from([
            'role' => $gptApiRequestDto->message->role,
            'content' => $history . PHP_EOL .
                ucfirst($gptApiRequestDto->getHistoryLabel()) . ': ' . $gptApiRequestDto->getMessage(),
        ]));



        $response = $this->askGptApi->handle($gptApiRequestWithHistory);
        $this->updateConversationHistory($response);
        $this->usedToken += $response->usage->totalToken;

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
     * @return GptApiResponseDTO
     */
    public function sendHistoryMessageWithAsk(): GptApiResponseDTO
    {
        $userMessage = $this->ask('Message');
        $pastMessage = 'En prenant compte du context des messages suivant: ' . PHP_EOL . $this->apiHistory
            ->getHistoryContent()
            ->map(
                fn(GptApiHistoryContentDTO $item) => $item->role . ': ' . $item->message
            )->join(PHP_EOL, PHP_EOL);


        return $this->sendSingleMessage(
            $userMessage,
            $this->apiHistory->count() ? $pastMessage : ''
        );
    }

    /**
     * @return GptApiRequestDTO
     */
    public function getRequestSetup(): GptApiRequestDTO
    {
        return GptApiRequestDTO::from([
            'apiKey' => env('OPEN_AI_KEY'),
            'model' => 'gpt-3.5-turbo',
            'maxToken' => $this->argument('max_response_token') ?? 1,
            'message' => GptApiMessageDTO::from([
                'content' => 'Placeholder',
                'role' => 'user',
            ]),
            'fakeResponse' => false,
        ]);
    }

    /**
     * @return void
     */
    public function infoTokenUsed(): void
    {
        $this->info('Token utilisé au total: ' . $this->usedToken . PHP_EOL);
    }

    /**
     * @param  GptApiRequestDTO|GptApiResponseDTO  $action
     * @return void
     * @throws \Exception
     */
    public function updateConversationHistory(GptApiRequestDTO|GptApiResponseDTO $action): void
    {
        $currentHistory = $this->apiHistory;

        $actionClass = get_class($action);
        $newHistoryItem = GptApiHistoryDTO::from([
            $actionClass::$historyProperty => $action,
        ]);

        $this->apiHistory = GptApiHistoryCollectionDTO::from([
            'items' => [...$currentHistory->items()->map(
                static fn(GptApiHistoryDTO $historyDTO) => GptApiHistoryDTO::from([
                    'request' => $historyDTO->request,
                    'response' => $historyDTO->response,
                ]),
            )->toArray(),clone $newHistoryItem],
        ]);

    }

    /**
     * @return void
     */
    public function showMessagesHistory(): void
    {

        $this->apiHistory
            ->getHistoryContent()
            ->each(
                fn(GptApiHistoryContentDTO $item) => $this->info($item->role . ': ' . $item->message)
            )
        ;
    }
}
