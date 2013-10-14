<?php
/**
 * User: anubis
 * Date: 10.10.13 11:31
 */

namespace aascms\gens;

class Generator {

    private $table;
    private $class;
    private $fields = array();

    public function __construct($class) {
        $this->class = new \ReflectionClass($class);;
    }

    public function generate($toFile = true) {
        echo "generate data map for ".$this->class->name." ...\r\n";
        $this->table = $this->getAnnotation('table', $this->class->getDocComment());

        echo "prepare fields...\r\n";
        $this->prepareFields($this->class);
        if(count($this->fields) == 0) {
            echo "nothing to export\r\n";

            return;
        }
        echo "check setters and getters...\r\n";
        $this->findGettersAndSetters();

        echo "create bindings...\r\n";
        $insertSql = $this->generateInsert();
        $updateSql = $this->generateUpdate();
        $deleteSql = "DELETE FROM ".$this->table." WHERE id=:id";
        $findOneSql = "SELECT * FROM ".$this->table." WHERE id=:id";
        $insertBindings = $this->generateInsertBindings();
        $updateBindings = $this->generateUpdateBindings();

        $fullClassName = $this->class->getName();
        $className = $this->class->getShortName();

        echo "generate data map...\r\n";
        $tpl = file_get_contents(__DIR__."/DataMap.tpl");

        $out = str_replace("%className%", $className, $tpl);
        $out = str_replace("%fullClassName%", $fullClassName, $out);
        $out = str_replace("%fullClassNameS%", addslashes($fullClassName), $out);
        $out = str_replace("%insertSql%", $insertSql, $out);
        $out = str_replace("%updateSql%", $updateSql, $out);
        $out = str_replace("%deleteSql%", $deleteSql, $out);
        $out = str_replace("%findOneSql%", $findOneSql, $out);
        $out = str_replace("%insertBindings%", $insertBindings, $out);
        $out = str_replace("%updateBindings%", $updateBindings, $out);

        $file = __DIR__."/../model/dataMaps/".$className."DataMap.php";

        if($toFile) {
            @unlink($file);
            file_put_contents($file, $out);
        }
        else {
            echo "---------\r\n";
            echo $file."\r\n";
            echo $out."\r\n";
        }

        echo "done\r\n";
    }

    private function getAnnotation($name, $comment) {
        preg_match_all('/@'.$name.'(.*?)\n/s', $comment, $annotations, PREG_SET_ORDER);
        if(isset($annotations[0])) {
            return trim($annotations[0][1]);
        }

        return null;
    }

    private function addGetter($name, $prefix = 'get') {
        $field = ($prefix == 'get')
            ? lcfirst(substr($name, 3))
            : $prefix;
        if(!array_key_exists($field, $this->fields)) {
            return;
        }
        $this->fields[$field]['get'] = $name;
    }

    private function addSetter($name, $prefix = 'set') {
        $field = ($prefix == 'set')
            ? lcfirst(substr($name, 3))
            : $prefix;
        if(!array_key_exists($field, $this->fields)) {
            return;
        }
        $this->fields[$field]['set'] = $name;
    }

    private function generateInsert() {
        $sql = "INSERT INTO ".$this->table." (";
        $fields = array_keys($this->fields);
        $names = array();
        $binds = array();
        foreach($fields as $field) {
            if($field == "id") continue;
            $names[] = $field;
            $binds[] = ':'.$field;
        }
        $sql .= implode(', ', $names);
        $sql .= ") VALUES (".implode(', ', $binds);
        $sql .= ")";

        return $sql;
    }

    private function generateUpdate() {
        $sql = "UPDATE ".$this->table." SET ";
        $fields = array_keys($this->fields);
        $sets = array();
        foreach($fields as $field) {
            if($field == "id") continue;
            if($this->fields[$field]['readonly']) continue;
            $sets[] = $field."=:".$field;
        }
        $sql .= implode(', ', $sets);
        $sql .= " WHERE id=:id";

        return $sql;
    }

    private function generateInsertBindings() {
        $bindings = array();
        foreach($this->fields as $field => $methods) {
            if($field == 'id') continue;
            if(!array_key_exists('get', $methods)) continue;
            $bindings[] = "\t\t".'$this->insertStatement->bindValue(":'.$field.'", $item->'.$methods['get'].'());';
        }

        return implode("\n", $bindings);
    }

    private function generateUpdateBindings() {
        $bindings = array();
        foreach($this->fields as $field => $methods) {
            if($field != 'id') {
                if($methods['readonly']) continue;
                if(!array_key_exists('get', $methods)) continue;
            }
            $bindings[] = "\t\t".'$this->updateStatement->bindValue(":'.$field.'", $item->'.$methods['get'].'());';
        }

        return implode("\n", $bindings);
    }

    /**
     * @param \ReflectionClass $class
     */
    private function prepareFields($class) {
        $fields = $class->getProperties();
        foreach($fields as $field) {
            $export = $this->getAnnotation('export', $field->getDocComment());
            if(!is_null($export)) {
                $this->fields[$field->name] = array();
                $this->fields[$field->name]['readonly'] = ($export == 'readonly' || $export == 'ro');
            }
            $convert = $this->getAnnotation('convert', $field->getDocComment());
            if(!is_null($convert)) {
                $this->fields[$field->name]['convert'] = $convert;
            }
        }
        if($class->getParentClass()) {
            $this->prepareFields($class->getParentClass());
        }
    }

    private function findGettersAndSetters() {
        $methods = $this->class->getMethods();
        foreach($methods as $method) {
            $name = $method->name;
            $get = $this->getAnnotation('get', $method->getDocComment());
            if(!is_null($get)) {
                $this->addGetter($name, $get);
            }
            $set = $this->getAnnotation('set', $method->getDocComment());
            if(!is_null($set)) {
                $this->addSetter($name, $set);
            }
            if(strpos($name, 'get') === 0) {
                $this->addGetter($name);
            }
            if(strpos($name, 'set') === 0) {
                $this->addSetter($name);
            }
        }
    }
}