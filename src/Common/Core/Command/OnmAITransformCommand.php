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
            ->addOption('contents', null, InputOption::VALUE_REQUIRED, 'Comma separated list of content ids')
            ->addOption('promptTitle', null, InputOption::VALUE_REQUIRED, 'Prompt id for title')
            ->addOption('promptDescription', null, InputOption::VALUE_REQUIRED, 'Prompt id for description')
            ->addOption('promptBody', null, InputOption::VALUE_REQUIRED, 'Prompt id for body')
            ->addOption('task', null, InputOption::VALUE_REQUIRED, 'Task id');

        $this->steps[] = 2;
    }

    /**
     * Executes the command logic.
     */
    protected function do()
    {
        list($instance, $ids, $promptTitleId, $promptDescriptionId, $promptBodyId) = $this->getParameters();
        $this->setInstance($instance);

        $prompts = $this->getPrompts($promptTitleId, $promptDescriptionId, $promptBodyId);

        $this->steps[] = count($ids);
        foreach ($ids as $id) {
            $this->writeStep("Content $id", false, 2);
            try {
                $this->processContent($id, $prompts);
                $this->writeStatus('success', 'DONE', true);
            } catch (\Exception $e) {
                $this->getContainer()->get('error.log')->error($e->getMessage());
                $this->writeStatus('error', 'FAIL', true);
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
        $contents          = $this->input->getOption('contents');
        $promptTitle       = $this->input->getOption('promptTitle');
        $promptDescription = $this->input->getOption('promptDescription');
        $promptBody        = $this->input->getOption('promptBody');
        $this->taskPK      = $this->input->getOption('task');

        if (empty($instance) || empty($contents) || empty($promptTitle)
            || empty($promptDescription) || empty($promptBody)
        ) {
            throw new \InvalidArgumentException('Parameters instance, contents, promptTitle, promptDescription 
            and promptBody are mandatory');
        }

        $ids = preg_split('/\s*,\s*/', $contents);

        return [$instance, $ids, $promptTitle, $promptDescription, $promptBody];
    }

    /**
     * Fetch prompts by id from manager repository.
     *
     * @param int $titleId
     * @param int $descriptionId
     * @param int $bodyId
     *
     * @return array
     */
    protected function getPrompts($titleId, $descriptionId, $bodyId): array
    {
        $repo = $this->getContainer()->get('orm.manager')->getRepository('PromptManager');

        return [
            'titlePrompt'       => $repo->find($titleId)->getData(),
            'descriptionPrompt' => $repo->find($descriptionId)->getData(),
            'bodyPrompt'        => $repo->find($bodyId)->getData(),
        ];
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
     * Processes a single content id applying the AI transformation.
     *
     * @param int   $id      Content id
     * @param array $prompts Prompt data
     */
    protected function processContent($id, array $prompts): void
    {
        $service = $this->getContainer()->get('api.service.content');
        $content = $service->getItem($id);

        $data = [
            'title'       => $content->title ?? '',
            'description' => $content->description ?? '',
            'body'        => $content->body ?? '',
        ];

        $data = array_merge($data, $prompts);

        $result = $this->getContainer()->get('core.helper.ai')->transform(
            $data,
            ['title', 'title_int', 'description', 'body']
        );

        $update = [
            'title'       => $result['title'] ?? $content->title,
            'description' => $result['description'] ?? $content->description,
            'body'        => $result['body'] ?? $content->body,
        ];

        $service->updateItem($id, $update);
    }
}
