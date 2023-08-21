<?php

namespace App\Console\Support;

use App\Exceptions\ApplicationErrorException;
use Illuminate\Console\Concerns\InteractsWithIO;
use Symfony\Component\Console\Output\ConsoleOutput;

trait ConsolePrinter
{
    use InteractsWithIO;

    public int $placeholderLength = 96;

    public function createOutput(): void
    {
        if (empty($this->output)) {
            $this->output = new ConsoleOutput();
        }
    }

    public function log($data): void
    {
        $this->createOutput();
        $time = time() + 60 * 60 * 7;
        if (is_array($data)) {
            $this->line('<comment>' . '[' . date('m/d/Y H:i:s', $time) . ']' . '</comment>');
            $this->info(print_r($data, true));
            return;
        }
        $this->line('<comment>' . '[' . date('m/d/Y H:i:s', $time) . ']' . '</comment> ' . $data);
    }

    /**
     * @throws ApplicationErrorException
     */
    public function printTitle(string $title): void
    {
        $titleLength = strlen($title);
        if ($titleLength > $this->placeholderLength - 2) {
            throw new ApplicationErrorException('Title is longer than placeholder.');
        }

        $this->createOutput();
        $placeholderLength = ($this->placeholderLength - $titleLength - 2);
        $placeholderBefore = str_repeat('=', ceil($placeholderLength / 2));
        $placeholderAfter = str_repeat('=', $placeholderLength / 2);
        $this->line($placeholderBefore . ' ' . $title . ' ' . $placeholderAfter);
    }

    public function printSeparator(): void
    {
        $this->createOutput();
        $this->line(str_repeat('-', $this->placeholderLength));
    }

    public function printBoldSeparator(): void
    {
        $this->createOutput();
        $this->line(str_repeat('=', $this->placeholderLength));
    }
}
