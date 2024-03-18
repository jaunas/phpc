<?php

namespace Jaunas\PhpCompiler\Tests;

use Symfony\Component\Process\Process;

class ProcessResult
{
    public int $exitCode;

    public string $output;

    public string $errorOutput;

    /**
     * @param string[] $command
     * @param array<string, string> $env
     */
    public function __construct(array $command, ?string $workingDirectory, ?array $env = null)
    {
        $process = new Process($command, $workingDirectory, $env);

        $this->exitCode = $process->run();
        $this->output = $process->getOutput();
        $this->errorOutput = $process->getErrorOutput();
    }
}
