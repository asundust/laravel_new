<?php


namespace App\Handlers;


use BeyondCode\QueryDetector\Outputs\Output;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log as LaravelLog;
use Symfony\Component\HttpFoundation\Response;

class QueryDetectorLogHandler implements Output
{
    public function boot()
    {
        //
    }

    public function output(Collection $detectedQueries, Response $response)
    {
        LaravelLog::channel('query_detector')->info('Detected N+1 Query');

        foreach ($detectedQueries as $detectedQuery) {
            $logOutput = 'Model: ' . $detectedQuery['model'] . PHP_EOL;

            $logOutput .= 'Relation: ' . $detectedQuery['relation'] . PHP_EOL;

            $logOutput .= 'Num-Called: ' . $detectedQuery['count'] . PHP_EOL;

            $logOutput .= 'Call-Stack:' . PHP_EOL;

            foreach ($detectedQuery['sources'] as $source) {
                $logOutput .= '#' . $source->index . ' ' . $source->name . ':' . $source->line . PHP_EOL;
            }

            LaravelLog::channel('query_detector')->info($logOutput);
        }
    }
}
