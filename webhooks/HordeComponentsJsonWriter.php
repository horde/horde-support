<?php

declare(strict_types=1);

/**
 * Copyright 2026 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @license http://www.horde.org/licenses/lgpl21 LGPL
 */

namespace Horde\Internal\Satisfiend;

use Horde\Satisfiend\Event\WebhookReceivedEvent;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Installation-local listener for dev.horde.org that mirrors every
 * WebhookReceivedEvent into `/horde/json/` in the layout
 * `horde-components web` expects:
 *
 *     {baseDir}/{eventType}/{action}/{owner}/{repo}/{Y-m-d.H:i:s}.json
 *
 * This class exists because `horde-components web` reads a filesystem
 * tree today. Its long-term replacement is expected to be a
 * satisfiend-reader in horde-components tool. 
 *
 * ## baseDir configuration
 *
 * Reads `$_ENV['HORDE_COMPONENTS_JSON_DIR']` if set (useful for
 * testing), otherwise `/horde/json`.
 *
 * satisfiend's ListenerLoader resolves this class via the injector
 * so all parameters must be autowire-ready. Env-first keeps
 * the class instantiable without a factory.
 *
 * ## Idempotence
 *
 * github.com retries failed deliveries. `satisfiend_events` dedupes
 * retries at the row level (unique index on `delivery_id`), but this
 * listener writes to the filesystem with `date('Y-m-d.H:i:s')` as the
 * filename - so a retry a few seconds later produces a second file
 * with a slightly later timestamp. A duplicate looks like an event that
 * happened one second after itself. Not ideal but not incorrect.
 *
 * ## Debug events
 *
 * `$event->debug` is true only for events synthesised by
 * `bin/satisfiend-cli debug inject`. This listener refuses to write
 * for debug events so that developer testing does not contaminate the
 * site's rendered output.
 *
 * ## Failure semantics
 *
 * Write failures throw with error details.
 */
class HordeComponentsJsonWriter
{
    private const DEFAULT_BASE_DIR = '/horde/json';

    public function __construct(
        private readonly LoggerInterface $log,
    ) {}

    public function __invoke(WebhookReceivedEvent $event): void
    {
        if ($event->debug) {
            return;
        }

        if ($event->eventType === '') {
            $this->log->warning('horde-support-json-writer: event has no eventType. Skipping', [
                'delivery_id' => $event->deliveryId,
            ]);
            return;
        }

        $baseDir = $this->baseDir();

        $repository = $event->repository !== '' ? $event->repository : 'unknown/unknown';
        if (!str_contains($repository, '/')) {
            $repository = 'unknown/' . $repository;
        }
        [$owner, $repo] = explode('/', $repository, 2);

        $action = $event->action !== '' ? $event->action : 'other';

        $targetDir = $baseDir
            . DIRECTORY_SEPARATOR . $this->sanitize($event->eventType)
            . DIRECTORY_SEPARATOR . $this->sanitize($action)
            . DIRECTORY_SEPARATOR . $this->sanitize($owner)
            . DIRECTORY_SEPARATOR . $this->sanitize($repo);

        $this->ensureDirectory($targetDir);

        $filename = date('Y-m-d.H:i:s') . '.json';
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $filename;

        $expectedBytes = strlen($event->payload);
        error_clear_last();
        $written = @file_put_contents($filePath, $event->payload);
        if ($written === false || $written !== $expectedBytes) {
            $err = error_get_last();
            $detail = $err['message'] ?? 'file_put_contents failed without a PHP warning';
            if ($written !== false) {
                $detail = sprintf(
                    'incomplete write (%d of %d bytes); %s',
                    $written,
                    $expectedBytes,
                    $detail,
                );
            }
            throw new RuntimeException(sprintf(
                'horde-support-json-writer: Could not write file %s (%d bytes expected): %s',
                $filePath,
                $expectedBytes,
                $detail,
            ));
        }
    }

    /**
     * Create a directory including any missing parents. 
     * Tolerates concurrent creation by parallel webhook deliveries.
     *
     * PHP's `mkdir(..., recursive: true)` returns false when *any*
     * intermediate mkdir hits "File exists" (racing peer), even when
     * the final directory was successfully created.
     *
     * PHP in most installations caches stat() results per-process.
     * A second `is_dir()` right after a 2nd in race can still see "no" 
     * until the cache is cleared.
     *
     * Work around both by walking parents and treating
     * EEXIST as success at every level.
     */
    private function ensureDirectory(string $targetDir): void
    {
        clearstatcache(true, $targetDir);
        if (is_dir($targetDir)) {
            return;
        }

        // Split into path segments, walk from root creating each level.
        $absolute = str_starts_with($targetDir, DIRECTORY_SEPARATOR);
        $parts = array_values(array_filter(explode(DIRECTORY_SEPARATOR, $targetDir), 'strlen'));

        $path = $absolute ? DIRECTORY_SEPARATOR : '';
        foreach ($parts as $segment) {
            $path .= $segment;
            clearstatcache(true, $path);
            if (!is_dir($path)) {
                error_clear_last();
                if (!@mkdir($path, 0o755)) {
                    clearstatcache(true, $path);
                    if (!is_dir($path)) {
                        $err = error_get_last();
                        $detail = $err['message'] ?? 'mkdir failed without a PHP warning';
                        throw new RuntimeException(sprintf(
                            'horde-support-json-writer: could not create directory %s (parent %s, mode 0755): %s',
                            $path,
                            dirname($path),
                            $detail,
                        ));
                    }
                }
            }
            $path .= DIRECTORY_SEPARATOR;
        }
    }

    private function baseDir(): string
    {
        $env = getenv('HORDE_COMPONENTS_JSON_DIR');

        return is_string($env) && $env !== '' ? $env : self::DEFAULT_BASE_DIR;
    }

    /**
     * Directory names come from webhook payload fields that github.com
     * controls but that horde-components will later split on `/` and
     * pass to a filesystem. Clamp to a safe charset so a hostile-but-
     * authenticated actor cannot smuggle path segments through us.
     */
    private function sanitize(string $segment): string
    {
        $clean = preg_replace('/[^A-Za-z0-9._-]/', '_', $segment);

        return $clean === '' || $clean === '.' || $clean === '..' ? 'unknown' : $clean;
    }
}
