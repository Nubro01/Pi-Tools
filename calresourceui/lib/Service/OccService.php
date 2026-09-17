<?php

declare(strict_types=1);

namespace OCA\CalResourceUI\Service;

/**
 * Shells out to this Nextcloud instance's own `occ`, using paths resolved at
 * runtime (\OC::$SERVERROOT / PHP_BINARY) so the same app works unmodified on
 * any instance it's installed on.
 */
class OccService {
    private string $occPath;
    private string $phpBinary;

    public function __construct() {
        $this->occPath = \OC::$SERVERROOT . '/occ';
        // Under php-fpm, PHP_BINARY can point at the php-fpm executable itself
        // rather than a CLI-usable php binary, which breaks occ. Prefer the
        // known-good CLI binary and only fall back to PHP_BINARY if it's absent.
        $this->phpBinary = is_executable('/usr/bin/php') ? '/usr/bin/php' : PHP_BINARY;
    }

    /** @return array{0: string, 1: string, 2: int} [stdout, stderr, exitCode] */
    public function run(array $args): array {
        $cmd = array_merge([$this->phpBinary, $this->occPath], $args);
        $cmdline = implode(' ', array_map('escapeshellarg', $cmd));

        $descriptors = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $proc = proc_open($cmdline, $descriptors, $pipes);
        if (!is_resource($proc)) {
            return ['', 'Kon occ niet starten', 1];
        }
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exit = proc_close($proc);
        return [$stdout, $stderr, $exit];
    }

    /** @return array{0: array, 1: string} [parsed sections, stderr] */
    public function listResources(): array {
        [$stdout, $stderr] = $this->run(['calendar-resource:resources:list']);
        return [self::parse($stdout), $stderr];
    }

    /** Parses the Symfony-console-table output of `resources:list`. */
    public static function parse(string $output): array {
        $sections = [];
        $current = null;
        $header = null;
        foreach (explode("\n", $output) as $line) {
            $line = rtrim($line, "\r");
            if (preg_match('/^([A-Za-z ]+):$/', $line, $m)) {
                $current = $m[1];
                $sections[$current] = [];
                $header = null;
                continue;
            }
            if ($current === null) {
                continue;
            }
            if ($line === '' || $line[0] === '+') {
                continue;
            }
            if ($line[0] === '|') {
                $cells = array_map('trim', explode('|', trim($line, '|')));
                if ($header === null) {
                    $header = $cells;
                } else {
                    $row = [];
                    foreach ($header as $i => $h) {
                        $row[$h] = $cells[$i] ?? '';
                    }
                    $sections[$current][] = $row;
                }
            }
        }
        return $sections;
    }
}
