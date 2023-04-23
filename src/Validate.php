<?php
namespace Shiftpi\StoreConsent;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpBadRequestException;

class Validate implements MiddlewareInterface
{
    protected $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    protected function isValid(array $data): bool
    {
        if (count($data) !== count($this->fields) + 1) {
            return false;
        }

        foreach ($this->fields as $field) {
            if (!isset($data[$field]) || $data[$field] !== '0' && $data[$field] !== '1') {
                return false;
            }
        }

        if (!isset($data['visitor_id']) || strlen($data['visitor_id']) !== 64){
            return false;
        }

        return true;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = $request->getParsedBody();

        if ($request->getAttribute('__route__')->getArgument('visitor_id')) {
            $body['visitor_id'] = $request->getAttribute('__route__')->getArgument('visitor_id');
        }

        if (!$this->isValid($body)) {
            throw new HttpBadRequestException($request);
        }

        return $handler->handle($request);
    }
}
