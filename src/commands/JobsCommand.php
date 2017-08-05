<?php

namespace ApiClientGitlab\Commands;

use ApiClientGitlab\Services\Entities\Job;
use ApiClientGitlab\Services\Jobs;
use ApiClientGitlab\ApiClient;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UsersCommand
 *
 * @package ApiClientGitlab\Command
 */
class JobsCommand extends Command
{
    /**
     *
     */
    protected function configure()
    {
        $this// the name of the command (the part after "bin/console")
        ->setName('jobs:status')// the short description shown while running "php bin/console list"
        ->setDescription('Jobs status ')// the full command description shown when running the command with
        // the "--help" option
        ->setHelp('This command allows you to jobs status...');

        $this->addArgument('id_project', InputArgument::REQUIRED)->addArgument('id_jobs', InputArgument::REQUIRED);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = $this->getConfiguration('GITLAB_TOKEN');
        $type = $this->getConfiguration('GITLAB_TYPE_ACCESS_TOKEN');
        $endpoint = $this->getConfiguration('GITLAB_ENDPOINT');

        $client = new ApiClient($endpoint, $token, $type);
        $serviceProjects = new Jobs($client);

        $projectId = $input->getArgument('id_project');
        $idJobs = $input->getArgument('id_jobs');
        $result = $serviceProjects->get($projectId, $idJobs);

        if ($result->success()) {

            $job = $result->current();

            if($job->getStatus() === Job::STATUS_FAILLED) {
                $output->writeln('<fg=red>' . $result->current()->getStatus() . '</>');
            }elseif($job->getStatus() === Job::STATUS_PASSED) {
                $output->writeln('<fg=green>' . $result->current()->getStatus() . '</>');
            }else {
                $output->writeln($result->current()->getStatus());
            }
        } else {
            $output->writeln('<fg=red>ERROR : ' . $result->getMessage() . '</>');
        }
    }
}