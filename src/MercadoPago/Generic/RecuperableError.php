<?php
 
namespace MercadoPago;

class RecuperableError {
    /**
     * @var string
     */
    public string $message = "";

    /**
     * @var string
     */
    public string $status = "";

    /**
     * @var string
     */
    public string $error = "";

    /**
     * @var array
     */
    public array $causes = [];

    /**
     * @param $message
     * @param $error
     * @param $status
     */
    function __construct($message, $error, $status) {
        $this->message = $message;
        $this->status = $status;
        $this->error = $error;
    }

    /**
     * @param $code
     * @param $description
     * @return void
     */
    public function add_cause($code, $description): void
    {
        $error_cause = new ErrorCause();
        $error_cause->code = $code;
        $error_cause->description = $description;
        $this->causes[] = $error_cause;
    }

    /**
     * @param $causes
     * @return void
     */
    public function proccess_causes($causes): void
    {
        if(isset($causes['code']) && isset($causes['description'])){
            $this->add_cause($causes['code'], $causes['description']);
        }else{
            foreach ($causes as $cause){
                if(is_array($cause) && (!isset($cause['code']) && !isset($cause['description']))){
                    $this->proccess_causes($cause);
                }else{
                    $this->add_cause($cause['code'], $cause['description']);
                }
            }
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->error . ": " . $this->message;
    }
}
