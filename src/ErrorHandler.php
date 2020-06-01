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

use DateTime;
use sFire\Http\Request;
use sFire\Logging\Adapter\FileAdapter;
use sFire\Debugging\Exception\BadMethodCallException;


/**
 * Class ErrorHandler
 * @package sFire\Debugging
 */
class ErrorHandler {


	/**
	 * Contains an instance of EntityError
	 * @var EntityError
	 */
	private ?EntityError $error;


	/**
	 * Contains an instance of Logger
	 * @var FileAdapter
	 */
	private ?FileAdapter $logger = null;


	/**
	 * Contains a path to a directory
	 * @var string
	 */
	private ?string $directory = null;


	/**
	 * Contains all the debugging options
	 * @var array 
	 */
	private array $options = [

		'write' 	=> true, 
		'display' 	=> true, 
		'ip'		=> [],
		'types' 	=> ['date', 'ip', 'message', 'line', 'number', 'context', 'type', 'backtrace']
	];


	/**
     * Constructor
     */
	public function __construct() {

		set_error_handler([$this, 'errorHandler']);
		set_exception_handler([$this, 'exceptionHandler']);
	}


    /**
     * Error handler
     * @param string $number The level of the error raised
     * @param string $message The error message
     * @param string $file The file where the error occurred
     * @param string $line The line of where the error occurred
     * @param array $context An array of every variable that existed in the scope the error was triggered in
     */
	public function errorHandler($number, string $message, string $file, string $line, array $context): void {

		if(0 === error_reporting()) {
			return;
		}

		$error = new EntityError();
		$error -> setFile($file); 
		$error -> setMessage($message); 
		$error -> setNumber((string) $number);
		$error -> setLine((string) $line);
		$error -> setDate(new DateTime()); 
		$error -> setContext($context); 
		$error -> setBacktrace(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5));

		if($ip = $this -> getIp()) {
            $error -> setIp($ip);
        }

		switch($error -> getNumber()) {

			case E_ERROR :
			case E_CORE_ERROR :
			case E_COMPILE_ERROR :
			case E_PARSE :
			case 8 :
				$error -> setType('FATAL');
			break;
			
			case E_USER_ERROR :
			case E_RECOVERABLE_ERROR :
				$error -> setType('ERROR');
			break;
		
			case E_WARNING :
			case E_CORE_WARNING :
			case E_COMPILE_WARNING :
			case E_USER_WARNING :
				$error -> setType('WARNING');
			break;
		
			case E_NOTICE :
			case E_USER_NOTICE :
				$error -> setType('INFO');
			break;
			
			case E_STRICT :
				$error -> setType('STRICT');
			break;
		}

		$this -> error = $error;
		$this -> action();
	}


    /**
     * Error handler
     * @param mixed $exception
     */
	public function exceptionHandler($exception): void {

		if(0 === error_reporting()) {
			return;
		}

		$error = new EntityError();
		$error -> setFile($exception -> getFile()); 
		$error -> setMessage($exception -> getMessage()); 
		$error -> setLine((string) $exception -> getLine());
		$error -> setDate(new DateTime()); 
		$error -> setBacktrace($exception -> getTrace());
		$error -> setNumber((string) $exception -> getCode());
        $error -> setType('Exception');

        if($ip = $this -> getIp()) {
            $error -> setIp($ip);
        }

		$this -> error = $error;
		$this -> action();
	}


    /**
     * Set the log directory where all the log files will be saved
     * @param string $directory
     * @return void
     */
    public function setLogDirectory(string $directory): void {
	    $this -> directory = $directory;
    }


	/**
     * Set debug options
     * @param array $options
     * @return void
     */
	public function setOptions(array $options = []): void {
		$this -> options = array_merge($this -> options, $options);
	}


    /**
     * Determines what to do with the error
     * @return void
     */
	private function action(): void {

		$options = (object) $this -> options;

		//Check if error needs to be logged
		if(true === $options -> write) {
			$this -> writeToFile();	
		}

		//Check if error needs to be displayed in the browser
		if(true == $options -> display) {
			
			$this -> displayError();
			return;
		}

		exit();
	}


    /**
     * Writes current error to log file
     * @return void
     * @throws BadMethodCallException
     */
    private function writeToFile(): void {

        //Check if error log directory has been set
        if(null === $this -> directory) {
            throw new BadMethodCallException('Error needs to be written to a log file but no error log directory has been set. Set the error log directory with the "setLogDirectory()" method');
        }

        $logger = $this -> getLogger();
        $logger -> setDirectory($this -> directory);

        $error = $this -> error -> toJson($this -> options['types']);

        if(false === $error) {

            $this -> error -> setContext(null);
            $this -> error -> setBacktrace(null);
        }

        $logger -> write($error);
    }


	/**
	 * Prints the error to client
	 * @return void
	 */
	private function displayError(): void {

		if(count($this -> options['ip']) === 0 || true === in_array(Request :: getIp(), $this -> options['ip'])) {

			$error = [

				'type'		=> $this -> error -> getType(),
				'text' 		=> $this -> error -> getMessage(),
				'file'		=> $this -> error -> getFile(),
				'line'		=> $this -> error -> getLine(),
				'backtrace' => $this -> formatBacktrace()
			];

			exit('<pre>' . print_r($error, true) . '</pre>');
		}
	}


    /**
     * Formats the backtrace
     * @return array
     */
    private function formatBacktrace(): array {

        $backtrace = $this -> error -> getBacktrace();

        if(null !== $backtrace) {

            foreach($backtrace as $index => $stack) {

                foreach(['type', 'args'] as $type) {

                    if(true === isset($backtrace[$index][$type])) {
                        unset($backtrace[$index][$type]);
                    }
                }
            }
        }

        return $backtrace;
    }


	/**
	 * Initialise new logger and returns it 
	 * @return FileAdapter
	 */
	private function getLogger(): FileAdapter {

		if(null === $this -> logger) {
			$this -> logger = new FileAdapter();
		}

		return $this -> logger;
	}


	/**
	 * Returns the request IP address
	 * @return string|void
	 */
	public static function getIp(): ?string {
		return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP-X-FORWARDED-FOR'] ?? $_SERVER['HTTP_VIA'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
	}
}