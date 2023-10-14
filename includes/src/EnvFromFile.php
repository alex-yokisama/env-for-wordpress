<?php 

namespace EnvForWordpress;

class EnvFromFile implements EnvProvider {
    protected string $file_path;
    protected array $values;

    public function __construct(string $file_path) 
    {
        $this->file_path = $file_path;
        $this->values = [];
    }

    public function init(): void
    {
        $this->values = [];

        $file = $this->readFile();
        $lines = $this->splitLines($file);
        $entries = $this->parseLines($lines);

        foreach ($entries as $entry) {
            $this->values[$entry['key']] = $entry['value'];
        }
    }

    protected function readFile(): string 
    {
        if (!file_exists($this->file_path)) {
            return '';
        }

        $file = file_get_contents($this->file_path);
        if (empty($file) || !is_string($file)) {
            return '';
        }

        return $file;
    }

    protected function splitLines(string $file): array 
    {
        $lines = explode("\n", $file);

        $lines = array_values(array_filter($lines, function ($line) {
            return !empty($line) && (strpos(trim($line), '#') !== 0); 
        }));

        return $lines;
    }

    protected function parseLines(array $lines): array 
    {
        $entries = [];

        foreach ($lines as $line) {
            $entries[] = $this->parseLine($line);
        }

        return array_values(array_filter($entries));
    }

    public function parseLine(string $line): ?array
    {
        if (preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*)\s*=\s*(.*)$/', $line, $match)) {
            $key = $match[1];
            $value = $this->parseValue(trim($match[2]));

            return [
                'key' => $key,
                'value' => $value
            ];            
        }

        return null;
    }

    private function parseValue(string $value)
    {
        if (empty($value)) return '';

        $parsed = '';
        $quote = '';

        $first_char = substr($value, 0, 1);
        if ($first_char === '"' || $first_char === '\'') {
            $quote = $first_char;
        }

        $escaped = false;

        if (!empty($quote)) {
            $value = substr($value, 1);
        }        

        foreach (str_split($value, 1) as $char) {            
            if ($escaped) {
                $escaped = false;
                if ($char === $quote) {
                    $parsed .= $char;                        
                    continue;
                } else {
                    $parsed .= '\\' . $char;
                    continue;
                }
            } 

            if (!empty($quote) && $char === '\\') {
                $escaped = true;
                continue;
            }
            

            if (empty($quote) && $char === '#') break;

            if (!empty($quote) && $char === $quote) break;

            $parsed .= $char;
        }

        return empty($quote) ? $this->getTypedValue(trim($parsed)) : $parsed;
    }

    public function getTypedValue(string $value)
    {
        if (strtoupper($value) === 'TRUE') {
            return true;
        }

        if (strtoupper($value) === 'FALSE') {
            return false;
        }

        if ($value === (string) intval($value)) {
            return intval($value);
        }

        if ($value === (string) floatval($value)) {
            return floatval($value);
        }

        return $value;
    }

    public function get(string $key)
    {
        if (!isset($this->values[$key])) return null;

        return $this->values[$key];
    }
}
