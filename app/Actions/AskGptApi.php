<?php

namespace App\Actions;

use App\DTO\GptApi\GptApiMessageDTO;
use App\DTO\GptApi\Request\GptApiRequestDTO;
use App\DTO\GptApi\Response\GptApiIAResponseDTO;
use App\DTO\GptApi\Response\GptApiResponseDTO;
use App\DTO\GptApi\Response\GptApiResponseUsageDTO;
use Carbon\Carbon;
use Http;
use Lorisleiva\Actions\Action;

class AskGptApi extends Action
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    /**
     * @param  GptApiRequestDTO  $apiRequest
     * @return GptApiResponseDTO
     */
    public function handle(GptApiRequestDTO $apiRequest): GptApiResponseDTO
    {
        if ($apiRequest->fakeResponse) {
            return $this->getFakeReponse();
        }

        $response = json_decode(Http::withToken($apiRequest->apiKey)->post(
            $apiRequest->baseUrl,
            [
                'model' => $apiRequest->model,
                'messages' => [$apiRequest->message],
                'max_tokens' => $apiRequest->maxToken
            ]
        )->body());


        return GptApiResponseDTO::from([
            'id' => $response->id,
            'created' => Carbon::createFromTimestamp($response->created),
            'model' => $response->model,
            'object' => $response->object,
            'usage' => GptApiResponseUsageDTO::from([
                'promptToken' => $response->usage->prompt_tokens,
                'completionToken' => $response->usage->completion_tokens,
                'totalToken' => $response->usage->total_tokens,
            ]),
            'responses' => GptApiIAResponseDTO::collection(
                collect($response->choices)->map(
                    static fn(object $response) => [
                        'message' => GptApiMessageDTO::from([
                            'role' => $response->message->role,
                            'content' => $response->message->content,
                        ]),
                        'finishReason' => $response->finish_reason,
                        'index' => $response->index,
                    ],
                )->toArray()
            ),
        ]);
    }

    private function getFakeReponse(): GptApiResponseDTO
    {
        return GptApiResponseDTO::from([
            'id' => -1,
            'created' => Carbon::now(),
            'model' => 'assistant',
            'object' => '',
            'usage' => GptApiResponseUsageDTO::from([
                'promptToken' => 0,
                'completionToken' => 0,
                'totalToken' => 0,
            ]),
            'responses' => GptApiIAResponseDTO::collection([[
                'message' => GptApiMessageDTO::from([
                    'role' => 'assistant',
                    'content' => 'fake response',
                ]),
                'finishReason' => '',
                'index' => -1,
            ]]),
        ]);
    }
}
