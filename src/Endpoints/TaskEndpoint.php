<?php

namespace Cyberfusion\Oxxa\Endpoints;

use Cyberfusion\Oxxa\Contracts\Endpoint as EndpointContract;
use Cyberfusion\Oxxa\Enum\StatusCode;
use Cyberfusion\Oxxa\Exceptions\OxxaException;
use Cyberfusion\Oxxa\Models\Task;
use Cyberfusion\Oxxa\Support\ArrayHelper;
use Cyberfusion\Oxxa\Support\OxxaResult;
use DateTime;
use Symfony\Component\DomCrawler\Crawler;

class TaskEndpoint extends Endpoint implements EndpointContract
{
    /**
     * Returns all the available identities.
     *
     * @throws OxxaException
     */
    public function list(?int $start = null): OxxaResult
    {
        $xml = $this
            ->client
            ->request(ArrayHelper::transformToParameters([
                'command' => 'task_list',
                'start' => $start,
            ]));

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);
        if ($statusCode !== StatusCode::STATUS_TASK_LIST_RETRIEVED) {
            return new OxxaResult(
                success: false,
                message: $statusDescription,
                status: $statusCode,
            );
        }

        $tasks = [];
        $xml
            ->filter('channel > order > details > task')
            ->each(function (Crawler $taskNode) use (&$tasks) {
                $tasks[] = new Task(
                    id: (int) $taskNode->filter('task_id')->text(),
                    sld: $taskNode->filter('sld')->text(),
                    tld: $taskNode->filter('tld')->text(),
                    title: $taskNode->filter('tasktitle')->text(),
                    description: $taskNode->filter('description')->count()
                        ? $taskNode->filter('description')->text()
                        : null,
                    dateTime: $taskNode->filter('datetime')->count()
                        ? DateTime::createFromFormat('Y-m-d H:i:s', $taskNode->filter('datetime')->text())
                        : null,
                    domains: $taskNode->filter('domains')->count()
                        ? explode(',', $taskNode->filter('domains')->text())
                        : [],
                    type: $taskNode->filter('type')->count()
                        ? $taskNode->filter('type')->text()
                        : null,
                    info: $taskNode->filter('info')->count()
                        ? $taskNode->filter('info')->text()
                        : null,
                );
            });

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'tasks' => $tasks,
            ],
            status: $statusCode,
        );
    }

    public function get(int $taskId): OxxaResult
    {
        $xml = $this
            ->client
            ->request([
                'command' => 'task_get',
                'task_id' => $taskId,
            ]);

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);
        if ($statusCode !== StatusCode::STATUS_TASK_RETRIEVED) {
            return new OxxaResult(
                success: false,
                message: $statusDescription,
                status: $statusCode,
            );
        }

        $detailsNode = $xml->filter('channel > order > details');

        $task = new Task(
            id: $taskId,
            sld: $detailsNode->filter('sld')->text(),
            tld: $detailsNode->filter('tld')->text(),
            title: $detailsNode->filter('tasktitle')->text(),
            description: $detailsNode->filter('description')->text(),
            dateTime: $detailsNode->filter('datetime')->count()
                ? DateTime::createFromFormat('Y-m-d H:i:s', $detailsNode->filter('datetime')->text())
                : null,
            domains: $detailsNode->filter('domains')->count()
                ? explode(',', $detailsNode->filter('domains')->text())
                : [],
            type: $detailsNode->filter('type')->text(),
            info: $detailsNode->filter('info')->text(),
        );

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'task' => $task,
            ],
            status: $statusCode,
        );
    }
}
