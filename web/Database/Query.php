<?php

namespace \Database;

class Query
{
    private string $query;

    public function delete(): Query
    {
        $this->query = "DELETE ";
        return $this;
    }

    public function where(string $requirement): Query
    {
        $this->query .= " WHERE " . $requirement;
        return $this;
    }

    public function from(string $table): Query
    {
        $this->query .= " FROM " . $table;
        return $this;
    }

    public function update(string $table): Query
    {
        $this->query = "UPDATE " . $table;
        return $this;
    }

    public function set(array $columns): Query
    {
        if (count($columns) == 0) {
            die("Invalid argument count.");
        }

        $qry .= " SET ";
        foreach ($columns as $column) {
            if (!is_string($column)) {
                die("Only strings allowed.");
            }

            $qry .= $column . " = ?,";
        }
        $this->query .= substr_replace($qry, '', -1);

        return $this;
    }

    public function insert(string $table): Query
    {
        $this->query = "INSERT INTO " . $table;
        return $this;
    }

    public function columns(array $columns): Query
    {

        if (count(columns) > 0) {
            $this->query .= " (" . implode(",", $columns) . ")"
        }
    }

    public function values(array $params): Query
    {
        if (count($params) == 0) {
            die("Invalid parameter count.");
        }

        $this->query .= " VALUES (";

        foreach ($params as $param) {
            $this->query .= "?,";
        }

        $this->query = substr_replace($this->query, '', -1);
        $this->query .= ");";

        return $this;
    }

    public function select(array $columns)
    {
        if ($columns == null || count($columns) == 0) {
            $this->query .= "*";
        } else {
            foreach ($columns as $column) {
                if (!is_string($column) {
                    die("Columns must be strings.");
                }
            }

            $this->query .= implode(",", $columns);
        }

        return $this;
    }

    public function getQuery(): string
    {
        return $this->query;
    }
}
