<?php

namespace YepSQL;

use PDO;

class Builder
{
    private $pdo;
    private $queries = [];

    /**
     * @param  object      $pdo      instance of PDO
     * @param  string      $filepath the path to file with queries
     */
    function __construct(PDO $pdo, $filepath = null)
    {
        $this->pdo = $pdo;
        if (!is_null($filepath)) {
            $this->loadFromFile($filepath);
        }
    }

    /**
     * Query call: $yepsql_instance->query_name($args);
     * @param  string    $name - query name
     * @param  array     $args - query params
     */
    public function __call($name, array $args = [])
    {
        if (!isset($this->queries[$name])) {
            throw new BuilderException('Query "'. $name .'" does not exist', 4);
        }
        $r = $this->pdo->prepare($this->queries[$name]);
        $r->execute($args);
        return $r;
    }

    /**
     * Creates a query from a file
     * @param  string   $path - path to file
     */
    public function loadFromFile($filepath)
    {
        if (!file_exists($filepath)) {
            throw new BuilderException("File not exists", 1);
        }
        $data = explode("\n", file_get_contents($filepath));
        $tag = [];
        foreach ($data as $line => $string) {
            // // remove extra characters (windows \r)
            $string = str_replace("\r", '', $string);
            if (preg_match("/^\s*--\s*name:\s*([a-zA-Z0-9_-]+)/", $string, $name)) {
                // complete previous query
                if (!empty($tag)) $this->createQueryWithTags($tag);
                // new query
                $tag = [
                    'name' => strtr($name[1], '-', '_'),
                    'query' => null
                ];
            } elseif (preg_match('/^\s*--/', $string) || empty($string)) {
                // sql comment or empty line
                // do nothing
            } elseif (empty($tag) && !preg_match('/^\s*--/', $string)) {
                // invalid file:
                throw new BuilderException('Parse error: the query definition without a "name:" line '.$line, 2);
            } else {
                // new query line
                $tag['query'] .= $string."\n";
            }
        }
        $this->createQueryWithTags($tag);
        return $this->queries;
	}

    /**
     * Create new query
     * @param  array       $tag - request [name, query]
     * @return array            - new empty array
     */
    private function createQueryWithTags(array $tag)
    {
        if (empty($tag['query'])) {
            throw new BuilderException('Query "'.$tag['name'].'" is empty!', 3);
        }
        $this->queries[$tag['name']] = $tag['query'];
    }

    /**
     * return query body
     * @param  string   $name  - request name
     * @return string   $query - request code
     */
    public function getQuery($name)
    {
        return isset($this->queries[$name]) ? $this->queries[$name] : false;
    }
}
