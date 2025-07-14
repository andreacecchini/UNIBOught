<?php

namespace middlewares;

use core\Middleware;
use core\Session;
use models\Client;
use models\User;

class CheckReviewMiddleware implements Middleware
{
    /**
     * Gestisce la richiesta verificando se l'utente ha acquistato il prodotto
     */
    public function handle(callable $next): void
    {
        $userSession = Session::get('user');
        $productId = $_GET['product_id'] ?? null;
        if (User::findById(id: $userSession['id'])->isClient() && $productId) {
            if (!Client::findById(id: $userSession['id'])->hasPurchased($productId)) {
                // 403
                $this->sendUnauthorizedResponse();
                return;
            }
            $next();
        } else {
            $this->sendUnauthorizedResponse();
        }
    }

    /**
     * Invia una risposta di errore 403 (non autorizzato)
     */
    private function sendUnauthorizedResponse(): void
    {
        $isAjaxRequest = $this->isAjaxRequest();

        if ($isAjaxRequest) {
            $this->sendJsonErrorResponse();
        } else {
            $this->redirectToErrorPage();
        }
    }

    /**
     * Verifica se la richiesta è una chiamata AJAX
     */
    private function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Invia una risposta JSON con errore 403
     */
    private function sendJsonErrorResponse(): void
    {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode([
            'error' => 'Forbidden',
            'message' => 'Non autorizzato'
        ]);
        exit;
    }

    /**
     * Reindirizza alla pagina di errore 403
     */
    private function redirectToErrorPage(): void
    {
        http_response_code(403);
        header('Location: /403');
        exit;
    }
}