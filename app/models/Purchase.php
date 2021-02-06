<?php

class Purchase extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $client_token;

    /**
     *
     * @var string
     */
    public $receipt_hash;

    /**
     *
     * @var string
     */
    public $platform;

    /**
     *
     * @var string
     */
    public $platform_response;

    /**
     *
     * @var string
     */
    public $status;

    /**
     *
     * @var string
     */
    public $expire_date;

    /**
     *
     * @var string
     */
    public $expire_date_utc;

    /**
     *
     * @var string
     */
    public $request_date;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("teknasyon");
        $this->setSource("purchase");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Purchase[]|Purchase|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): \Phalcon\Mvc\Model\ResultsetInterface
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Purchase|\Phalcon\Mvc\Model\ResultInterface
     */

}
