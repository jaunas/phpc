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
     */
    public function __construct(array $command, ?string $workingDirectory)
    {
        $process = new Process($command, $workingDirectory);

        $this->exitCode = $process->run();
        $this->output = $process->getOutput();
        $this->errorOutput = $process->getErrorOutput();
    }
}
