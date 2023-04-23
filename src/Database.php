<?php
namespace Shiftpi\StoreConsent;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Database implements MiddlewareInterface
{
    protected $connection;

    public function __construct(string $connectionString, string $user, string $password)
    {
        $this->connection = new \PDO($connectionString, $user, $password);
    }

    public function store(string $visitorId, array $data): void
    {
        $stmt = $this->connection->prepare(
            'REPLACE INTO consent (visitor_id, settings, last_change) VALUES (:visitorId, :data, CURRENT_TIMESTAMP)'
        );
        $stmt->bindValue(':visitorId', $visitorId);
        $stmt->bindValue(':data', json_encode($data));

        $stmt->execute();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->store($request->getAttribute('__route__')->getArgument('visitor_id'), $request->getParsedBody());

        return $handler->handle($request);
    }
}
