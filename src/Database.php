<?php
namespace Shiftpi\StoreConsent;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Database implements MiddlewareInterface
{
    protected $connection;

    public function __construct($connectionString)
    {
        $this->connection = new \PDO($connectionString);
    }

    public function store($visitorId, array $data)
    {
        $stmt = $this->connection->prepare('REPLACE INTO consent VALUES (:visitorId, :data, CURRENT_TIMESTAMP)');
        $stmt->bindValue(':visitorId', $visitorId);
        $stmt->bindValue(':data', json_encode($data));

        $stmt->execute();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->store($request->getAttribute('route')->getArgument('visitor_id'), $request->getParsedBody());

        return $handler->handle($request);
    }
}