<?php

namespace Common\Core\Command;

use Symfony\Component\Console\Input\InputOption;

class OnmAITransformCommand extends Command
{
    private $taskPK = null;

    /**
     * Configures the command options and description.
     */
    protected function configure()
    {
        $this
            ->setName('core:onmai:transform')
            ->setDescription('Applies AI transformations to contents using provided prompts')
            ->addOption('instance', null, InputOption::VALUE_REQUIRED, 'ID of the instance')
            ->addOption('oql', null, InputOption::VALUE_REQUIRED, 'OQL query to select contents')
            ->addOption('promptTitle', null, InputOption::VALUE_OPTIONAL, 'Prompt id for title')
            ->addOption('promptDescription', null, InputOption::VALUE_OPTIONAL, 'Prompt id for description')
            ->addOption('promptBody', null, InputOption::VALUE_OPTIONAL, 'Prompt id for body')
            ->addOption('task', null, InputOption::VALUE_REQUIRED, 'Task id');

        $this->steps[] = 2;
    }

    /**
     * Executes the command logic.
     */
    protected function do()
    {
        list($instance, $oql, $promptTitleId, $promptDescriptionId, $promptBodyId) = $this->getParameters();
        $this->setInstance($instance);

        $service = $this->getContainer()->get('api.service.content');
        $response = $service->getList($oql);
        $ids = array_map(function ($item) {
            return $item->pk_content;
        }, $response['items']);

        $prompts = $this->getPrompts($promptTitleId, $promptDescriptionId, $promptBodyId);

        $this->steps[] = count($ids);
        foreach ($ids as $id) {
            $baseMsg = "Content $id";
            $this->writeStep($baseMsg, false, 2);
            try {
                $this->processContent($id, $prompts, $promptTitleId, $promptDescriptionId, $promptBodyId);
                $this->writeStatus('success', 'DONE', true);
                $this->appendTaskOutput($baseMsg, 'DONE');
            } catch (\Exception $e) {
                $this->getContainer()->get('error.log')->error($e->getMessage());
                $this->writeStatus('error', 'FAIL', true);
                $this->appendTaskOutput($baseMsg, 'ERROR', $e->getMessage());
            }
        }

        if ($this->taskPK) {
            $this->getContainer()->get('orm.manager')
                ->remove(
                    $this->getContainer()->get('orm.manager')->getRepository('Task')
                        ->find($this->taskPK)
                );
        }
    }

    /**
     * Returns the command parameters.
     *
     * @return array
     */
    protected function getParameters(): array
    {
        $instance          = $this->input->getOption('instance');
        $oql               = $this->input->getOption('oql');
        $promptTitle       = $this->input->getOption('promptTitle');
        $promptDescription = $this->input->getOption('promptDescription');
        $promptBody        = $this->input->getOption('promptBody');
        $this->taskPK      = $this->input->getOption('task');

        if (empty($instance) || empty($oql)) {
            throw new \InvalidArgumentException('Parameters instance and oql are mandatory');
        }

        return [$instance, $oql, $promptTitle, $promptDescription, $promptBody];
    }

    /**
     * Fetch prompts by id from manager repository.
     *
     * @param int|null $titleId
     * @param int|null $descriptionId
     * @param int|null $bodyId
     *
     * @return array
     */
    protected function getPrompts($titleId, $descriptionId, $bodyId): array
    {
        $repo = $this->getContainer()->get('orm.manager')->getRepository('PromptManager');

        $prompts = [];

        if ($titleId) {
            $prompts['titlePrompt'] = $repo->find($titleId)->getData();
        }
        if ($descriptionId) {
            $prompts['descriptionPrompt'] = $repo->find($descriptionId)->getData();
        }
        if ($bodyId) {
            $prompts['bodyPrompt'] = $repo->find($bodyId)->getData();
        }

        return $prompts;
    }

    /**
     * Set the value of instance
     *
     * @return  self
     */
    public function setInstance($instance)
    {
        $instance = $this->getContainer()->get('orm.manager')
            ->getRepository('Instance')->find($instance);
        $this->getContainer()->get('core.loader')->configureInstance($instance);
        $this->getContainer()->get('core.security')->setInstance($instance);

        return $this;
    }

    /**
     * Appends a message to the task output if a task id is provided.
     */
    protected function appendTaskOutput(string $message, string $status, string $description = ''): void
    {
        if (!$this->taskPK) {
            return;
        }

        $conn   = $this->getContainer()->get('orm.manager')->getConnection('manager');
        $output = $conn->fetchColumn('SELECT output FROM tasks WHERE id = ?', [ $this->taskPK ]);

        $line = str_pad($message, $this->padding, '.') . $status;
        if (!empty($description)) {
            $line .= ' ' . $description;
        }
        $output = ($output ?? '') . $line . PHP_EOL;

        $conn->update('tasks', [ 'output' => $output ], [ 'id' => $this->taskPK ]);
    }

    /**
     * Processes a single content id applying the AI transformation.
     *
     * @param int   $id      Content id
     * @param array $prompts Prompt data
     * @param int|null $titleId
     * @param int|null $descriptionId
     * @param int|null $bodyId
     */
    protected function processContent($id, array $prompts, $titleId = null, $descriptionId = null, $bodyId = null): void
    {
        $service = $this->getContainer()->get('api.service.content');
        $content = $service->getItem($id);

        $data = [
            'title'       => $content->title ?? '',
            'title_int'   => $content->title_int ?? '',
            'description' => $content->description ?? '',
            'body'        => $content->body ?? '',
            'categories'  => $content->categories ?? '',
            'slug'        => $content->slug ?? '',
        ];

        $data = array_merge($data, $prompts);

        // Build the fields to transform dynamically
        $fieldsToTransform = [];
        if ($titleId) {
            $fieldsToTransform[] = 'title';
            $fieldsToTransform[] = 'title_int';
        }
        if ($descriptionId) {
            $fieldsToTransform[] = 'description';
        }
        if ($bodyId) {
            $fieldsToTransform[] = 'body';
        }
        
        $result = $this->getContainer()->get('core.helper.ai')->transform($data, $fieldsToTransform);

        $update = [
            'title'       => $result['title'] ?? $content->title,
            'description' => $result['description'] ?? $content->description,
            'body'        => $result['body'] ?? $content->body,
        ];

        $service->updateItem($id, $update);
    }
}
