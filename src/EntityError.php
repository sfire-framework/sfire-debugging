<?php
/**
 * sFire Framework (https://sfire.io)
 *
 * @link      https://github.com/sfire-framework/ for the canonical source repository
 * @copyright Copyright (c) 2014-2020 sFire Framework.
 * @license   http://sfire.io/license BSD 3-CLAUSE LICENSE
 */

declare(strict_types=1);

namespace sFire\Debugging;

use sFire\Entity\EntityAbstract;
use DateTime;


/**
 * Class EntityError
 * @package sFire\Debugging
 */
class EntityError extends EntityAbstract {


    /**
     * Contains the error message
     * @var null|string
     */
    private ?string $message = null;


    /**
     * Contains the line of where the error occurred
     * @var null|string
     */
    private ?string $line = null;


    /**
     * Contains the IP address which triggered the error
     * @var null|string
     */
    private ?string $ip = null;


    /**
     * Contains a Datetime instance
     * @var null|Datetime
     */
    private ?Datetime $date = null;


    /**
     * Contains an array of every variable that existed in the scope the error was triggered in
     * @var array
     */
    private array $context = [];


    /**
     * Contains the file where the error occurred
     * @var null|string
     */
    private ?string $file = null;


    /**
     * Contains the error type (Parse, Fatal, Warning, Notice, User -error)
     * @var null|string
     */
    private ?string $type = null;


    /**
     * Contains the level of the error raised
     * @var null|string
     */
    private ?string $number = null;


    /**
     * Contains the debug_backtrace content
     * @var array
     */
    private array $backtrace = [];


    /**
     * Set the error message
     * @param string $message
     * @return self
     */
    public function setMessage(string $message): self {

        $this -> message = $message;
        return $this;
    }


    /**
     * Returns the error message
     * @return null|string
     */
    public function getMessage(): ?string {
        return $this -> message;
    }


    /**
     * Sets the line number the error was raised at
     * @param string $line
     * @return self
     */
    public function setLine(string $line): self {

        $this -> line = $line;
        return $this;
    }


    /**
     * Returns the line number the error was raised at
     * @return string
     */
    public function getLine(): string {
        return $this -> line;
    }


    /**
     * Sets the ip address which triggered the error
     * @param string $ip
     * @return self
     */
    public function setIp(string $ip): self {

        $this -> ip = $ip;
        return $this;
    }


    /**
     * Returns the ip which triggered the error
     * @return null|string
     */
    public function getIp(): ?string {
        return $this -> ip;
    }


    /**
     * Sets the date when the error occurred
     * @param \Datetime $date
     * @return self
     */
    public function setDate(DateTime $date): self {

        $this -> date = $date;
        return $this;
    }


    /**
     * Returns the date when the error occurred
     * @return null|Datetime
     */
    public function getDate(): ?Datetime {
        return $this -> date;
    }


    /**
     * Sets the file where the error occurred
     * @param string $file
     * @return self
     */
    public function setFile(string $file): self {

        $this -> file = $file;
        return $this;
    }


    /**
     * Returns the file where the error occurred
     * @return null|string
     */
    public function getFile(): ?string {
        return $this -> file;
    }


    /**
     * Sets the level of the error raised
     * @param string $number
     * @return self
     */
    public function setNumber(string $number): self {

        $this -> number = $number;
        return $this;
    }


    /**
     * Returns the level of the error raised
     * @return null|string
     */
    public function getNumber(): ?string {
        return $this -> number;
    }


    /**
     * Sets an array of every variable that existed in the scope the error was triggered in
     * @param array $context
     * @return self
     */
    public function setContext(array $context = []): self {

        $this -> context = $context;
        return $this;
    }


    /**
     * Returns an array of every variable that existed in the scope the error was triggered in
     * @return null|array
     */
    public function getContext(): ?array {
        return $this -> context;
    }


    /**
     * Sets the error type (Parse, Fatal, Warning, Notice, User -error)
     * @param string $type
     * @return self
     */
    public function setType(string $type): self {

        $this -> type = $type;
        return $this;
    }


    /**
     * Returns the error type (Parse, Fatal, Warning, Notice, User -error)
     * @return null|string
     */
    public function getType(): ?string {
        return $this -> type;
    }


    /**
     * Sets the debug_backtrace content
     * @param array $backtrace
     * @return self
     */
    public function setBacktrace(array $backtrace): self {

        $this -> backtrace = $backtrace;
        return $this;
    }


    /**
     * Returns the debug_backtrace content
     * @return null|array
     */
    public function getBacktrace(): ?array {
        return $this -> backtrace;
    }
}