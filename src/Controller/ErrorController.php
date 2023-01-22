<?php

namespace App\Controller;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
class ErrorController extends GeneralController
{
    public function show(FlattenException $exception, DebugLoggerInterface $logger = null): Response
    {
        // dd($exception->getStatusCode());

        if($exception->getStatusCode() === 404) {
            return $this->render('bundles/TwigBundle/Exception/error404.html.twig', [
                "code" => $exception->getStatusCode(),
                "message" =>$exception->getStatusText(),
                'general' => $this->general
            ]);
        } elseif($exception->getStatusCode() === 500) {
            return $this->render('bundles/TwigBundle/Exception/error500.html.twig', [
                "code" => $exception->getStatusCode(),
                "message" =>$exception->getStatusText(),
                'general' => $this->general
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error.html.twig', [
                "code" => $exception->getStatusCode(),
                "message" =>$exception->getStatusText(),
                'general' => $this->general
            ]);
        }
    }
}
