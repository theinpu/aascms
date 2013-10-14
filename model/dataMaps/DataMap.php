<?php
/**
 * User: anubis
 * Date: 08.10.13 17:13
 */

namespace aascms\model\dataMaps;

use aascms\lib\PDOHelper;
use aascms\model\exceptions\NullException;
use aascms\model\Model;
use PDO;
use PDOStatement;

abstract class DataMap {

    const ASC = 'ASC';
    const DESC = 'DESC';

    /**
     * @var PDOStatement
     */
    protected $insertStatement = null;
    /**
     * @var PDOStatement
     */
    protected $updateStatement = null;
    /**
     * @var PDOStatement
     */
    protected $deleteStatement = null;
    /**
     * @var PDOStatement
     */
    protected $findOneStatement = null;

    /**
     * @var string
     */
    protected $class = '';

    /**
     * @var PDO
     */
    private $pdo = null;

    function __construct() {
        $this->pdo = PDOHelper::getPDO();
    }

    /**
     * @return PDO
     */
    protected final function getPDO() {
        return $this->pdo;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function delete($id) {
        $this->checkStatement($this->deleteStatement);
        $this->deleteStatement->bindValue(':id', $id);

        return $this->deleteStatement->execute();
    }

    /**
     * @param $id
     *
     * @return Model|null
     */
    public function findById($id) {
        $this->checkStatement($this->findOneStatement);
        $this->findOneStatement->bindValue(':id', $id);
        $this->findOneStatement->execute();
        $item = empty($this->class)
            ? $this->findOneStatement->fetch(PDO::FETCH_ASSOC)
            : $this->findOneStatement->fetchObject($this->class);
        $this->findOneStatement->closeCursor();
        if($item === false) {
            return null;
        }
        else {
            $this->itemCallback($item);

            return $item;
        }
    }

    /**
     * @param Model $object
     */
    protected function insert($object) {
        $this->checkStatement($this->insertStatement);
        $this->insertBindings($object);
        $this->insertStatement->execute();
        $object->setId($this->getPDO()->lastInsertId());
    }

    /**
     * @param Model $object
     */
    protected function update($object) {
        $this->checkStatement($this->updateStatement);
        $this->updateBindings($object);
        $this->updateStatement->bindValue(':id', $object->getId());
        $this->updateStatement->execute();
    }

    /**
     * @param Model $object
     */
    public function save($object) {
        $object = $this->prepareItem($object);
        if(is_null($object->getId())) {
            $this->insert($object);
            $this->onInsert($object);
        }
        else {
            if($object->isChanged()) {
                $this->update($object);
                $this->onUpdate($object);
            }
        }
        $this->onSave($object);
        $object->onSave();
    }

    /**
     * @param Model $item
     */
    protected function itemCallback($item) {
        $this->setItemId($item);
    }

    /**
     * @param string $sql
     *
     * @return \PDOStatement
     */
    protected final function prepare($sql) {
        return $this->getPDO()->prepare($sql);
    }

    /**
     * @param Model $item
     */
    protected function insertBindings($item) { }

    /**
     * @param Model $item
     */
    protected function updateBindings($item) { }

    /**
     * @throws \aascms\model\exceptions\NullException
     */
    protected final function checkStatement($statement) {
        if(is_null($statement)) {
            throw new NullException;
        }
    }

    /**
     * @param Model $item
     *
     * @return Model
     */
    protected function prepareItem($item) {
        return $item;
    }

    /**
     * @param Model $item
     */
    protected function onSave($item) { }

    /**
     * @param Model $item
     */
    protected function onInsert($item) { }

    /**
     * @param Model $item
     */
    protected function onUpdate($item) { }

    protected function fetchAll($sql, $bindings = array()) {
        $stmt = $this->prepare($sql);
        if(count($bindings) > 0) {
            foreach($bindings as $key => $value) {
                $stmt->bindValue($key, $value);
            }
        }
        $stmt->execute();
        $items = $stmt->fetchAll(\PDO::FETCH_CLASS, $this->class);
        foreach($items as &$item) {
            $this->itemCallback($item);
        }

        return $items;
    }

    /**
     * @param $from
     * @param $count
     *
     * @return string
     */
    protected final function getLimitString($from, $count) {
        $limits = '';
        if($count > -1) {
            $limits = ' LIMIT ';
            if($from > 0) {
                $limits = $limits.$from.', ';
            }
            $limits = $limits.$count;

            return $limits;
        }

        return $limits;
    }

    /**
     * @param Model $item
     */
    private function setItemId($item) {
        if(is_null($item->getId())) {
            $item->setId($item->{'id'});
            unset($item->{'id'});
        }
    }
}