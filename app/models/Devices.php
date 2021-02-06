<?php

class Devices extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $uid;

    /**
     *
     * @var integer
     */
    public $appId;

    /**
     *
     * @var string
     */
    public $language;

    /**
     *
     * @var string
     */
    public $os;

    /**
     *
     * @var string
     */
    public $client_token;

    /**
     *
     * @var string
     */
    public $register_date;

    /**
     *
     * @var string
     */
    public $create_date;

    /**
     *
     * @var string
     */
    public $update_date;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("teknasyon");
        $this->setSource("devices");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Devices[]|Devices|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): \Phalcon\Mvc\Model\ResultsetInterface
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Devices|\Phalcon\Mvc\Model\ResultInterface
     */

}
