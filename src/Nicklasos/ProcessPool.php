<?php
namespace Nicklasos;

/**
 * Depends on the symfony/process
 * @see http://symfony.com/doc/current/components/process.html
 */
use Symfony\Component\Process\Process;

/**
 * <code>
 * $pool = new ProcessPool(
 *     'php child.php', // command
 *     [ // arguments
 *         'one',
 *         'two',
 *         'three',
 *     ],
 *     6 // number processes (running at same time)
 * );
 *
 * $pool->run(function ($arg, $result) {
 *     echo "{$arg}: {$result}";
 * });
 * </code>
 */
class ProcessPool
{
    /**
     * Number of processes that running at same time
     * @var int
     */
    protected $numProcesses;

    /**
     * Command to run (php run_child.php)
     * @var string
     */
    protected $command;

    /**
     * Array of tasks to pass to command
     *
     * $commands = ['one', 'two', 'three']
     *
     * php run_child.php one
     * php run_child.php two
     * php run_child.php three
     * @var array
     */
    protected $arguments;

    /**
     * Current processes
     * (Now running)
     *
     * [
     *    ['process' => Process, 'argument' => Argument that process running with],
     *    ...
     * ]
     *
     * @var array
     */
    protected $processes;

    /**
     * milliseconds
     * sleep between ticks
     * @var int
     */
    protected $tick;

    /**
     * @param string $command Example: php run_child.php
     * @param array $arguments this params will be passed to command
     * @param int $numProcesses number of running processes at same time
     * @param int $tick sleep between ticks
     */
    public function __construct($command, array $arguments, $numProcesses = 3, $tick = 100)
    {
        $this->tick = $tick;
        $this->command = $command;
        $this->arguments = $arguments;
        $this->numProcesses = $numProcesses;
    }

    /**
     * @param callable $callback called when process is done
     */
    public function run(callable $callback = null)
    {
        for ($i = 0; $i < $this->numProcesses; $i++) {
            $argument = array_shift($this->arguments);
            if ($argument) {
                $this->startProcess($argument);
            }
        }

        while (count($this->processes)) {
            /**
             * @var Process[] $process
             */
            foreach ($this->processes as $key => $process) {
                if (!$process['process']->isRunning()) {
                    if (is_callable($callback)) {
                        $callback($process['argument'], $process['process']->getOutput());
                    }

                    unset($this->processes[$key]);

                    $argument = array_shift($this->arguments);
                    if ($argument) {
                        $this->startProcess($argument);
                    }
                }
            }

            usleep($this->tick);
        }
    }

    /**
     * @param string $argument
     */
    protected function startProcess($argument)
    {
        $process = new Process("{$this->command} {$argument}");
        $process->start();
        $this->processes[] = [
            'argument' => $argument,
            'process' => $process
        ];
    }
}
