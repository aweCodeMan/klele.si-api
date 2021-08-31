<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            if ($this->shouldReport($e) && $this->shouldSendEmail($e)) {
                $this->sendEmail($e);
            }
        });
    }

    private function shouldSendEmail(Throwable $e)
    {
        return !config('app.debug');
    }

    private function sendEmail(Throwable $e)
    {
        try {
            $request = $this->container->get(Request::class);
            $user = $request->user();
            $url = $request->fullUrl();
            $flatten = FlattenException::createFromThrowable($e);
            $handler = new HtmlErrorRenderer(true);
            $css = $handler->getStylesheet();
            $content = $handler->getBody($flatten);

            Mail::send('emails.exception', compact('css', 'content'), function ($message) use ($url, $user) {
                $subject = 'Exception: ' . $url;

                if ($user) {
                    $email = $user->email;
                    $subject .= " ($email)";
                }

                $message->to(['miha@klele.si'])
                    ->subject($subject);
            });
        } catch (Throwable $throwable) {
        }
    }
}
