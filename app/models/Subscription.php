<?php

//namespace Models;

class Subscription extends \Phalcon\Mvc\Model
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
    public $os;

    /**
     *
     * @var string
     */
    public $expire_date;

    /**
     *
     * @var integer
     */
    public $status;

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
        $this->setSource("subscription");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Subscription[]|Subscription|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): \Phalcon\Mvc\Model\ResultsetInterface
    {
        return parent::find($parameters);
    }
/*
    public function update($filter = NULL, $data = NULL)
    {
        if (isset($filter) && !empty($filter) && isset($filter[0]) && !empty($filter[0]) && isset($filter['bind']) && !empty($filter['bind']) && isset($data) && !empty($data))
        {
            $updated_columns = [];
            foreach ($data as $key => $value) {
                $updated_columns[] = $key . ' = :' . $key . ':';
                $filter['bind'][] = [$key => $value];
            }

            if (isset($updated_columns) && !empty($updated_columns))
            {
                $phql = 'UPDATE subscription SET ' . implode(', ', $updated_columns) . ' WHERE ' . $filter[0];
                $this->modelsManager->executeQuery($phql, $filter['bind']);
            }
        }
    }
*/
    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Subscription|\Phalcon\Mvc\Model\ResultInterface
     */

}
