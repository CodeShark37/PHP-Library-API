# PHP JWT API

```php
public function findFieldsById($fields,$author_id)
    {
        
        $query = 'SELECT ';
        $query .= implode(', ', $fields);
        $query .= 'FROM author WHERE author_id = :author_id';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':author_id', $author_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {  
            $author = new Author()          
            if (!empty($fields[''])) {

            }    
            return $result['author_id']??null, $result['name'], $result['last_name'], $result['country']);
        } else {
            return null;
        }
    }
```