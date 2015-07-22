<?php 
/*
|
| @ Package: easyCrud
|
| @ Author: izni burak demirtaş / @izniburak <info@burakdemirtas.org>
| @ Web: http://burakdemirtas.org
| @ URL: https://github.com/izniburak/easy-crud
| @ Licence: The MIT License (MIT) - Copyright (c) - http://opensource.org/licenses/MIT
|
*/

namespace buki;

class easyCrud
{
	private $query = null;
	private $pdo = null;
	private $queryParams = [];
	private $error = null;
	
	public function __construct($object)
	{
		if(strtolower(get_class($object)) == 'pdo')
			$this->pdo = $object;
		else
			throw new \Exception("Opps! Required PDO object.");
	}
	
	public function prepare($sql = [])
    {
	 	if(!empty($sql) && is_array($sql))
        {
            $q = [
				'table' => '',
                'select' => '*',
                'join' => '',
                'where' => '',
                'groupBy' => '',
                'having' => '',
                'orderBy' => '',
                'limit' => '',
				'set' => '',
				'values' => ''
            ];

			if(isset($sql['table']))
            {
                if(is_array($sql['table']))
                    $q['table'] = implode(', ', $sql['table']);
                else
                    $q['table'] = $sql['table'];
            }
			
            if(isset($sql['select']))
            {
                if(is_array($sql['select']))
                    $q['select'] = implode(', ', $sql['select']);
                else
                    $q['select'] = $sql['select'];
            }

            if(isset($sql['join']))
            {
                if(is_array($sql['join'][0]))
                {
                    $q['join'] = '';
                    foreach($sql['join'] as $join)
                    {
                        if(count($join) > 2)
                            $q['join'] .= strtoupper($join[0]) . ' JOIN ' . $join[1] . ' ON ' . $join[2] . ' ';
                        else
                            $q['join'] .= 'JOIN ' . $join[0] . ' ON ' . $join[1] . ' ';
                    }
                }
                else
                {
                    if(count($sql['join']) > 2)
                        $q['join'] = strtoupper($sql['join'][0]) . ' JOIN ' . $sql['join'][1] . ' ON ' . $sql['join'][2];
                    else
                        $q['join'] = 'JOIN ' . $sql['join'][0] . ' ON ' . $sql['join'][1];
                }
            }

	 		if(isset($sql['where']))
            {
                $q['where'] = 'WHERE ';
                if(is_array($sql['where']))
                {
					if(count($sql['where']) == 2 && isset($sql['where'][1]) && is_array($sql['where'][1]))
					{
						$x = explode('?', $sql['where'][0]);
						$where = '';
						foreach($x as $k => $v)
							if(!empty($v))
								$where .= $v . (isset($sql['where'][1][$k]) ? $this->escape($sql['where'][1][$k]) : '');

						$q['where'] .= $where;
					}
					else
					{
						$where = '';
						foreach($sql['where'] as $k => $v)
								$where .= $k . ' = ' . $this->escape($v) . ' AND ';
						
						$q['where'] .= rtrim($where, ' AND ');
					}
                }
                else
                    $q['where'] .= $sql['where'];
            }

            if(isset($sql['groupBy']))
            {
                $q['groupBy'] = 'GROUP BY ';
                if(is_array($sql['groupBy']))
                    $q['groupBy'] .= implode(', ', $sql['groupBy']);
                else
                    $q['groupBy'] .= $sql['groupBy'];
            }

            if(isset($sql['having']))
            {
                $q['having'] = 'HAVING ';
                if(is_array($sql['having']))
                {
                    $x = explode('?', $sql['having'][0]);
                    $having = '';
                    foreach($x as $k => $v)
                        if(!empty($v))
                            $having .= $v . (isset($sql['having'][1][$k]) ? $this->escape($sql['having'][1][$k]) : '');

                    $q['having'] .= $having;
                }
                else
                    $q['having'] .= $sql['having'];
            }

		 	if(isset($sql['orderBy']))
            {
                $q['orderBy'] = 'ORDER BY ';
                if(is_array($sql['orderBy']))
                {
                    if(count($sql['orderBy']) > 1)
                    {
                        foreach($sql['orderBy'] as $key => $value)
                            $q['orderBy'] .= $key . ' ' . strtoupper($value) . ', ';

                        $q['orderBy'] = rtrim($q['orderBy'], ', ');
                    }
                    else
                        $q['orderBy'] .= $sql['orderBy'][0] . ' ' . $sql['orderBy'][1];
                }
                else
                    $q['orderBy'] .= $sql['orderBy'];
            }

		 	if(isset($sql['limit']))
            {
                $q['limit'] = 'LIMIT ';
                if(is_array($sql['limit']))
                    if(count($sql['limit']) > 1)
                        $q['limit'] .= $sql['limit'][0] . ', ' . $sql['limit'][1];
                    else
                        $q['limit'] .= $sql['limit'][0];
                else
                    $q['limit'] .= $sql['limit'];
            }
			
			if(isset($sql['set']))
			{
				$set = '';
				foreach($sql['set'] as $k => $v)
					$set .= $k . ' = ' . $this->escape($v) . ', ';
				
				$q['set'] = rtrim($set, ', ');
			}
			
			if(isset($sql['values']))
			{
				$column = [];
				$values = [];
				foreach($sql['values'] as $k => $v)
				{
					$column[] = $k;
					$values[] = $this->escape($v);
				}
				$q['column'] = implode(', ', $column);
				$q['values'] = implode(', ', $values);
			}

	 		$this->queryParams = $q;
			return $this;
	 	}
	 	else
	 		throw new Exception("Opps! You need to enter parameter.");
	}
	
	public function insert()
	{
		$q = $this->queryParams;
		
		if(!isset($q['column']))
			return false;
		
		$this->query = 'INSERT INTO ' . $q['table'] . ' (' . $q['column'] . ') VALUES (' . $q['values'] . ')';
		
		$result = $this->run();
		if ($result)
			return $this->pdo->lastInsertId();
		
		return false;
	}
	
	public function get($all = false)
	{
		$q = $this->queryParams;

		if(is_array($q) && !empty($q))
		{
			$this->query = $this->space('SELECT ' . $q['select'] . ' FROM ' . $q['table'] . ' ' . $q['join'] .  ' ' . $q['where'] . ' ' . $q['groupBy'] . ' ' . $q['having'] . ' ' . $q['orderBy'] . ' ' . ( !$all ? 'LIMIT 1' : $q['limit']) );
			$result = $this->run();
			
			return $this->fetch($result, $all);
		}
		
		return false;
	}
	
	public function all()
	{
		return $this->get(true);
	}
	
	public function update()
	{
		$q = $this->queryParams;
		
		if(is_array($q) && !empty($q))
		{
			$this->query = $this->space('UPDATE ' . $q['table'] . ' ' . $q['join'] .  ' SET ' . $q['set'] . ' ' . $q['where'] . ' ' . $q['groupBy'] . ' ' . $q['having'] . ' ' . $q['orderBy'] . ' ' . $q['limit']);
			return $this->run();
		}
		
		return false;
	}
	
	public function delete()
	{
		$q = $this->queryParams;
		
		if(is_array($q) && !empty($q))
		{
			$this->query = $this->space('DELETE FROM ' . $q['table'] . ' ' . $q['join'] .  ' ' . $q['where'] . ' ' . $q['groupBy'] . ' ' . $q['having'] . ' ' . $q['orderBy'] . ' ' . $q['limit']);
			return $this->run();
		}

		return false;
	}
	
	public function query($sql, $params = [])
	{
		if(is_array($params) && !empty($params))
        {
            $x = explode('?', $sql);
            $q = '';
            foreach($x as $k => $v)
                if(!empty($v))
                    $q .= $v . (isset($params[$k]) ? $this->escape($params[$k]) : '');

            $this->query = $q;
        }
		else
			throw new Exception("Opps! You need to enter parameter.");
		
		return $this->run();
	}
	
	private function run()
	{
		if(empty($this->query))
			return false;
		
		$result = $this->pdo->query($this->query);
		if(!$result)
		{
			$this->error = $this->pdo->errorInfo();
			$this->error = $this->error[2];
			return $this->error();
		}
		
		return $result;
	}
	
	private function fetch($result, $all = true)
	{
		return ($all ? $result->fetchAll(\PDO::FETCH_OBJ) : $result->fetch(\PDO::FETCH_OBJ));
	}
	
	private function space($query)
	{
		return preg_replace("/\s+/", " ", trim($query));
	}
	
	private function escape($data)
	{
		$data = trim($data);
		return $this->pdo->quote($data);
	}
	
	private function error()
    {
        $msg = '
		<h1>Database Error</h1>
		<h4>Query: <em style="font-weight:normal;">" '.$this->query.' "</em></h4>
		<h4>Error: <em style="font-weight:normal;">'.$this->error.'</em></h4>';
		die($msg);
    }
}