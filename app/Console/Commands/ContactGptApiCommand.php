<?php

namespace App\Console\Commands;

use App\Actions\AskGptApi;
use App\DTO\GptApi\GptApiHistoryCollectionDTO;
use App\DTO\GptApi\GptApiHistoryDTO;
use App\DTO\GptApi\GptApiMessageDTO;
use App\DTO\GptApi\Request\GptApiRequestDTO;
use App\DTO\GptApi\Response\GptApiResponseDTO;
use App\DTO\GptApi\WithHistory;
use Illuminate\Console\Command;
use phpDocumentor\Reflection\Types\Collection;
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
        $firstMessage = $this->sendSingleMessage($this->argument('prompt'));
        $this->info($firstMessage->getMessage());
        $this->infoTokenUsed();

        $usedTokenProgressbar = $this->output->createProgressBar($this->argument('max_token'));

        $usedTokenProgressbar->start(startAt: $this->usedToken);

        while ($this->usedToken < $this->argument('max_token')) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                system('cls');
            } else {
                system('clear');
            }
            $this->showMessagesHistory();
            $response = $this->sendSingleMessageWithAsk();
            $this->info($response->getMessage());
            $this->infoTokenUsed();
            $usedTokenProgressbar->advance($response->usage->totalToken);
        }

        $usedTokenProgressbar->finish();

    }

    public function singleMessage(): void
    {
        $userMessage = $this->argument('prompt');
        $message = $this->sendSingleMessage(message: $userMessage);
        $this->info($message->getMessage());
    }

    public function sendSingleMessage(string $message): GptApiResponseDTO
    {

        $gptApiRequestDto = $this->getRequestSetup();
        $gptApiRequestDto->message->changePropertyValue('content', $message);

        $this->updateConversationHistory($gptApiRequestDto);
        $response = $this->askGptApi->handle($gptApiRequestDto);
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
     * @return GptApiRequestDTO
     */
    public function getRequestSetup(): GptApiRequestDTO
    {
        return GptApiRequestDTO::from([
            'apiKey' => env('OPEN_AI_KEY'),
            'model' => 'gpt-3.5-turbo',
//            'maxToken' => $this->argument('max_response_token'),
            'maxToken' => 1,
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

    /**
     * @param  GptApiRequestDTO|GptApiResponseDTO  $action
     * @return void
     * @throws \Exception
     */
    public function updateConversationHistory(GptApiRequestDTO|GptApiResponseDTO $action): void
    {
        $currentHistory = $this->apiHistory;

        $actionClass = get_class($action);
        $historyProperty = $actionClass::$historyProperty;
        $newHistoryItem = GptApiHistoryDTO::from([
           $historyProperty => $action,
        ]);

        $this->apiHistory = GptApiHistoryCollectionDTO::from([
            'items' => [...$currentHistory->items()->map(
                static fn(GptApiHistoryDTO $historyDTO) => GptApiHistoryDTO::from([
                    'request' => $historyDTO->request,
                    'response' => $historyDTO->response,
                ]),
            )->toArray(),$newHistoryItem],
        ]);
    }

    /**
     * @return void
     */
    public function showMessagesHistory(): void
    {
        $this->apiHistory->items()->map(
            function (GptApiHistoryDTO $item) {
                $filledProperty = $item->request ? 'request' : 'response';

                /** @var WithHistory $realProperty */
                $realProperty = $item->{$filledProperty};

                $this->info($realProperty->getHistoryLabel() . ': ' . $realProperty->getHistoryContent());

            }
        );
    }
}
