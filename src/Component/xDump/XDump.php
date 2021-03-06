<?php
/**
 * @author Vinicius Feitosa da Silva <viniciusfesil@gmail.com>
 * Date: 22/04/2016
 * Time: 13:09
 */

namespace Component\xDump;

class XDump
{

    const OUTPUT_MODE_HTML = 0;
    const OUTPUT_MODE_CLI = 1;

    private $value;
    private $arrBacktrace;
    public $hasExecutionStopped = false;
    public $hasDetails = false;
    public $hasBacktraceShowed = true;
    public $outputMode = XDump::OUTPUT_MODE_HTML;

    public function __construct($value, $arrBacktrace)
    {
        $this->value = $value;
        $this->arrBacktrace = $arrBacktrace;
    }

    public function output()
    {
        switch ($this->outputMode) {
            case XDump::OUTPUT_MODE_HTML:
                echo $this->outputAsHTML();
                break;
            case XDump::OUTPUT_MODE_CLI:
                echo $this->outputAsCLI();
                break;
        }
        if ($this->hasExecutionStopped) {
            die;
        }
    }

    private function outputAsHTML()
    {
        $dump = '<div style="text-align:left">';
        $dump .= '<pre>';
        $dump .= '<div style="background:lightgray; color:black;">';

        foreach ($this->arrBacktrace[0]['args'] as $index => $value) {
            $dump .= "<div style='border: 1px solid black'>[Value " . ($index + 1) . "]</div>";
            $dump .= "<div style='background:black; color:white; border: 1px solid black'>";
            ob_start();
            if ($this->hasDetails) {
                var_dump($value);
            } else {
                print_r($value);
            }
            $dump .= ob_get_contents();
            ob_end_clean();
            $dump .= '</div>';
        }
        if ($this->hasBacktraceShowed) {
            foreach ($this->arrBacktrace as $backtrace) {
                $line = isset($backtrace['line']) ? $backtrace['line'] : "";
                $file = isset($backtrace['file']) ? $backtrace['file'] : "";
                $dump .= "<div style='background:brown; color:white; border: 1px solid black'>[Line] {$line} {$file}</div>";
                $dump .= "<div style='background:black; color:white; border: 1px solid black'> {$backtrace['function']}</div>";
            }
        }

        return $dump;
    }

    private function outputAsCLI()
    {
        $dump = '\n ==================================';
        $spaces = '    ';
        foreach ($this->arrBacktrace[0]['args'] as $index => $value) {
            $dump .= "\n {$spaces} '---> [Value " . ($index + 1) . " \n\n ";
            ob_start();
            if ($this->hasDetails) {
                var_dump($value);
            } else {
                print_r($value);
            }
            $dump .= "\n ";
            $dump .= ob_get_contents();
            $dump .= "\n ";
            ob_end_clean();
            $dump .= "\n {$spaces} ---------------";
        }

        if ($this->hasBacktraceShowed) {
            $dump .= "\n {$spaces} '-> [ BACKTRACE ] ---------------";
            foreach ($this->arrBacktrace as $backtrace) {
                $line = isset($backtrace['line']) ? $backtrace['line'] : "";
                $file = isset($backtrace['file']) ? $backtrace['file'] : "";
                $dump .= "\n {$spaces} '---> [Line: {$line}] [File: {$file}]";
                $dump .= "\n {$spaces} '------->  {$backtrace['function']}";
            }
        }
        $dump .= '\n ==================================';

        return $dump;
    }

}