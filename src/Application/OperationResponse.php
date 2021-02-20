<?php

namespace Storytale\CustomerActivity\Application;

class OperationResponse implements \JsonSerializable
{
    /** @var bool */
    private bool $success;

    /** @var array|null */
    private ?array $result;

    /** @var string|null */
    private ?string $message;

    /** @var int|null */
    private ?int $code;

    /**
     * OperationResponse constructor.
     * @param bool $success
     * @param array|null $result
     * @param string|null $message
     * @param int|null $code
     */
    public function __construct(bool $success, ?array $result = null, ?string $message = null, ?int $code = null)
    {
        $this->success = $success;
        $this->result = $result;
        $this->message = $message;
        $this->code = $code;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return array|null
     */
    public function getResult(): ?array
    {
        return $this->result;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @return int|null
     */
    public function getCode(): ?int
    {
        return $this->code;
    }

    public function jsonSerialize()
    {
        return [
            'success' => $this->success,
            'result' => $this->result,
            'message' => $this->message,
            'code' => $this->code,
        ];
    }
}